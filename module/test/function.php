<?php

class Test extends Module{

	/*
		处理数据扩展
		$fied		当前扩展属性
		$row		当前数据行
		$data		当前数据集
		$config		当前表单配置
	*/
	public static function parse_value( $fied, $row, $data, $config ){
		switch($fied){
			case "RESULT":		
				if( $data[$fied] == "Y" ){
					return '<span class="text-yes">正确</span>';
				}else{
					return '<span class="text-no">错误</span>';	
				}		
			break;
			case "SCORES":		
				return '<span class="text-yes">'.$data["SCORES"].'</span>';
			break;
		}
	}

}

?>