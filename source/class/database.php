<?php

/*
	系统级数据库维护工具
*/

class Database{

	//获取编码
	function charset(){
		global $_G;
		return str_replace('-', '', $_G['product']['charset']);
	}

	/*
		创建数据表
		$sql		建表语句
		$charset	数据编码
	*/
	function createtable( $sql, $charset ) {
		$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
		$type = in_array($type, array('MyISAM', 'HEAP')) ? $type : 'MyISAM';
		return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$charset" : " TYPE=$type");
	}

	/*
		执行查询
		$sql		查询语句
		$charset	数据编码
	*/
	function query( $sql, $charset = '' ){
	
		//默认编码
		$charset = $charset ? $charset : self :: charset();

		//前辍
		$sql = str_replace( "{TableSysPre}", VI_DBMANPRE, $sql );
		$sql = str_replace( "{TableModPre}", VI_DBMODPRE, $sql );
		
		//参数
		$sql = str_replace( "{TableIP}", GetIP(), $sql );
		$sql = str_replace( "{TableTime}", time(), $sql );
		$sql = str_replace( "{TableBase}", VI_BASE, $sql );
		$sql = str_replace( "{TableSalt}", substr(uniqid(rand()), -6), $sql );
		
		//用户
		$sql = str_replace( "{TableAdmin}", $_SESSION['manager']['account'] ? $_SESSION['manager']['account'] : 'admin', $sql );

		$sql = str_replace("\r", "\n", $sql);
		
		$ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $query) {
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
			}
			$num++;
		}
		unset($sql);
		
		///////////////////////
		
		$succ = array();	//成功
		$fail = array();	//失败
		
		$ERR = 0;
		$MID = 0;

		foreach($ret as $query) {
			$query = trim($query);
			
			if($query) {
			
				if(substr($query, 0, 12) == 'CREATE TABLE') {
					
					$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
					
					System :: $db -> execute( self :: createtable($query, $charset) );
					
					if( mysql_errno() ){
						//$state .= '<h4>创建数据表出错</h4><p class="error">'.mysql_error().'</p>';
						
						array_push($fail,array('message'=>'创建数据表出错','query'=>$query,'error'=>mysql_error()));
						
						$ERR++;
					}else{
						//$state .= '<h4>成功创建数据表</h4><p class="note">'.$query.'</p>';
						
						array_push($succ,array('message'=>'成功创建数据表','query'=>$query));
					}

				} else {
					
					System :: $db -> execute( $query );
					
					if(mysql_errno()){
						//$state .= '<h4>写入数据出错</h4><p class="error">'.mysql_error().'</p>';
						array_push($fail,array('message'=>'执行查询出错','query'=>$query,'error'=>mysql_error()));
						$ERR++;
					}else{
						//$state .= '<h4>成功写入数据</h4><p class="note">'.$query.'</p>';
						array_push($succ,array('message'=>'成功执行查询','query'=>$query));
					}
				}

			}
		}
		
		return array('error'=>$ERR,'succ'=>$succ,'fail'=>$fail);
	}
        
	//////////////////////////////////
		
	//备份基准目录
	function direct( $dir ){
		//return realpath("../../data/$dir/")."/";
		return VI_ROOT.'data/'.$dir.'/';
	}
        
	/*
		导入 SQL 查询
		$file    文件名
	*/
	function import( $file ){
	
		if( !$file ) return FALSE;
		
		ini_set("auto_detect_line_endings", true);
		
		$i = 0;
		$fp = fopen( $file, 'rb' );
		
		while (!feof($fp)) {
			$line = trim( fgets( $fp ) );
			if( !$line ) continue;
			$res = self :: query( self :: detext( $line ) );
			//var_dump( self :: detext( $line ) );
			//echo chr(13);
			//echo '<br />';
			//echo '<br />';
			//echo chr(13);
			$i++;
			//if( $i >= 100 ) return;
		}
		
		fclose($fp);
		
		return $i;
			
	}
	
	function detext( $text ){
		return str_replace( '\\\n', '\n', $text );
	}
	
	function entext( $text ){
		return addslashes( str_replace( array("\r\n", "\r", "\n"), '\n', $text ) );	
	}
	
	function getname( $file ){
	
		$file = self :: direct( 'backup' ) . $file;
		
		$handle = fopen( $file, "r");
		
		//读取第一行
		//$result = fgets( $handle );
		
		//preg_match( "/Name\:(\S*)$/", $result, $match );
		
		//var_dump( $match );
		

		$x = 0;
		
		while ( !feof($handle) && $x < 5 ) {
		  $result = fgets( $handle );
		  $x++;
		}
		
		//$result = fread( $handle, 216 );
		
		preg_match( "/Name\: (.*?)$/", $result, $match );
		
		return $match[1];
		
	}
	
	/*
		导出 SQL 查询
		$file    	文件名
		$prefix		表前辍过滤
		$option		备份选项
	*/
	function export( $name, $prefix = array( VI_DBMANPRE, VI_DBMODPRE, '*' ), $option = array( 'data', 'structure' ) ){
		global $_G;
		
		$i = 0;
		
		if( count( $prefix ) == 0 || count( $option ) == 0 ) return $i;

		//文件名为当天的日期
		$file = self :: direct( 'backup' ). md5( rand() ) .".sql";
		
		$handle = fopen( $file, "w");
		
		$eol = chr(13).chr(10);

		//缓存名称
		$text = '--' . $eol.
				'--	VeryIDE '.$_G['product']['appname'].
				'-- '.$_G['product']['version'] . $eol.
				'--	Host: '.VI_DBHOST . $eol.
				'--	Database: '.VI_DBNAME . $eol.
				'--	Name: '.$name . $eol.
				'--	Prefix: '.join(', ',$prefix) . $eol.
				'--	Option: '.join(', ',$option) . $eol.
				'--	Date: '.date("Y/m/d H:i:s") . $eol.
				'--' . $eol;
		
		if( fwrite($handle, $text) === FALSE ){
			return $i;
		}
		
		//////////////////////////////////
		
		//遍历数据表
		$table = System :: $db -> getAll('show tables');
		
		$item = getall_by_key( $table , 'Tables_in_'.VI_DBNAME );
		
		//var_dump( $table );
		
		$list = array();
		
		if( count( $prefix ) < 3 ){
			foreach( $item as $table ){
				foreach( $prefix as $pre ){
					
					if( $pre == '*' && strpos( $table, VI_DBMANPRE ) !== 0 && strpos( $table, VI_DBMODPRE ) !== 0 ){
						array_push( $list, $table );
						break;
					}
					
					if( $pre != '*' && strpos( $table, $pre ) === 0 ){
						array_push( $list, $table );
						break;
					}
					
				}
			}
		}else{
			$list = $item;
		}
		
		//////////////////////////////////
		
		foreach( $list as $table ){
			
			//备份结构
			if( in_array( 'structure', $option ) ){
			
				$sql = System :: $db -> getOne("show create table `$table`");
				$text = $sql['Create Table'];
				
				//删除自增长记录
				if( !in_array( 'data', $option ) ) $text = preg_replace( '/ AUTO_INCREMENT=(\d+)/i', '', $text );
				
				$text = preg_replace("/([\r\n])/",'',$text);				
				fwrite($handle, $text.";\r\n\r\n");
				
				$i++;
			
			}
			
			//备份数据
			if( in_array( 'data', $option ) ){

				//数据长度
				$size = System :: $db -> getValue("SELECT count(*) FROM `$table`");
				
				//起始长度
				for( $init = 0; $init < $size; $init += 1000 ){				
		
					//遍历字段
					$res = System :: $db -> getAll("SELECT * FROM `$table` limit $init, 1000");
		
					foreach( $res as $data ){
						
						$keys = array_keys($data);     
						//$keys = array_map('addslashes',$keys);  
						$keys = array_map( array('Database','entext'), $keys ); 
						$keys = join('`,`',$keys);     
						$keys = "`".$keys."`";
						
						$vals =array_values($data);     
						$vals = array_map( array('Database','entext'), $vals );     
						$vals = join("','",$vals);     
						$vals ="'".$vals."'";     
						   
						$text ="INSERT INTO `$table`($keys) values($vals);\r\n";
						fwrite($handle, $text);
						
						$i++;
					}     
					$text="\r\n";
					fwrite($handle, $text);
					
					$i++;
				}
			
			}
			
		}
		
		fclose($handle);
		
		return $i;
			
	}        
        
	//////////////////////////////////
	
	
	/*
		安装 SQL 更新
		$file    文件名
	*/
	function update( $file ){
		global $_CACHE;
		
		$base = self :: direct( 'update' );
	
		$sqlfile = $base.$file;
          
		//锁文件
		$lock = str_replace(".sql",".lock",$sqlfile);
		
		if( file_exists($lock) ){
			
			//echo '<div id="state" class="failure">抱歉！安装已经存在。更新于早前已经安装，并在使用中：<span class="text-key">'.$update.'</span></div>';
			return 'locked';
			
		}else{
			
			if( file_exists($sqlfile) ){
				
				//日志开始时间
				$time = time();
			
				$text = sreadfile($sqlfile);
				
				/////////////////////////////
				
				//获取全部参数
				preg_match_all( "/#\[module=(.*?)\](.+?)#\[\/module\]/ism", $text, $match );
								
				//遍历模块
				foreach( $match[1] as $index => $appid ){
					
					//不存在此模块
					if( array_key_exists( $appid, $_CACHE['system']['module']) === FALSE ){
						
						//从更新语句中移除，#[module=appid]...#[/module]
						$text = str_replace( $match[0][$index], '', $text);
					}
					
				}
				
				/////////////////////////////
			
				$res = self :: query( $text );
				
				if( $res['error'] == 0 ){
				
					//写入锁
					create_file( $lock ,date("Y-m-d H:i:s"));
		
					//写入日志
					System :: insert_event($func,$time,time(),"安装更新：".$file);
					
					//搜索模块
					//Module :: search();
					
					//缓存系统用户组
					//Cached :: table( 'system', 'sys:group', array( 'jsonde' => array('config') ) );
					
					//echo '<div id="state">恭喜！成功安装更新：'.$update.'</div>';				
					return 'success';
					
				}else{
					
					//echo '<div id="state" class="failure">抱歉！安装更新失败。以下是本错误信息详细报告：</div>';
					return 'abort';
					
				}
				
			}
			
		}
		
	}
		
}
