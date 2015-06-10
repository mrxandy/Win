<?php

class Pk extends Module{

	/*
		处理数据扩展
		$fied		当前扩展属性
		$row		当前数据行
		$data		当前数据集
		$config		当前表单配置
	*/
	public static function parse_value( $fied, $row, $data, $config ){
		global $_G;
		
		switch($fied){
			
			case "OBJECT":
				return $config[ $data[$fied] ];
			break;
			
			case "MESSAGE":
				return $data[$fied];
			break;
		}
	}

}
