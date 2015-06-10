<?php

define("MYSQL_SQL_GETDATA", 1);
define("MYSQL_SQL_EXECUTE", 2);

class mysql_db{

    var $_server;               //数据库服务器地址
    var $_user;                 //数据库连接帐号
    var $_password;             //数据库连接密码
    var $_dbname;               //数据库名称
    var $_persistency=false;    //是否使用持久连接
    var $_isConnect = false;    //是否已经建立数据库连接
    var $_charset="utf8";       //数据库连接字符集

    var $_isDebug = true;      //是否Debug模式
    
    var $_sql=array();          //执行sql语句数组

    var $_db_connect_id;        //数据库连接对象标识
    var $_result;               //执行查询返回的值
   
    var $_record;				//数据库返回的结果记录
    var $_rowset;				//数据库查询返回的结果集
    var $_errno = 0;			//MYSQL错误类型
    var $_error = "connection error";		//默认错误提示
    var $_checkDB = false;		//是否检测数据库链接

    /**
    * @desc 构造函数
    * @param string 数据库地址
    * @param string 数据库用户名
    * @param string 数据库密码
	* @param string 数据库名
    */
    function mysql_db($dbserver, $dbuser, $dbpassword,$database,$persistency = false,$autoConnect=false,$checkdb = false){
        $this->_server = $dbserver;
        $this->_user = $dbuser;
        $this->_password = $dbpassword;
        $this->_dbname = $database;
        $this->_persistency = $persistency;
        $this->_autoConnect = $autoConnect;
        $this->_checkDB = $checkdb;

        if($autoConnect){
            $this->connection();
        }
    }

    /**
    * @desc 链接数据库
	* @return bool TRUE
    */
    function connection($newLink = false){
        if (!$newLink){
            if($this->_isConnect && isset($this->_db_connect_id)){
                @mysql_close($this->_db_connect_id);
            }
        }
		
        $this->_db_connect_id = ($this->_persistency) ? @mysql_pconnect($this->_server, $this->_user, $this->_password):@mysql_connect($this->_server, $this->_user, $this->_password,$newLink);
       
        if ($this->_db_connect_id)       {
			$dbcharset = str_replace('-', '', $this->_charset);
			
			/*
            if ($this->version() > '4.1')
            {
                if ($this->_charset != "")
                {
                    @mysql_query("SET NAMES '".$dbcharset."'", $this->_db_connect_id);
                }
            }

            if ($this->version() > '5.0')
            {
                @mysql_query("SET sql_mode=''", $this->_db_connect_id);
            }
			*/
			
			$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
			$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
			$serverset && mysql_query("SET $serverset", $this->_db_connect_id);
			

            //检测指定数据库是否连接成功
            if ($this->_checkDB){
                $dbname = mysql_query('SELECT database()',$this->_db_connect_id);
                $dbname = mysql_fetch_array($dbname,MYSQL_NUM);
                $dbname = trim($dbname[0]);
            }else{
                $dbname = '';
            }
            if ($dbname==$this->_dbname || $dbname==''){
                if (!@mysql_select_db($this->_dbname, $this->_db_connect_id))
                {
                    @mysql_close($this->_db_connect_id);
                    $this->_halt("cannot use database " . $this->_dbname);
                }
            }else{
                if ($this->_checkDB && !$newLink){
                    $this->connection(true);
                }
            }
            return true;
        }
        else
        {
            $this->_halt('connect failed.',false);
        }
    }
   
	/**
	* @desc 设置数据库字符集
	* @param string 字符集
	*/
	function setCharset($charset){
		//$charset = str_replace('-', '', $charset);
		$this->_charset = $charset;
	}

	/**
	* @desc 开启调试模式
	* @param bool true：开启  false:关闭
	*/
	function setDebug($isDebug=true){
		$this->_isDebug = $isDebug;
	}
	
	/**
    * @desc 执行数据查询
    * @param string SQL语句
    */
    function query($sql,$type=''){
        return $this->_runSQL($sql,MYSQL_SQL_GETDATA,$type);
    }
   
    /**
    * @desc 执行数据查询,不产生mysql缓存
    * @param string SQL语句
    */
	function execute($sql){
		return $this->_runSQL($sql,MYSQL_SQL_EXECUTE,"UNBUFFERED");
	}
	
	function fixname( $sql ){
		$sql = str_replace( '`sys:', '`'.VI_DBMANPRE, $sql );
		$sql = str_replace( '`mod:', '`'.VI_DBMODPRE, $sql );
		return $sql;	
	}
	
	/**
    * @desc 执行数据查询
    * @param string SQL语句
    */
	function _runSQL($sql,$sqlType=MYSQL_SQL_GETDATA,$type = ''){
		global $_G;
		
		$_G['project']['queries']++;
		
		if ($type =="UNBUFFERED"){
		    $this->_result = @mysql_unbuffered_query( $this->fixname($sql), $this->_db_connect_id );
		}else{
		    $this->_result = @mysql_query( $this->fixname($sql), $this->_db_connect_id );
		}

		//测试模式下保存执行的sql语句
		if($this->_isDebug){
		    $this->_sql[]=$sql;
		}

		if ($this->_result){
		    return $sqlType == MYSQL_SQL_GETDATA ? $this->getNumRows() : $this->getAffectedRows();
		}else{
		    $this->_halt("Invalid SQL: ".$sql);
		    return false;
		}
	}

	/**
	* @desc 判断是否有下一行记录，是否EOF   
	* @return bool TRUE FALSE
	*/
	function next($result_type=MYSQL_ASSOC) {
		$this->fetchRow($result_type); 
		return is_array($this->_record);
	}
   
	/**
	* @desc 获取结果中指定列或索引的值
	* @param string 列名或索引
	* @return  指定列或索引的值
	*/
	function f($name) {
		if(is_array($this->_record)){
		    return $this->_record[$name];
		}else{
		    return false;
		}
	}
   
	/**
	* @desc 获取一行记录   
	* @return array 返回维数为1的数组
	*/
	function fetchRow($result_type=MYSQL_ASSOC){
		if( $this->_result )
		{
		    $this->_record = @mysql_fetch_array($this->_result,$result_type);
		    return $this->_record;
		}else{
		    return false;
		}
	}
   
	/**
	* @desc 执行SQL，获取所有记录集
	* @param string SQL语句
	* @param string 指定的列名
	* @param        
	* @return array SQL所产生的所有结果集
	*/
	function getAll($sql,$primaryKey="",$result_type=MYSQL_ASSOC){
		if ($this->_runSQL($sql,MYSQL_SQL_GETDATA)>=0){

		    return $this->fetchAll($primaryKey,$result_type);
		}else{
		    return false;
		}
	}

	/*
	返回一行数据结题，数组形式
	$sql	查询语句
	*/
	function getOne($sql,$result_type=MYSQL_ASSOC){
		if ($this->_runSQL($sql,MYSQL_SQL_GETDATA)>0){
		    $arr = $this->fetchAll("",$result_type);
		    if(is_array($arr)){
			return $arr[0];
		    }
		}else{
		    return false;
		}
	}
    
	/*
	返回当前查询总数
	$sql	查询语句
	$index  主键
	*/
	function getCount( $sql, $index = 'id' ){
		return $this->getValue( preg_replace( '/select (.+?) from/is', 'select count('.$index.') from', $sql ) );
	}
   
	/**
	* @desc 执行SQL，获取所有记录集
	* @param string 指定的列名
	* @param        
	* @return array 执行SQL所产生的所有结果集
	*/
	function fetchAll($primaryKey = "",$result_type=MYSQL_ASSOC){
		if ($this->_result)
		{
		    $i = 0;
		    $this->_rowset = array();

		    if ($primaryKey=="")
		    {
			while($this->next($result_type))
			{
			    $this->_rowset[$i] = $this->_record;
			    $i++;
			}
		    }else{
			while($this->next($result_type))
			{
			    $this->_rowset[$this->f($primaryKey)] = $this->_record;
			    $i++;
			}
		    }

		    return $this->_rowset;
		}else{
		    //$this->_halt("Invalid Result");
		    return false;
		}
	}
   
	/**
	* @desc 判断是否存在数据
	* @param string SQL语句      
	* @return bool TRUE:是 FALSE:否
	*/ 
	function checkExist($sql){
		return $this->query($sql)>0?true:false;
	}

	/**
	* @desc 得到指定字段的值
	* @param string SQL语句      
	* @return 
	*/
	function getValue($sql, $colset = 0){
		if ($this->query($sql)>0){
		    $this->next(MYSQL_BOTH);
		    return $this->f($colset);
		}else{
		    return false;
		}
	}
   
	/**
	* @desc 得到记录集的总数      
	* @return int 记录集的数量
	*/
	function getNumRows(){
		return @mysql_num_rows($this->_result);
	}
   
	/**
	* @desc 得到结果集中字段的数。
	* @return int 结果集的字段数量
	*/
	function getNumFields(){
		return @mysql_num_fields($this->_result);
	}

	/**
	* @desc 取得结果中指定字段的字段名 
	* @param int 键值，偏移量      
	* @return string 结果中指定字段的字段名 
	*/
	function getFiledName($offset){
		return @mysql_field_name($this->_result, $offset);
	}

	/**
	* @desc 函数返回结果集中指定字段的类型
	* @param int 键值，偏移量      
	* @return string 函数返回结果集中指定字段的类型 
	*/
	function getFiledType($offset){
		return @mysql_field_type($this->_result, $offset);
	}

	/**
	* @desc 取得指定字段的长度
	* @param int 键值，偏移量      
	* @return string 指定字段的长度
	*/
	function getFiledLen($offset){
		return @mysql_field_len($this->_result, $offset);
	}

	/**
	* @desc 取得上一次INSERT动作所产生的ID值 
	* @return int 上一次INSERT动作所产生的ID值 
	*/
	function getInsertId(){
		return @mysql_insert_id($this->_db_connect_id);
	}

	/**
	* @desc 取得上一次增加、删除、修改所影响行的数量
	* @return int 影响行的数量
	*/
	function getAffectedRows(){
		return @mysql_affected_rows($this->_db_connect_id);
	}

	/**
	* @desc 函数释放结果内存。
	* @return bool TRUE：成功 FALSE：失败
	*/
	function free_result(){
		$ret = @mysql_free_result($this->_result);
		$this->_result = 0;
		return $ret;
	}

	/**
	* @desc 取得 MySQL 服务器信息
	* @return string MySQL 服务器信息
	*/
	function version() {
		return @mysql_get_server_info($this->_db_connect_id);
	}

	/**
	* @desc 关闭mysql链接
	* @return bool TRUE：成功 FALSE：失败
	*/
	function close() {
		return @mysql_close($this->_db_connect_id);
	}
   
   /**
    * @desc 显示SQL语句
	* @param bool 开启或关闭
	* @param bool 输出本页面全部执行的SQL或上次执行的SQL 
	* @return bool TRUE：成功 FALSE：失败
    */
    function sqlOutput($isOut = true, $all = true){
        if($all){
            $ret = implode("<br>",$this->_sql);
        }else{
            $ret = $this->_sql[count($this->_sql)-1];
        }
        if ($isOut){
            echo $ret;
        }else{
            return $ret;
        }
    }

   	/**
    * @desc 错误提示
    * @param string SQL语句      
	* @return 
    */
    function _halt($msg="Session halted.",$getErr=true) {
        if($this->_isDebug){
            echo '<div id="error">';
	    echo 'database:'.$this->_dbname.'<br />';
            if($getErr){
                $this->_errno = @mysql_errno($this->_db_connect_id);
                $this->_error = @mysql_error($this->_db_connect_id);
                printf("<b>MySQL _error</b>: %s (%s)<br></font>\n",$this->_errno,$this->_error);
            }
            echo $msg;
            echo '</div>';
            exit;
        }else{
            die("Session halted.");
        }
    }
}
