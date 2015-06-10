<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';
html_start("数据结构 - VeryIDE");
?>



	<?php
	
	//loader
	require('include/naver.stats.php');
	
	//连接数据库
	System :: connect();	
	
	?>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
    <tr class="thead">
        <td width="40"></td>
        <td width="160">表名</td>
        <td width="100">引擎</td>
        <td width="120">字符集</td>
        <td>行数</td>
        <td>数据长度</td>
        <td>索引长度</td>
        <td>备注信息</td>
        <td>空间占用</td>
    </tr>
    <tr class="item">
        <td colspan="9"><?php echo loader_image("icon/gear.png");?> 系统数据（表前辍：<em><?php echo VI_DBMANPRE;?></em>）</td>
    </tr>
    
    <?php		

    $result = mysql_list_tables( VI_DBNAME );
	
	$i = 1;
	$s = 0;

    while ($row = mysql_fetch_row($result)) {
        
        $table = $row[0];
        
        if( strpos( $table , VI_DBMANPRE ) !== false ){
            
            //$count = System :: $db -> getValue("SELECT count(*) FROM `$table`");
            
            $schema = System :: $db -> getOne("SELECT ENGINE,TABLE_COLLATION,TABLE_ROWS,DATA_LENGTH,INDEX_LENGTH,TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".VI_DBNAME."' AND TABLE_NAME = '$table'");
            
            //<td> <input name="table['.$table.']" type="text" class="text" value="'.$_CACHE["sys.tables"][$table].'" /> </td>
            
            $size = number_format(($schema["DATA_LENGTH"]+$schema["INDEX_LENGTH"])/1024/1024,2);
        
            echo '
            <tr class="'.( $i % 2 == 0 ? 'line' : 'band' ).'">
                <td>'.$i.'</td>
                <td><strong class="text-yes">'.str_replace( VI_DBMANPRE , '' , $table ).'</strong></td>
                <td class="'.( $schema["ENGINE"] != 'MyISAM' ? 'text-no text-bold' : '' ).'">'.$schema["ENGINE"].'</td>
                <td>'.$schema["TABLE_COLLATION"].'</td>
                <td>'.$schema["TABLE_ROWS"].'</td>
                <td>'.$schema["DATA_LENGTH"].'</td>
                <td>'.$schema["INDEX_LENGTH"].'</td>
                <td>'.$schema["TABLE_COMMENT"].'</td>
                <td>'.$size.'MB</td>
            </tr>
            ';
			
			$i++;
			$s+=$size;
        
        }
        
    }
    
    ?>
    <tr class="choice">
        <th colspan="8">总计：</th>
        <td><b><?php echo $s;?> MB</b></td>
    </tr>
    
    <tr class="item">
        <td colspan="9"><?php echo loader_image("icon/box.png");?> 模块数据（表前辍：<em><?php echo VI_DBMODPRE;?></em>）</td>
    </tr>
    
    <?php
    
    $result = mysql_list_tables( VI_DBNAME );
    
    $s = 0;
    
    while ($row = mysql_fetch_row($result)) {
        
        $table = $row[0];
        
        if( strpos( $table , VI_DBMANPRE ) === false ){
            
            //$count = System :: $db -> getValue("SELECT count(*) FROM `$table`");				
            
            $schema = System :: $db -> getOne("SELECT ENGINE,TABLE_COLLATION,TABLE_ROWS,DATA_LENGTH,INDEX_LENGTH,TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".VI_DBNAME."' AND TABLE_NAME = '$table'");
            
            //<td> <input name="table['.$table.']" type="text" class="text" value="'.$_CACHE["sys.tables"][$table].'" /> </td>
            
            $size = number_format(($schema["DATA_LENGTH"]+$schema["INDEX_LENGTH"])/1024/1024,2);
        
            echo '
            <tr class="'.( $i % 2 == 0 ? 'line' : 'band' ).'">
                <td>'.$i.'</td>
                <td><strong class="text-yes">'.str_replace( VI_DBMODPRE , '' , $table ).'</strong></td>
                <td class="'.( $schema["ENGINE"] != 'MyISAM' ? 'text-no text-bold' : '' ).'">'.$schema["ENGINE"].'</td>
                <td>'.$schema["TABLE_COLLATION"].'</td>
                <td>'.$schema["TABLE_ROWS"].'</td>
                <td>'.$schema["DATA_LENGTH"].'</td>
                <td>'.$schema["INDEX_LENGTH"].'</td>
                <td>'.$schema["TABLE_COMMENT"].'</td>
                <td>'.$size.'MB</td>
            </tr>
            ';
			
			$i++;
			$s+=$size;
        
        }
        
    }
    
    ?>
     <tr class="choice">
        <th colspan="8">总计：</th>
        <td><b><?php echo $s;?> MB</b></td>
    </tr>
   
    <?php
    
	//关闭数据库
	System :: connect();
        
    ?>
    
    </table>        
    


<?php html_close();?>