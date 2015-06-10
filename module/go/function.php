<?php

class Go extends Module{

	//生成统计链接
	public static function getlink($method,$id){
		global $_G;
		return VI_HOST."module/go/content/?id=".$id;	
	}

	//转向目标链接
	public static function click( $link ){
		global $_G;
		
		if( !$link ) return;
		
		//清空缓冲
		ob_clean();
		
		//分析链接
		preg_match("/^(.+?):\/\/(.*)/i", $link, $matche );
		
		switch( $matche[1] ){
		
			case 'qq':
				$link = 'tencent://message/?site=veryide&menu=yes&uin='. $matche[2];
			break;
		
			case 'mail':
				$link = 'mailto:'. $matche[2];
			break;
		
			case 'msn':
				$link = 'msnim:chat?contact='. $matche[2];
			break;
		
			case 'ww':
				$link = 'http://amos1.taobao.com/msg.aw?site=cntaobao&charset=utf-8&v=2&uid='. $matche[2];
			break;
			
		}
		
		//redirect
		if( $matche[1] == 'http' ){
			header("Location: ".$link);
		}else{
			exit( '<script type="text/javascript">location.href=\''.$link.'\';</script>' );
		}
		
	}
	
	/////////////////////////////////	
	
	/*
		写入统计
		$app		模块ID
		$ver		版本号
		$index		链接索引
		$title		链接标题
	*/
	public static function link_click( $app, $ver, $index, $title ){
		global $_G;
		
		//加载模块配置及函数
		parent::loader( strtolower( __CLASS__ ) );
		
		//如果参数错误
		if( !$app || !$ver || !is_int( $index ) ){
			return false;
		}
		
		//日期
		$date = array('Y'=> date("Y") ,'M'=> date("Y-m") ,'D'=> date("Y-m-d") ,'H'=> date("H"));
		
		//查询语句
		$sql = array();
		
		$sql['UPDATE'] = "UPDATE `mod:go_click` SET click=click+1 WHERE app='".$app."' and ver='".$ver."' and `index`='".$index."' and cate ='{CATE}' and `date`='{DATE}'";
		
		$sql['INSERT'] = "INSERT INTO `mod:go_click`(app,ver,`index`,cate,date,click) VALUES('".$app."','".$ver."','".$index."','{CATE}','{DATE}',1)";
			
		foreach( $date as $key => $val ){
			
			$UPDATE = str_replace(array('{CATE}','{DATE}'),array($key,$val),$sql['UPDATE']);
			$INSERT = str_replace(array('{CATE}','{DATE}'),array($key,$val),$sql['INSERT']);
			
			//更新数据
			System :: $db -> execute( $UPDATE );
			
			//没有记录
			if( System :: $db -> getAffectedRows() == 0 ){
				
				//创建数据
				System :: $db -> execute( $INSERT );				
				
			}
			
		}
		
		return '<!--ping:'.$do.' key:'.$appkey.' status:ok-->';
		
	}
	
	/*
		输出统计
		$param		参数列表
	*/
	public static function link_count( $param ){	
		
		//加载模块配置及函数
		parent::loader( strtolower( __CLASS__ ) );
		
		//如果参数错误
		if( !is_array( $param ) ){
			return false;
		}
		
		//统计点击
		$sql = "SELECT sum(click) as count FROM `mod:go_click` WHERE 1=1";
		
		foreach( $param as $key => $val ){
			$sql .= " and `$key` = '". $val ."'";
		}
		
		return intval( System :: $db -> getValue( $sql ) );
		
	}
	
	/*
		统计链接
		$app		模块ID
		$link		链接地址
		$title		链接标题
	*/
	public static function link_stat( $app, $link, $title ){
		global $_G;
		
		//加载模块配置及函数
		parent::loader( strtolower( __CLASS__ ) );
		
		//如果参数错误
		if( !$app || !$link || !$title ){
			return false;
		}
		
		//取16位hash
		$hash = substr( md5( $link ) , 8, 16 );
		
		//查询语句
		$sql['UPDATE'] = "UPDATE `mod:go_stat` SET click=click+1, title='". $title ."', final='". time() ."' WHERE app='".$app."' and hash='".$hash."'";
		
		$sql['INSERT'] = "INSERT INTO `mod:go_stat`(hash,app,`link`,title,start,final,click) VALUES('".$hash."','".$app."','".$link."','".$title."','". time() ."','". time() ."',1)";
			
		//更新数据
		System :: $db -> execute( $sql['UPDATE'] );
		
		//没有记录
		if( System :: $db -> getAffectedRows() == 0 ){
			
			//创建数据
			System :: $db -> execute( $sql['INSERT'] );				
			
		}
		
		return '<!--ping:'.$do.' key:'.$appkey.' status:ok-->';
		
	}
	
	/////////////////////////////////	
	
	/*
		计划任务
	*/
	public static function link_cron(){
		global $_G;
		
		//验证邮件配置
		if( !System :: validmail() ) return FALSE;
		
		//加载模块配置及函数
		parent::loader( strtolower( __CLASS__ ) );
		
		//不需要接收报表
		if( !$_G['setting']['go']['subject'] || !$_G['setting']['go']['receive'] ) return FALSE;
		
		/////////////////////////////////////////////
		
		//$appid = self :: get_appid();
		$appid = 'go';
		
		$based = VI_ROOT.'cache/compile/'.$appid.'/';
		
		$locked = $based . date("Y-m-d") . '.htm';
		
		if( file_exists( $locked ) ){
			return 'exist';
		}
		
		/////////////////////////////////////////////
		
		//加载 Smarty 类
		require VI_ROOT.'source/smarty/Smarty.class.php';
		
		$smarty = new Smarty;
		
		//模板路径
		$smarty->template_dir = VI_ROOT.'module/'.$appid.'/content/';
		
		//编译路径
		$smarty->compile_dir = create_dir( $based );
		
		$smarty->compile_check = true;
		
		$smarty->debugging = false;
		
		$data = array();
		
		////////////////////// 本周概况 //////////////////////
		
		$total = array();
		
		for( $i = 0; $i < 7; $i++ ){
			
			//本周
			$time = strtotime("last Monday +$i day");
			$date = date("Y-m-d", $time );
			$stat = Analytic :: link_count( array( 'cate' => 'D', 'date' => $date ) );
			
			//上周
			$diff = Analytic :: link_count( array( 'cate' => 'D', 'date' => date("Y-m-d", strtotime("last Monday +". ( $i - 7 ) ." day") ) ) );
			//$diff = 1;
			
			//统计
			$data['week'][ $date ] = array( 'week' => $_G['project']['weeks'][date("w", $time )], 'value' => $stat, 'value' => $stat, 'ratio' => 0, 'diff' => ( $diff ? number_format( ( $stat - $diff ) / $diff, 1 ) * 100 : '100' ) );
			
			//本周全部
			$total['week'][] = $stat;
			
			//上周全部
			$total['diff'] = $diff + intval( $total['diff'] );
			
		}
		
		//计算高低值以及百分比
		$_min = min( $total['week'] );
		$_max = max( $total['week'] );
		$data['week_total'] = array_sum( $total['week'] );
		$data['week_diff'] = $total['diff'] ? number_format( ( $data['week_total'] - $total['diff'] ) / $total['diff'], 1 ) * 100 : '';
		
		foreach( $data['week'] as $date => $item ){
			$data['week'][ $date ][ 'ratio' ] = ( $data['week_total'] ? number_format( ( $item['value'] / $data['week_total'] ), 1 ) * 100 : '100' );
			$data['week'][ $date ][ 'ismin' ] = ( $item['value'] == $_min ? true : false );
			$data['week'][ $date ][ 'ismax' ] = ( $item['value'] == $_max ? true : false );
			$data['week'][ $date ][ 'holiday' ] = self :: holiday( $date );
		}
		
		////////////////////// 频道概况 //////////////////////
		
		$sql = "SELECT app FROM `mod:go_click` group by app";
		
		$list = System :: $db -> getAll( $sql, 'app' );
		
		//var_dump( $list );
		
		foreach( $list as $app => $row ){
			
			for( $i = 0; $i < 7; $i++ ){
			
				//本周
				$time = strtotime("last Monday +$i day");
				$date = date("Y-m-d", $time );
				$stat = Analytic :: link_count( array( 'app' => $app, 'cate' => 'D', 'date' => $date ) );
				$data['cate'][ $app ]['week'][ date("N", $time ) ] = array( 'value' => $stat );
				$data['cate'][ $app ]['list'][] = $stat;
				
				//上周
				$data['cate'][ $app ]['last'][ date("N", $time ) ] = Analytic :: link_count( array( 'app' => $app, 'cate' => 'D', 'date' => date("Y-m-d", strtotime("last Monday +". ( $i - 7 ) ." day") ) ) );
				//$data['cate'][ $app ]['last'][ date("N", $time ) ] = 1;
			}
			
		}
		
		foreach( $data['cate'] as $app => $row ){
			
			$_min = min( $data['cate'][ $app ]['list'] );
			$_max = max( $data['cate'][ $app ]['list'] );
			$_sat = array_sum( $data['cate'][ $app ]['list'] );
			$_lat = array_sum( $data['cate'][ $app ]['last'] );
			//var_dump( $_sat );
			//var_dump( $_lat );
			//exit;
			
			foreach( $row['week'] as $date => $item ){
				$data['cate'][ $app ]['week'][ $date ][ 'ratio' ] = ( $_sat ? number_format( ( $item['value'] / $_sat ), 1 ) * 100 : '100' );
				$data['cate'][ $app ]['week'][ $date ][ 'ismin' ] = ( $item['value'] == $_min ? true : false );
				$data['cate'][ $app ]['week'][ $date ][ 'ismax' ] = ( $item['value'] == $_max ? true : false );
				
				//本周总数
				(int) $data['cate'][ $app ]['stat'] += $item['value'];
				
				//上周总数
				(int) $data['cate'][ $app ]['diff'] = ( $_lat ? number_format( ( $_sat - $_lat ) / $_lat, 1 ) * 100 : '100' );
			}
			
			/*
			$data['cate'][ $app ][ $date ][ 'ratio' ] = ( $data['week_total'] ? 100 * ( $item['value'] / $data['week_total'] ) : '100' );
			$data['cate'][ $app ][ $date ][ 'ismin' ] = ( $item['value'] == $_min ? true : false );
			$data['cate'][ $app ][ $date ][ 'ismax' ] = ( $item['value'] == $_max ? true : false );
			*/
		}
		
		
		////////////////////// 本周热门 //////////////////////
		
		//本周
		$start = strtotime("last Monday");
		$final = strtotime("Sunday");
		
		foreach( $list as $app => $row ){
			
			//echo date( 'Y-m-d', $start );
			//echo date( 'Y-m-d', $final );
			
			$sql = "SELECT title,link,start,final,click FROM `mod:go_stat` WHERE app='".$app."' and ( start >= $start and final <= $final ) order by click desc limit 0,10";
			
			//var_dump( $sql );
			
			//上周
			$data['hots'][ $app ] = System :: $db -> getAll( $sql );
			
		}
		
		////////////////////// 最近走势 //////////////////////
		
		//今天至30天以前
		$start = strtotime("-30 day");
		$final = time();
		
		foreach( $list as $app => $row ){
			
			//echo date( 'Y-m-d', $start );
			//echo date( 'Y-m-d', $final );
			
			//上周
			$data['trend'][ $app ] = array();
			
			$temp = array();
			
			$fish = null;
			
			for( $time = $start; $time < $final; $time += 86400 ){
			
				$sql = "SELECT sum(click) FROM `mod:go_click` WHERE app='".$app."' and ( date = '". date("Y-m-d",$time) ."' and cate = 'D' )";
				$stat = intval(System :: $db -> getValue( $sql ));
				
				$data['trend'][ $app ][ date("Y-m-d",$time) ] = array( 'value' => $stat, 'weekend' => ( date("w", $time ) == 0 | date("w", $time ) == 6 ? true : false ) );
				$temp[] = $stat;
				
				$fish = $fish ? $fish : date("Y-m-d",$time);
			
			}
			
			$ismin = min( $temp );
			$ismax = max( $temp );
			
			foreach( $data['trend'][ $app ] as $date => $item ){
				$data['trend'][ $app ][ $date ]['ismin'] = ( $item['value'] == $ismin ? true : false );
				$data['trend'][ $app ][ $date ]['ismax'] = ( $item['value'] == $ismax ? true : false );
				$data['trend'][ $app ][ $date ]['ratio'] = ( intval( $item['value'] / $ismax * 100 ) );
				$data['trend'][ $app ][ $date ]['label'] = ( substr( $date, 8 ) == 01 || $fish == $date ? substr( $date, 5, 2 ) : false );
			}
			
			//var_dump( $sql );
			
			//上周
			//$data['trend'][ $app ] = System :: $db -> getAll( $sql );
			
		}
		
		////////////////////// 输出数据 //////////////////////
		
		//系统变量
		$smarty->assign("data",$data);

		//随机颜色函数
		$smarty->register_function('color',"rand_color");

		$text = $smarty->fetch('template.htm');
		
		//var_dump( $data['trend'] );
		//exit;
		
		$subt = str_replace( array('$DATE','$WEEK'), array( date('Y/m/d'), date('Y/m/d',$start).' - '.date('Y/m/d',$final) ), $_G['setting']['go']['subject'] );
				
		System :: sendmail( $subt, $_G['setting']['go']['receive'], $_G['setting']['go']['copyto'], $text );
		
		//exit($text);
		
		//写入文件锁
		create_file( $locked, $text );
		
		return 'done';

		
	}
	
	/*
		节日信息
	*/
	public static function holiday( $date ){
		
		//载入 日历转换 类
		require_once VI_ROOT.'source/class/lunar.php';
		
		$res = array();
		
		////////////////////////////////////////
		
		//农历转换类
		$lunar = new Lunar();
		
		//农历节日
		$holiday = array(
			'01-01'=>'春节',
			'01-15'=>'元宵节',
			'02-02'=>'二月二',
			'05-05'=>'端午节',
			'07-07'=>'七夕节',
			'08-15'=>'中秋节',
			'09-09'=>'重阳节',
			'12-08'=>'腊八节',
			'12-23'=>'小年'
		);
		
		/*
		var_dump( $date );
		var_dump( $lunar->getLar( $date, 0 ) );
		var_dump( date( 'Y-m-d', $lunar->getLar( $date, 0 ) ) );
		var_dump( substr( date( 'Y-m-d', $lunar->getLar( $date, 0 ) ), 5 ) );
		exit;
		*/
		
		//公历转农历，并截取月份的日期
		$days = substr( date( 'Y-m-d', $lunar->getLar( $date, 0 ) ), 5 );
		
		if( isset( $holiday[ $days ] ) ){
			$res[] = $holiday[ $days ];
		}
		
		////////////////////////////////////////
		
		$days = substr( $date, 5 );
	
		//公历节日
		$holiday = array(
			'01-01'=>'元旦',
			'02-02'=>'世界湿地日(1996)',
			'02-14'=>'情人节',
			'03-03'=>'全国爱耳日',
			'03-08'=>'妇女节(1910)',
			'03-12'=>'植树节(1979)',
			'03-15'=>'国际消费者权益日',
			'03-20'=>'世界睡眠日',
			'03-25'=>'世界气象日',
			'04-01'=>'愚人节',
			'04-07'=>'世界卫生日',
			'05-01'=>'国际劳动节',
			'05-04'=>'中国青年节',
			'05-08'=>'世界红十字日',
			'05-12'=>'国际护士节',
			'05-19'=>'全国助残日',
			'06-01'=>'国际儿童节',
			'06-05'=>'世界环境日',
			'06-22'=>'中国儿童慈善活动日',
			'06-23'=>'国际奥林匹克日',
			'07-01'=>'中国共产党成立(1921)',
			'07-07'=>'中国人民抗日战争纪念日',
			'08-01'=>'中国人民解放军建军(1927)',
			'09-03'=>'抗日战争胜利纪念日(1945)',
			'09-08'=>'国际扫盲日',
			'09-10'=>'教师节',
			'09-16'=>'世界臭氧层保护日',
			'09-18'=>'九一八纪念日',
			'09-27'=>'世界旅游日',
			'09-29'=>'国际聋人节',
			'10-01'=>'国庆节',
			'10-14'=>'世界标准日',
			'10-24'=>'联合国日',
			'12-05'=>'国际志愿人员日',
			'12-29'=>'12.9运动纪念日',
			'12-25'=>'圣诞节'
		);
		
		if( isset( $holiday[ $days ] ) ){
			$res[] = $holiday[ $days ];
		}
		
		return implode( '，', $res );
	
	}
	
	/*
		写入统计
		$app		模块ID
		$ver		版本号
		$index		链接索引
		$title		链接标题
	*/
	public static function uin_write( $number, $nickname, $headface, $origin, $keyword ){
		global $_G;
		
		//加载模块配置及函数
		parent::loader( strtolower( __CLASS__ ) );
		
		//如果参数错误
		if( !$number || !$nickname || !$headface ){
			return false;
		}
		
		//更新数据
		System :: $db -> execute( "UPDATE `mod:go_number` SET visits=visits+1,`headface`='".$headface."',lifetime=".time().",ip='".GetIP()."' WHERE qq='".$number."'" );
		
		//var_dump( $number, System :: $db -> getAffectedRows() );
		
		//没有记录
		if( System :: $db -> getAffectedRows() == 0 ){
			
			//创建数据
			System :: $db -> execute( "INSERT INTO `mod:go_number`(qq,nickname,headface,origin,keyword,dateline,lifetime,visits,state,ip) VALUES('".$number."','".$nickname."','".$headface."','".$origin."','".$keyword."',".time().",".time().",1,1,'".GetIP()."')" );				
			
		}
		
		return '<!--ping:'.$qq.' nickname:'.$nickname.' status:ok-->';
		
	}
	
	/*
		写入统计
		$app		模块ID
		$ver		版本号
		$index		链接索引
		$title		链接标题
	*/
	public static function uin_update( $number ){
		global $_G;
		
		//如果参数错误
		if( !$number ){
			return false;
		}
			
		//创建数据
		System :: $db -> execute( "UPDATE `mod:go_number` SET lifetime=".time().",ip='".GetIP()."' WHERE qq='".$number."'" );				
		
		return '<!--ping:'.$qq.' nickname:'.$nickname.' status:ok-->';
		
	}
}
