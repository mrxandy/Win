<?php
/*
	*PHP获取和设置XML节点,用于修改和读取站点配置文件
	*2008-4-3 
	*LIQUAN
	*eg.get config
	
	*$c = new Configuration('config.xml');
	*echo( $c->TemplateDirectory." " );
	*
	* SET config
	* $c = new Configuration('config.xml');
	* $c->TemplateDirectory='test';
	* $c->save();
*/

class Configuration{
	private $configFile;
	private $charset;
	private $items=array();

	//构造函数
	function __construct($configFile,$charset=""){
		$this->configFile=$configFile;
		$this->charset=$charset;
		$this->parse();
	} 

	//获取属性
	function __get($id){
		return $this->items[$id];
	}

	//设置属性
	function __set($key,$value){
		//if($this->charset){
		//	$this->items[$key]=iconv('utf-8',$this->charset, $value);
		//}else{
			$this->items[$key]=$value;
		//}
	} 

	//获取所有属性
	function __getAll(){
		$array=array();
		foreach($this->items as $key =>$value)
		{
			 $array[$key] = $value;
		}
		return $array;
	} 

	//解析XML文件保存到数组
	function parse(){
		$doc=new DOMDocument();
		$doc->load($this->configFile);
		$cn=$doc->getElementsByTagName('config');
		$nodes=$cn->item(0)->getElementsByTagName('*');
		foreach($nodes as $node)
		{
			if($this->charset){
				$this->items[$node->nodeName]=iconv('utf-8',$this->charset, $node->nodeValue);
			}else{
				$this->items[$node->nodeName]=$node->nodeValue;
			}
		}
	}

	//保存XML文件
	function save(){
		$doc=new DOMDocument();
		   $doc->formatOutput=true;

		$r=$doc->createElement('config');
		   $doc->appendChild($r);
		   $doc->encoding ='utf-8';

		foreach($this->items as $k=>$v)
		{
			$keyName=$doc->createElement($k);

			//转编码
			if($this->charset){
				$v=iconv($this->charset,'utf-8', $v);
			}

			$keyName->appendChild($doc->createTextNode($v));
			$r->appendChild($keyName);
		}
		copy($this->configFile,$this->configFile.".bak");

		$doc->save($this->configFile);
	}

}
