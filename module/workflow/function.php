<?php

/*
	订单及支付整合工具
	仅支持支付宝及时到账接口
*/
class Workflow{

	//短信发送结果
	public $result = null;

	/*
		构造函数
		加载支付宝函数包和配置
	*/
	function __construct( ){
		global $_G;
		
		//加载设置
		require_once VI_ROOT.'module/sms/config.php';
		require_once VI_ROOT.'module/sms/setting.php';
		
	}

	/*
		发送短信
		$appid		模块ID
		$phone		号码列表
		$content		短信内容		
		$field		扩展字段
	*/
	function thread_url( $thread ){
		global $_G;		
		
		return str_replace( array('$TID','$PID'), array($thread['tid'],$thread['pid']), $thread['first'] ? $_G['setting']['workflow']['thread'] : $_G['setting']['workflow']['reply'] );
	
	}
	
	/*
		计算内容工资
		$thread			主题量
		$replys			回帖量
		$content_pay	内容工资
	*/
	function content_bills( $thread, $replys, $content_pay, $content_multi ){
		global $_G;
				
		if( $thread >= $_G['setting']['workflow']['thread_radix'] * $content_multi && $replys >= $_G['setting']['workflow']['reply_radix'] * $content_multi ){
			return $content_pay;
		}else{
			return ( ( $thread / ( $_G['setting']['workflow']['thread_radix'] * $content_multi ) ) + ( $replys / ( $_G['setting']['workflow']['reply_radix'] * $content_multi ) ) ) * $content_pay / 2;
		}
		
	}
	
	/*
		计算出勤工资
		$month_days	本月天数
		$work_days	上班天数
		$work_pay	全勤工资
	*/
	function general_bills( $month_days, $work_days, $work_pay ){
		global $_G;
				
		if( $work_days >= $month_days ){
			return $work_pay;
		}else{
			return $work_pay / $month_days * $work_days;
		}
		
	}	

	/*
		计算迟到扣款
		$month_days	本月天数
		$work_days	上班天数
		$work_pay	全勤工资
	*/
	function late_bills( $work_late ){
		global $_G;
				
		if( $work_late > 0 ){
			return -$_G['setting']['workflow']['late_price'] * $work_late;
		}else{
			return 0;
		}		
		
	}	

}

?>