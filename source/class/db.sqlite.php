<?php

# Sqlite 3 PDO Database Class
# Created by Gideon Graphics Studio
# Date: October 12, 2008
/* 
Description: SQLITE 3 Class designed to intereact with database and output 	results for html, flash and xml.
* Uses Nine Methods:
* Database Interaction Methods
* (1) $object->query() - executes a database query
* (2) $object->fetchobject() - return query result as object
* (3) $object->fetcharray() - returns query results as an array
* (4) $object->fetchnum() - returns query results as column number reference
* (5) $object->checkrowCount() - checks total number of rows in a table
* (6) $object->connect()

* Special Formatting Methods
* (6) $object->showDataAsTable() - outputs results as html tables
* (7) $object->showDataAsURLEncoded() - outputs results as url data
* (8) $object->showDataAsXML() - outputs results as xml
* (9) $object->writeDataToXMLFile() - sends xml data to a filename of your choice.
*/

class sqlitePDO {

	#instantiate variables;
	
	private $dbase;		//数据库名
	private $result;
	private $link;
	private $row;
	public  $ext;
	private $qry;
	
	/**
    * @desc 构造函数
    * @param string 数据库名
	* @param string 
    */
	function __construct($dbase,$ext) {
		$this->dbase = $dbase;
		$this->link	 = $link;
		$this->ext   = $ext;		
	}
	
	/**
    * @desc 链接数据库
    */
	function connect() {
		$this->link = new PDO("sqlite:".$this->dbase.".".$this->ext);
	}
	
	/*
		执行数据查询
		$sql	查询语句
	*/
	#Query sqlite database results.
	
	/**
    * @desc 执行数据库查询语句
	* @param string 数据库查询语句
    */
	function query( $sql ) {				
		$this->result = $this->link->query( $sql );
		$this->result->execute;
	}
	
	#Show result SET as an object
	
	/**
    * @desc 获取执行SELECT获取的结果集，返回对象
	* @return object   example: $row->id
    */
	function fetchobject() {
		$row = $this->result->fetch(PDO::FETCH_OBJ);
		return $row;
	}
	
	#Show result SET as an array
	/**
    * @desc 获取执行SELECT获取的结果集，返回数组
	* @return array   example: $row['id']
    */
	function fetcharray() {
		$row = $this->result->fetch(PDO::FETCH_ASSOC);
		return $row;
	}
	
	#Show result SET as a number
	/**
    * @desc 获取执行SELECT获取的结果集，返回数组
	* @return array   example: $row[0]
    */
	function fetchnum() {
		$row = $this->result->fetch(PDO::FETCH_NUM);
		return $row;
	}
	
	#check total number of rows in database
	
	/**
    * @desc 获取结果集数量
	* @return int
    */
	function checkrowCount() {
		$count = count($this->result->fetchAll());
		return $count;
	}
	
	////////////////////////

	/**
	* @desc 执行SQL，获取所有记录集
	* @param string SQL语句
	* @param string 指定的列名
	* @param        
	* @return array SQL所产生的所有结果集
	*/
	function getAll($sql,$primaryKey="",$result_type=PDO::FETCH_ASSOC){
	
		$this -> query($sql);		
		$row = $this->result->fetchAll($result_type);
	
		return $row;

	}
    
	/*
	返回当前查询总数
	$sql	查询语句
	$index  主键
	*/
	function getCount( $sql, $index = 'id' ){
		return $this->getValue( preg_replace( '/select (.+?) from/is', 'select count('.$index.') from', $sql ) );
	}
	
	/*
	返回一行数据结题，数组形式
	$sql	查询语句
	*/
	function getOne($sql,$result_type=PDO::FETCH_ASSOC){
	
		$this -> query($sql);
		
		if( $this->result ){		
			$row = $this->result->fetchAll($result_type);
			return $row[0];			
		}else{
			return $row;
		}
	}
	
	/**
	* @desc 得到指定字段的值
	* @sql 		string SQL语句      
	* @colset intger   字段索引（从0开始）
	* @return 
	*/
	function getValue($sql, $colset = 0){
		
		$this -> query($sql);		
		$row = $this -> fetchnum();
		
		if ( $row ){
			return $row[$colset];
		}else{
			return false;
		}
	}
	
	/**
	* @desc 取得上一次INSERT动作所产生的ID值 
	* @return int 上一次INSERT动作所产生的ID值 
	*/
	function getInsertId(){
		return $this->link->lastInsertId();
	}
	
	/**
	* @desc 判断是否有下一行记录，是否EOF   
	* @return bool TRUE FALSE
	*/
	function next($result_type=MYSQL_ASSOC) {
		$this->fetchRow($result_type); 
		return is_array($this->_record);
	}
	
	#special formatting functions	
	#---------------------------------------------------------------------
	
	#Show data in html tabulated fashion 
	#takes three parameters (1) Table Header name ($tharr)
	#						(2) Table Cell values ($tdarr);
	#						(3) Table CSS class ($class) - optional
	
	/**
    * @desc 以表格形式显示结果
	* @param array 表字段名,作为表头  array('id','name','age','sex')
	* @param array 表字段名，以对象形式获取记录，并循环显示
	* @param string 表格样式
    */
	function showDataAsTable($tharr, $tdarr, $class = '') {		
		#determine if a table class exists
		if($class == '') {
			echo "<table>";
		}
		else {
			echo '<table class="'.$class.'">';
		}
		
		#list table header values;
		echo '<tr>';
		for ($i=0; $i < count($tharr); $i++) {
			echo "<th>".$tharr[$i]."</th>";
		}
		echo'</tr>';
		
		#list table cell values;
		while($row = $this->fetchobject()) {
			echo "<tr>";
			for ($i=0; $i < count($tdarr); $i++) {
				echo "<td>".$row->$tdarr[$i]."</td>";				
			}
			echo "</tr>";
		}		
		echo "</table>";
	}
	#---------------------------------------------------------------------
	
	#Show data in a url encoded fashion (flash datafeed)
	#takes three parameters (1) name of variable to store values ($varname)
	#						(2> database row title ($titlearr)
	#						(3) database row value ($titleval)
	
	/**
    * @desc 将读取到的结果组成URL参数
	* @param string 对应URL参数名    http://aa.com/a.php?dbext={此为组合的数据}
	* @param array 表字段名，此为需要组合的字段名
	* @param array 表字段名，以对象形式获取记录，并循环显示
    */
	function showDataAsURLEncoded($varname, $rowtitle, $rowval) {	
		echo "{$varname}=";
		while($row = $this->fetchobject()) {
			for($i=0; $i < count($rowtitle); $i++) {
				echo urlencode("&{$rowtitle[$i]}=".$row->$rowval[$i]); 
			}
			
		}		
	}
	
	#---------------------------------------------------------------------
	
	#Creates xml formatted data
	#takes four parameters (1) opening and closing outer xml tags to wrap data ($tag)
	#						(2) inner xml tags to wrap data
	#						(3) database row title ($titlearr)
	#						(4) database row value ($titleval)
	
	/**
    * @desc 以XML文档显示获得的结果
	* @param string 对象标签名
	* @param string 子标签名
	* @param array 表字段名，以对象形式获取记录，并循环显示
    */
	function showDataAsXML($otag, $itag, $rowtitle, $rowval) {	
		$str = "<".$otag."> \n";
		while($row = $this->fetchobject()) {
			$str .= " \t<".$itag."> \n";
			
			for($i=0; $i < count($rowval); $i++) {
				$str.= "\t \t<".$rowtitle[$i].">".$row->$rowval[$i]."</".$rowtitle[$i]."> \n";
			}
			$str.="\t</".$itag.">\n";
		}
		$str .= "</".$otag.">";
		$xml = simplexml_load_string($str);
		echo $xml->asXML();
	}
	
	#---------------------------------------------------------------------
	# Sends xml formatted database information to a file
	# takes two parameters file path and filename
	
	/**
    * @desc 将结果集输出XML文件
	* @param string 对象标签名,输出的文件名
	* @param string 子标签名
	* @param array 表字段名，以对象形式获取记录，并循环显示
    */
	function writeDataToXMLFile($otag, $itag, $rowtitle, $rowval) {
		$str = "<".$otag."> \n";
		while($row = $this->fetchobject()) {
			$str .= " \t<".$itag."> \n";
			
			for($i=0; $i < count($rowval); $i++) {
				$str.= "\t \t<".$rowtitle[$i].">".$row->$rowval[$i]."</".$rowtitle[$i]."> \n";
			}
			$str.="\t</".$itag.">\n";
		}
		$str .= "</".$otag.">";
		$xml = simplexml_load_string($str);
		
		$data = $xml->asXML();
		$fh = fopen("{$otag}.xml", "at");
		fwrite($fh, $data);
		fclose($fh);
	}
}
