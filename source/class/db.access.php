<?php
/*
  类名: PHP ACCESS 数据处理、分页通用类

  作者：月影
  版本：1.0
  时间：2007-9-1
  Email:zhangxugg@163.com

   用法范例：

   1.查询记录并生成分页链接
   $dbc=new db("data.mdb"); 				//构造一个ACCESS处理类，ACCESS文件名为data.mdb
   $dbc->PageSize=16;						//每页显示16条记录，即页大小
   $dbc->PageStr="page";					//设置分页符为 page ,比方URL是 http://ww.abc.com/show.php?page=2
   $dbc->LinkStyle="DbLink";				//分页超链接CSS样式名 ，注意，是CSS类名

   $dbc->query(""SELECT * FROM info);		//查询info 数据表 info并返回一个Recordset对象

   //移动游标，列举字段值
   for($i=1;$i<=$dbc->PageSize;$i++){
   	echo GetValue("id");
   	$dbc->MoveNext();
   }

注意：不需要调用 $dbc->close(),此功能已经在析构函数中实现

	2.删除记录：
	与上面类似，不过语句更少
	$dbc=new db("data.mdb");
	$dbc->query("DELETE FROM info WHERE id=2");

 3.增加数据：
 	$dbc=new db("data.mdb");
 	$dbc->query("INSERT INTO info(id,user_name,addr,email) values($id,'$user_name','$addr,$email')");
 	目前暂时不支持 INSERT INTO info SET id=$id,user_name='$user_name' 形式的SQL语句

*/

class access{
	
	private $isRsOpen;		
	private $FieldsArray;			//执行SELECT语句后，返回的结果集
	private $rsArray;
	private $rsQueryNum=0;
	private $sql;
	private $conn;					//数据库链接RESOURCE	
	private $RecordCount=0;
	private $connStr;				//数据库链接句柄
	private $PageCount=0;			//总页数
	private $rs;					//数据库结果集控件

	public $PageSize=10;			//每页显示数量
	public $PageStr='page';			//URL代表分页的参数
	public $LinkStyle='LinkStyle';	//分页样式
	public $PageStyle='PageStyle';	//分页页码样式

	/**
    * @desc 构造函数
    * @param string ACCESS数据库文件路径
    */
	function __construct($mdb_file){
		 $this->isRsOpen=false;
		 $this->FieldsArray=array();
		 $this->rsArray=array();
		$this->conn=new COM('ADODB.Connection') or die('create ADODB.Connection Error');
		$this->rs=  new COM("ADODB.Recordset") or die('create ADODB.Recordset Error');
		$this->connStr='DRIVER={Microsoft Access Driver (*.mdb)};DBQ='.realpath($mdb_file);
		//$this->rs->CursorLocation=1;
		$this->conn->Open($this->connStr);
	}

	/**
    * @desc 执行SQL语句
    * @param string SQL查询语句
    */
	function query( $sql ){
		$sql=iconv('utf-8','GBK',$sql);
		$this->sql=$sql;
		if(preg_match("/(insert|delete|update)/i",$sql)){
   			$this->conn->execute($sql);
   			return ;
		}else{
			($this->isRsOpen) && ($this->rs->Close());
			$this->rs->Open($sql,$this->conn,1,3);
			 $this->isRsOpen=TRUE;
			 for($i=0;$i<$this->rs->Fields->Count;$i++){
			 	$this->FieldsArray[$i]=$this->rs->Fields[$i]->name;
			 }
		}

        $this->rs->PageSize=$this->PageSize;
		$this->RecordCount=$this->rs->RecordCount;
		if($this->RecordCount<1)	return;
		$this->PageCount=$this->rs->PageCount;
		$this->AbsolutePage=$this->rs->AbsolutePage=$this->GetCurrentPage($this->PageStr);
	}
	
	/**
    * @desc 获取查询结果
    * @return array 
    */
	function getRecord(){
       if($this->rs->EOF || $this->rs->BOF || $this->rsQueryNum>=$this->PageSize) return NULL;
       for($i=0;$i<$this->rs->Fields->Count;$i++){
			$this->rsArray[$this->FieldsArray[$i]]=iconv('GBK','utf-8',$this->rs->Fields[$i]->value);
       }
      $this->rsQueryNum++;
	  $this->rs->MoveNext();
      return $this->rsArray;
	}

	/**
    * @desc 分页
	* @param int 默认每页显示数量
	* @param string 
	* @param string 分页链接 
    */
	function link($NumPerPage=10,$extraStr='',$PagePre=NULL){
		GLOBAL $PHP_SELF;
		$CurrentPage=$this->GetCurrentPage($this->PageStr);
		$prefix=($PagePre)?$PagePre:$PHP_SELF.'?'.$extraStr.'&'.$this->PageStr.'=';

		if($this->PageCount<=1){
			return ;
		}

		if($this->PageCount>1 ){
			echo "<div class='$this->LinkStyle'><a  title='到第一页' href={$prefix}1> << </a>\r\n";
		}

		$start=max($CurrentPage-intval($NumPerPage/2),1);
		$end=min($start+$NumPerPage-1,$this->PageCount);

		for($p=$start;$p<=$end;$p++){
         if($p==$CurrentPage){
         	echo("<span class=$this->PageStyle>$p</span>");
         	continue ;
         }
         echo("<a href=$prefix$p>$p</a>\r\n");
		}

 		echo("<a title='到最后页' href='$prefix{$this->rs->PageCount}'> >></a>\r\n  ");
		//printf("&nbsp;<input onmouseover=this.focus(); onfocus=this.select(); type=text name=jump size=2 value=%s>",$this->AbsolutePage<$this->rs->PageCount?$this->AbsolutePage+1:1);
		//printf("\r\n<input type=button value=GO onclick=window.location='%s'+document.all.jump.value>",$prefix);
		printf("\r\n 页次 $this->AbsolutePage/{$this->PageCount}  共有记录数:$this->RecordCount\r\n</div>");
	}

	/**
    * @desc 获取总页数
	* @return int 
    */
	public function getPageCount(){
		return $this->PageCount;
	}
	
	/**
    * @desc 获取记录数
	* @return int 
    */
	public function getRecordCount(){
		return $this->RecordCount;
	}

	/**
    * @desc 获取当前页码
	* @param string URL代表分页的参数  http://www.cn?page=2
	* @return int 
    */
	public function GetCurrentPage($pageStr='page'){
		$CurrentPage=(isset($_GET[$pageStr]))?(is_numeric($_GET[$pageStr])?$_GET[$pageStr]:1):1;
     	return $CurrentPage=($CurrentPage<1)?1:(($CurrentPage>$this->PageCount)?$this->PageCount:$CurrentPage);
	}
	
}
