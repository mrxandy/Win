<?php

/********** Func **********/

class Analytic extends Module {
	
	/*
		写入统计
		$appid		模块ID
		$id			记录ID
		$do			方法（ clicks | views）
		$func		回调函数
	*/
	public static function ping( $appid, $id, $do, $func = null ){
		global $_G;
		
		//加载模块配置及函数
		parent::loader( strtolower( __CLASS__ ) );
		
		//如果统计已半闭
		if( $_G['setting']['analytic'][$do] == 'off'  ){
			return false;
		}	
		
		//如果参数错误或正在管理员身份登录
		if( !$appid || !$id || !$do ){
			return false;
		}
		
		//统计标识
		$appkey = $appid."-".$id;
		
		//数据类型
		$data = ($do=='click' || $do=='clicks'?'clicks':'views');
		
		//日期
		$date = array('Y'=> date("Y") ,'M'=> date("Y-m") ,'D'=> date("Y-m-d") ,'H'=> date("H"));
		
		//查询语句
		$sql = array();
		$sql['UPDATE'] = "UPDATE `mod:common_analytic` SET ".$data."=".$data."+1 WHERE appkey='".$appkey."' and category ='{CATE}' and `date`='{DATE}' LIMIT 1";
		$sql['INSERT'] = "INSERT INTO `mod:common_analytic`(appkey,category,date,".$data.") VALUES('".$appkey."','{CATE}','{DATE}',1)";
		
		$cookie = $appid."-".$data."-".$id;
		
		if( !$_SESSION[$cookie] ){
			
			foreach( $date as $key => $val ){
				
				$UPDATE = str_replace(array('{CATE}','{DATE}'),array($key,$val),$sql['UPDATE']);
				$insert = str_replace(array('{CATE}','{DATE}'),array($key,$val),$sql['INSERT']);
				
				//更新数据
				System :: $db -> execute( $UPDATE );
				
				//没有记录
				if( System :: $db -> getAffectedRows() == 0 ){
					
					//创建数据
					System :: $db -> execute( $insert );				
					
				}
				
			}
			
			//写入Cookie
			$_SESSION[$cookie] = "Y";
			
			//回调函数
			if( $func ){
				call_user_func( $func, $id, $do );
			}
			
			return '<!--ping:'.$do.' key:'.$appkey.' status:ok-->';
			
		}else{
			return '<!--ping:'.$do.' key:'.$appkey.' status:no-->';	
		}
		
	}
	
	/*
		输出统计
		$appid		模块ID
		$id			记录ID
		$do			方法（ clicks | views）
	*/
	public static function count( $appid, $id, $do ){	
		
		if( !$appid || !$id || !$do ){
			return false;
		}
		
		//统计标识
		$appkey = $appid."-".$id;
		
		//数据类型
		$data = ($do=='click'?'clicks':'views');
		
		//日期
		$date = array('Y'=> date("Y") ,'M'=> date("Y-m") ,'D'=> date("Y-m-d") ,'H'=> date("H"));
		
		//查询语句
		$sql = "SELECT sum(".$data.") as count FROM `mod:common_analytic` WHERE appkey='".$appkey."' and category = 'Y' ";
		
		return intval(System :: $db -> getValue( $sql ));
		
	}
	
	/*
		清空统计
		$appid		模块ID
		$list		记录ID
	*/
	public static function clear( $appid, $list ){
		
		if( !$appid || !$list ){
			return false;
		}
		
		$array = explode(",",$list);
		
		foreach( $array as $id ){
		
			//统计标识
			$appkey = $appid."-".$id;
			
			//查询语句
			$sql = "DELETE FROM `mod:common_analytic` WHERE appkey='".$appkey."'";
			System :: $db -> execute( $sql );
		
		}
		
	}
		
}
