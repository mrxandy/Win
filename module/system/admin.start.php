<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*/

require '../../source/dialog/loader.php';

html_start("欢迎使用 - VeryIDE");
?>


    <div id="board">
        
        <div id="calendar">
        	<span class="month"><?php echo $_G['project']['months'][date("n")-1]; ?></span>
            <span class="day"><?php echo date("j"); ?></span>
        	<span class="week"><?php echo $_G['project']['weeks'][date("w")]; ?></span>
        </div>
		
        <p>
        	<strong>
				<script type="text/javascript">Mo.write(Serv.Hello());</script>
                <span class="font_b"><?php echo $_G['manager']['account'];?></span>
            </strong>
            <br>
			<?php
			
			//生日提醒
			$birthday = $_CACHE['system']['admin'][$_G['manager']["id"]]["birthday"];
			
			if( $birthday ){			
				$array = explode("-",$birthday);
				
				$bd = mktime(0, 0, 0, $array[1], $array[2], date("Y"));
				
				$start = $bd - (86400*3);
				$end = $bd + (86400*3);
			}
			
			//开始时间
			if( $birthday && (time()>$start && time()<$end) ){
				
				echo loader_image("icons/present_16.png");
				echo '<span class="text-key">祝你生日快乐！</span>';
				
				echo '<br />您的生日是 '.$birthday;
			}else{
				if( $_G['manager']["last_login"] ){
					echo '上次登录：<span class="text-key">'.date("Y-m-d H:i:s",$_G['manager']["last_login"]).'</span>
					<br>共访问 <span class="text-key">'.$_G['manager']["stat_login"].'</span> 次';
				}else{
					echo '欢迎您第一次访问';
				}
			}
            ?>
        </p>
    </div>
    
    <div id="quick">
        
       <dl>
            <dt><strong>VeryIDE 系统</strong></dt>
    		<dd>
                <ul>
                    <li><a href="serve.news.php"><img src="<?php echo VI_BASE;?>static/image/classic/bubble_32.png" /> 官方动态 <br /><span>NEWS</span></a></li>
                    <li><a href="system.licence.php"><img src="<?php echo VI_BASE;?>static/image/classic/licence_32.png" /> 版本验证 <br /><span>LICENCE</span></a></li>
                    <li><a href="group.list.php"><img src="<?php echo VI_BASE;?>static/image/classic/group_32.png" /> 用户组管理 <br /><span>GROUP</span></a></li>
                    <li><a href="admin.list.php"><img src="<?php echo VI_BASE;?>static/image/classic/user_32.png" /> 用户管理 <br /><span>USER</span></a></li>                  
                    <li><a href="data.stats.php"><img src="<?php echo VI_BASE;?>static/image/classic/statistics_32.png" /> 统计信息 <br /><span>STATS</span></a></li>
                    
                    <li><a href="admin.event.php"><img src="<?php echo VI_BASE;?>static/image/classic/clock_32.png" /> 操作日志 <br /><span>LOGS</span></a></li>
                    <li><a href="data.attach.php"><img src="<?php echo VI_BASE;?>static/image/classic/folder_32.png" /> 附件管理 <br /><span>ATTACH</span></a></li>
                    <li><a href="data.update.php"><img src="<?php echo VI_BASE;?>static/image/classic/reload_32.png" /> 更新管理 <br /><span>UPGRADE</span></a></li>
                    <li><a href="serve.control.php"><img src="<?php echo VI_BASE;?>static/image/classic/gear_32.png" /> 系统配置 <br /><span>SETTING</span></a></li>
                    <li><a href="module.control.php"><img src="<?php echo VI_BASE;?>static/image/classic/box_32.png" /> 管理模块 <br /><span>MODULE</span></a></li>
                    <li><a href="system.server.php"><img src="<?php echo VI_BASE;?>static/image/classic/diagram_32.png" /> 服务器信息 <br /><span>SERVER</span></a></li>
                    <li><a href="data.backup.php"><img src="<?php echo VI_BASE;?>static/image/classic/save_32.png" /> 数据备份 <br /><span>BACKUP</span></a></li>
                    <li><a href="system.secure.php"><img src="<?php echo VI_BASE;?>static/image/classic/shield_32.png" /> 安全中心 <br /><span>SAFE</span></a></li>
                </ul>
             
            <dd>
        </dl>
        
        <dl>
            <dt><strong>VeryIDE 服务</strong></dt>
    		<dd>
                <ul>
                    <li><a href="http://www.veryide.com/" target="_blank"><img src="<?php echo VI_BASE;?>static/image/classic/home_32.png" /> 官方网站 <br /><span>VERYIDE</span></a></li>
                    <li><a href="http://www.veryide.com/product.php" target="_blank"><img src="<?php echo VI_BASE;?>static/image/classic/down_32.png" /> 程序下载 <br /><span>DOWNLOAD</span></a></li>
                    <li><a href="http://www.veryide.com/market.php" target="_blank"><img src="<?php echo VI_BASE;?>static/image/classic/buy_32.png" /> 购买产品 <br /><span>MARKET</span></a></li>
                    <li><a href="http://www.veryide.com/guide.php" target="_blank"><img src="<?php echo VI_BASE;?>static/image/classic/info_32.png" /> 使用手册 <br /><span>GUIDE</span></a></li>
                    <li><a href="http://www.veryide.com/forum.php" target="_blank"><img src="<?php echo VI_BASE;?>static/image/classic/letter_32.png" /> 联系我们 <br /><span>CONTACT</span></a></li>		
                    <li><a href="http://www.veryide.com/forum.php" target="_blank"><img src="<?php echo VI_BASE;?>static/image/classic/bug_32.png" /> 报告错误 <br /><span>REPORT</span></a></li>
                </ul>
            </dd>
        </dl>
    </div>
    
    <div id="footer" class="powered">
        <?php echo $_G['product']['appname'];?>
        <?php echo $_G['product']['version'];?>
    	<?php echo $_G['project']['powered'];?>
    </div>

<?php html_close();?>