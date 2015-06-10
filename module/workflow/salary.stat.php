<?php

/*
*	Copyright VeryIDE,2007-2009
*	http://www.veryide.com/
*
*	$Id: ,v1 10:39 2008-7-27 Leilei $
*/

require '../../source/dialog/loader.php';
html_start("许可列表 - VeryIDE");
?>


<?php

	//载入模块配置并生成菜单
	$appid = Module :: get_appid();
	
	echo Module :: get_context( $appid );

	//////////////////////////////////


	
	//连接数据库
	System :: connect();	
	
?>

	<div id="box">
    
		<?php
		
		//当前年份
        $YEAR = date("Y");
        
		//每月份额
        $STAT = array();
		
		//年度总额
		$COUN = array( 'sales' => 0, 'count' => 0 );
        
        for( $i = 1; $i <= 12; $i++ ){
        
            //月开始和结束时间
            $beg = mktime(0, 0, 0, $i, 1, $YEAR);
            $end = mktime(23, 59, 59, $i, GetDay( $YEAR, $i), $YEAR);
			
			$STAT[$i]['threads'] = System :: $db -> getValue( "SELECT count(id) as count FROM `mod:workflow_thread` WHERE first=1 and `state`>0 and dateline>".$beg." AND dateline<".$end );			
			$STAT[$i]['replys'] = System :: $db -> getValue( "SELECT count(id) as count FROM `mod:workflow_thread` WHERE first=0 and `state`>0 and dateline>".$beg." AND dateline<".$end );
        }
        ?>
	
        <div class="item">今年（<?php echo $YEAR;?>）每月帖量统计</div>
        
        <table id="bind_data" class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr class="thead">
                <td class="text-bold" width="50">月份</td>
                <?php
                foreach( $STAT as $i => $row ){
                    echo '<td width="7%">'.$i.' 月</td>';
                }
                ?>
                <td class="text-bold">年度总额</td>
            </tr>
            <tr class="line">
                <td class="text-bold">主题</td>
                <?php
                foreach( $STAT as $i => $row ){
                    echo '<td'.( $row['threads'] ? ' class="text-no text-bold"' : '' ).'>'.( $row['threads'] ? number_format($row['threads']).' 个' : '' ).'</td>';
                }
                ?>
                <td class="text-no text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'threads' ) ) );?> 个</td>
            </tr>
            <tr class="band">
                <td class="text-bold">回复</td>
                <?php
                foreach( $STAT as $i => $row ){
                    echo '<td'.( $row['replys'] ? ' class="text-yes text-bold"' : '' ).'>'.( $row['replys'] ? number_format($row['replys']).' 条' : '' ).'</td>';
                }
                ?>
                <td class="text-yes text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'replys' ) ) );?> 条</td>
            </tr>
        </table>
        
        <!--********************************************-->
    
		<?php
		
		//当前年份
        $YEAR = date("Y") - 1;
        
		//每月份额
        $STAT = array();
		
		//年度总额
		$COUN = array( 'sales' => 0, 'count' => 0 );
        
        for( $i = 1; $i <= 12; $i++ ){
        
            //月开始和结束时间
            $beg = mktime(0, 0, 0, $i, 1, $YEAR);
            $end = mktime(23, 59, 59, $i, GetDay( $YEAR, $i), $YEAR);
            
            $STAT[$i]['threads'] = System :: $db -> getValue( "SELECT count(id) as count FROM `mod:workflow_thread` WHERE first=1 and `state`>0 and dateline>".$beg." AND dateline<".$end );			
			$STAT[$i]['replys'] = System :: $db -> getValue( "SELECT count(id) as count FROM `mod:workflow_thread` WHERE first=0 and `state`>0 and dateline>".$beg." AND dateline<".$end );
        }
        ?>
	
        <div class="item">去年（<?php echo $YEAR;?>）每月帖量统计</div>
        
        <table id="bind_data" class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr class="thead">
                <td class="text-bold" width="50">月份</td>
                <?php
                foreach( $STAT as $i => $row ){
                    echo '<td width="7%">'.$i.' 月</td>';
                }
                ?>
                <td class="text-bold">年度总额</td>
            </tr>
            <tr class="line">
                <td class="text-bold">主题</td>
                <?php
                foreach( $STAT as $i => $row ){
                    echo '<td'.( $row['threads'] ? ' class="text-no text-bold"' : '' ).'>'.( $row['threads'] ? number_format($row['threads']).' 个' : '' ).'</td>';
                }
                ?>
                <td class="text-no text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'threads' ) ) );?> 个</td>
            </tr>
            <tr class="band">
                <td class="text-bold">回复</td>
                <?php
                foreach( $STAT as $i => $row ){
                    echo '<td'.( $row['replys'] ? ' class="text-yes text-bold"' : '' ).'>'.( $row['replys'] ? number_format($row['replys']).' 条' : '' ).'</td>';
                }
                ?>
                <td class="text-yes text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'replys' ) ) );?> 条</td>
            </tr>
        </table>
        
        <!--********************************************-->
    
		<?php		
		$STAT = System :: $db -> getAll( "SELECT FROM_UNIXTIME( dateline,'%c') + 0 as `date`,count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 group by `date` ORDER BY `date` ASC" );
        ?>
	
        <div class="item">按月份统计趋势</div>
        
        <table id="bind_data" class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr class="thead">
                <td class="text-bold" width="50">月份</td>
                <?php
                foreach( $STAT as $row ){
                    echo '<td width="7%">'.$row['date'].' 月</td>';
                }
                ?>
                <td class="text-bold">所有月总额</td>
            </tr>
            <tr class="line">
                <td class="text-bold">主题</td>
                <?php
                foreach( $STAT as $row ){
                    echo '<td'.( $row['threads'] ? ' class="text-no text-bold"' : '' ).'>'.( $row['threads'] ? number_format($row['threads']).' 个' : '' ).'</td>';
                }
                ?>
                <td class="text-no text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'threads' ) ) );?> 个</td>
            </tr>
            <tr class="band">
                <td class="text-bold">回复</td>
                <?php
                foreach( $STAT as $row ){
                    echo '<td'.( $row['replys'] ? ' class="text-yes text-bold"' : '' ).'>'.( $row['replys'] ? number_format($row['replys']).' 条' : '' ).'</td>';
                }
                ?>
                <td class="text-yes text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'replys' ) ) );?> 条</td>
            </tr>
        </table>
        
        <!--********************************************-->
    
		<?php		
		$STAT = System :: $db -> getAll( "SELECT FROM_UNIXTIME( dateline,'%Y') as `date`,count( CASE WHEN first=1 THEN 1 ELSE NULL END ) as `threads`,count( CASE WHEN first=0 THEN 1 ELSE NULL END ) as `replys` FROM `mod:workflow_thread` WHERE `state`>0 group by `date` ORDER BY `date` ASC" );
        ?>
	
        <div class="item">按年份统计趋势</div>
        
        <table id="bind_data" class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr class="thead">
                <td class="text-bold" width="50">年份</td>
                <?php
                foreach( $STAT as $row ){
                    echo '<td width="7%">'.$row['date'].' 年</td>';
                }
                ?>
                <td class="text-bold">所有年总额</td>
            </tr>
            <tr class="line">
                <td class="text-bold">主题</td>
                <?php
                foreach( $STAT as $row ){
                    echo '<td'.( $row['threads'] ? ' class="text-no text-bold"' : '' ).'>'.( $row['threads'] ? number_format($row['threads']).' 个' : '' ).'</td>';
                }
                ?>
                <td class="text-no text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'threads' ) ) );?> 个</td>
            </tr>
            <tr class="band">
                <td class="text-bold">回复</td>
                <?php
                foreach( $STAT as $row ){
                    echo '<td'.( $row['replys'] ? ' class="text-yes text-bold"' : '' ).'>'.( $row['replys'] ? number_format($row['replys']).' 条' : '' ).'</td>';
                }
                ?>
                <td class="text-yes text-bold"><?php echo number_format( array_sum( getall_by_key( $STAT, 'replys' ) ) );?> 条</td>
            </tr>
        </table>
    
    </div>



<?php html_close();?>