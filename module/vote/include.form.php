<?php

/*************************************************

		VeryIDE 通用模块文件：表单高级选项

*************************************************/

if(!defined('VI_BASE')) {
	exit('Access Denied');
}

?>

    <tr>
        <td colspan="2" class="section"><strong>用户限制</strong></td>
    </tr>

    <tr>
        <th>地区限制：</th>
        <td>
            <input type="text" class="text" name="config[USER_ZONE]" size="35" value="<?php echo $config["USER_ZONE"];?>" placeholder="用户地区的判断不一定准确" />
            <var data-type="tip">使用空格分隔多个地址</var>
        </td>
    </tr>

    <tr>
        <th>用户选项：</th>
        <td>
            <label>
            <input type="radio" class="radio" name="config[USER_MODE]" value="ANY" checked /> 
            匿名用户
            </label>
            <label>
            <input type="radio" class="radio" name="config[USER_MODE]" value="REG" />
            注册用户
            </label>
        </td>

    </tr>
    
    <tbody id="MODE_ANY" style="display:none;">

        <tr text='对于匿名用户收集扩展信息'>
            <th>匿名扩展：</th>
            <td>
                <label>
                <input name="config[ANY_USERNAME]" type="checkbox" class="checkbox" value="Y" /> 
                用户名
                </label>
                
                <label>
                <input name="config[ANY_NAME]" type="checkbox" class="checkbox" value="Y" /> 
                姓名
                </label>
                
                <label>
                <input name="config[ANY_QQ]" type="checkbox" class="checkbox" value="Y" /> 
                QQ号码
                </label>
                
                <label>
                <input name="config[ANY_EMAIL]" type="checkbox" class="checkbox" value="Y" /> 
                邮箱地址
                </label>
                
                <label>
                <input name="config[ANY_PHONE]" type="checkbox" class="checkbox" value="Y" /> 
                电话号码
                </label>
                
                <label>
                <input name="config[ANY_IDCARD]" type="checkbox" class="checkbox" value="Y" /> 
                身份证
                </label>
                
                <label>
                <input name="config[ANY_COMPANY]" type="checkbox" class="checkbox" value="Y" /> 
                单位名称
                </label>
            </td>
        </tr>                
  
    </tbody>
    
    <tbody id="MODE_REG" style="display:none;">
                
        <tr>
            <th>收集信息：</th>
            <td>
                
                <table id="mods" cellpadding="0" cellspacing="1" border="0" class="frame">
                
                <?php
                
                echo '<tr>';
                
                //载入会员配置
                require VI_ROOT.'module/member/config.php';
                
                $i = 0;
                foreach ($_G['module']['member']["fieds"] as $fied => $name){
                    //
                    if( !in_array($fied,array("id","modify","ip","dateline","stat_login","stat_level"))  ){	
                    
                        echo '<td>';
                        echo '<label><input name="config[REG_'.strtoupper($fied).']" value="1" type="checkbox" class="checkbox" '.($config["REG_".strtoupper($fied)]?' checked="checked"':'').' /> '.$name.'</label>';
                        echo '</td>';
                        
                        if( $i == 7 ){
                            echo "</tr>";
                            $i = 0;
                        }else{						
                            $i++;
                        }
                    
                    }
                    
                }
                
                echo '</tr>';
				                
                //载入通行证配置
                require(VI_ROOT."module/passport/setting.php");
                
                ?>
                
                </table>
                
                <a href="javascript:Mo('#mods input[type=checkbox]').checked( true );void(0);">全选</a> / <a href="javascript:Mo('#mods input[type=checkbox]').checked( false );void(0);">全不选</a> / <a href="javascript:Mo('#mods input[type=checkbox]').checked( );void(0);">反选</a>
                
                如果未选任何字段，则视为仅需登录即可
                
            </td>
            
        </tr>
        
        <tr>
        	<th>防刷设置：</th>
            <td>
            
            <table cellpadding="0" cellspacing="1" border="0" class="gird">
                <tr>
                    <td>
                    参加次数<br />
                    <select name="config[FORM_DATEDIFF]">
                        <option value="ALL">全部</option>
                        <option value="DAY">每天</option>
                        <option value="HOUR">每小时</option>
                    </select>
                    <input type="text" size="13" class="text" name="config[FORM_NUMBER]" value="<?php echo $config["FORM_NUMBER"];?>" data-valid-name="参加次数" data-valid-number="no" /> 票
                    <var data-type="tip">留空为不限制</var>
                    </td>
                    <td>
                    赠送积分<br />
                    加减 <input type="text" size="13" class="coin" name="config[FORM_MONEY]" value="<?php echo $config["FORM_MONEY"];?>" data-valid-name="积分数值" data-valid-number="no" />
					（<?php echo $_G['setting']['passport']["currency"];?>）
                    </td>
                </tr>
                <tr>
                    <td>
                    注册时间<br />
                    早于 <input type="text" name="config[FORM_REGDATE]" id="REGDATE" class="text date" value="<?php echo $config["FORM_REGDATE"] ? date("Y-m-d",$config["FORM_REGDATE"]) : '';?>" size="12" readonly="true" title="年-月-日" />
                    <a href="javascript:;" onclick="Mo('#REGDATE').value('');">清空</a>
                    <var data-type="tip">投票用户注册时间必需小于或等于此值</var>
                    </td>
                    <td>
                    积分限制<br />
                    大于 <input type="text" size="13" class="coin" name="config[FORM_SCORE]" value="<?php echo $config["FORM_SCORE"];?>" data-valid-name="积分数值" data-valid-number="no" />
                    <var data-type="tip">投票用户积分必需大于或等于此值</var>
                    </td>
                </tr>
            </table>
            
            </td>
        </tr>
    
    </tbody>
    
    <tr>
        <td colspan="2" class="section pointer" onclick=" Mo('#box-extend').toggle( 0, function( vis ){ if( vis ){ Mo('#sel-extend').attr({ 'class' : 'open' }); }else{ Mo('#sel-extend').attr({ 'class' : 'close' }); } } ); "><a id="sel-extend" class="close"></a><strong>扩展信息</strong></td>                
    </tr>
    
    <tbody id="box-extend" style="display:none;">
                
        <tr>
            <th>人数上限：</th>
            <td>
                <input type="text" size="13" class="text" name="config[FORM_MAX]" value="<?php echo $config["FORM_MAX"];?>" />
                最多允许多少人数参加                       
            </td>
        </tr>

        <tr text='填写此表单的关键字词,将用于显示与此相关的表单'>
            <th>相关标签：</th>
            <td>
            <?php
			$tmp = array();
			foreach( $_CACHE['system']['module'] as $mod => $app ){
				if( $app['model'] != 'module' ) continue;
				$tmp[] = $app['name'];
			}
			$tag = implode( '\',\'', $tmp );
			?>
            <input name="tags" type="text" class="text" id="tags" value="<?php echo $row["tags"];?>" size="60" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '内置标签' : ['<?php echo $tag;?>'] }, function( value ){ var val = this.value; var txt = ' '+ value; var pos = val.indexOf( txt ); this.value = pos > -1 ? val.replace( txt, '' ) : val + txt; }, 1 , 23 );" />
            </td>
        </tr>
    
        <tr text='填写此表单引用页地址,将在列表中显示,以后可以更方便的找到引用页'>
            <th>引用地址：</th>
            <td>
                <input name="quote" type="text" class="text" id="quote" value="<?php echo $row["quote"];?>" size="60" />
                <?php echo loader_image("icon/globe.png","打开引用地址","","var s=Mo('#quote').value();if(s){window.open(s);}");?>
                
                <select name="config[PAGE_REDIRECT]">
                    <option value="">成功提交后..</option>
                    <option value="A">新窗口打开</option>
                    <option value="B">本窗口打开</option>
                </select>
            </td>
        </tr>

    </tbody>
        
    <tr>
        <td colspan="2" class="section pointer" onclick=" Mo('#box-advanced').toggle( 0, function( vis ){ if( vis ){ Mo('#sel-advanced').attr({ 'class' : 'open' }); }else{ Mo('#sel-advanced').attr({ 'class' : 'close' }); } } ); "><a id="sel-advanced" class="close"></a><strong>高级选项</strong></td>                
    </tr>
    
    <tbody id="box-advanced" style="display:none;">

        <tr text='用于设置选项组显示顺序'>
            <th>随机选项：</th>
            <td>
                <label>
                <input name="config[FORM_RAND_GROUPS]" type="checkbox" class="checkbox" value="Y" >	
                选项组随机排序
                </label>
                <label>
                <input name="config[FORM_RAND_OPTION]" type="checkbox" class="checkbox" value="Y" >	
                子选项随机排序
                </label>
                <label>
                <input type="text" name="config[FORM_RAND_LIMIT]" title="随机显示X个" class="text digi" onclick="Mo.Soler( this, event, this.value, 'mo-soler', { '随机显示X个' : (function(){ var a = []; for(var i=0;i<=48;i++){a.push(i);} return a; })() }, function( value ){ this.value = value; }, 1 , 23 );" readonly="true" value="<?php echo $config['FORM_RAND_LIMIT']+0;?>" />
                </label>
            </td>
        </tr>
        
        <tr>
            <th>图形验证：</th>
            <td>
                <label><input name="config[VERIFY_CAPTCHA]" type="radio" class="radio" value="Y" /> 开启</label>
                <label><input name="config[VERIFY_CAPTCHA]" type="radio" class="radio" value="" /> 关闭</label>						  
            </td>
        </tr>
        
        <tr>
            <th>Cookie：</th>
            <td>

                <select name="config[FORM_FREQ]">
                    <option value="0">间隔时间</option>
                    <option value="10">10分钟间隔</option>
                    <option value="30">30分钟间隔</option>
                    <option value="60">1小时间隔</option>
                    <option value="120">2小时间隔</option>
                    <option value="360">6小时间隔</option>
                    <option value="720">12小时间隔</option>
                    <option value="1440">24小时间隔</option>
                </select>

            </td>
        </tr>
        
        <tr>
            <th>I P 限制：</th>
            <td>
                <select name="config[VERIFY_MODE]">
                    <option value="ALL">总共</option>
                    <option value="DAY">每天</option>
                    <option value="HOUR">每小时</option>
                </select>
                <input type="text" size="13" class="text" name="config[FORM_SPEED]" value="<?php echo $config["FORM_SPEED"];?>" />
                票（留空为不限制）

                <select name="config[FORM_SPACE]">
                    <option value="0">间隔时间</option>
                    <option value="10">10分钟间隔</option>
                    <option value="30">30分钟间隔</option>
                    <option value="60">1小时间隔</option>
                    <option value="120">2小时间隔</option>
                    <option value="360">6小时间隔</option>
                    <option value="720">12小时间隔</option>
                    <option value="1440">24小时间隔</option>
                </select>

            </td>
        </tr>
    
        <tr>
            <th>提交控制：</th>
            <td>
                <select name="config[FORM_TARGET]">
                    <option value="">目标窗口</option>
                    <option value="_blank">新窗口</option>
                    <option value="_parent">父窗口</option>
                    <option value="_top">顶窗口</option>
                </select>
                
                <var data-type="tip"><strong>目标窗口</strong><br />提交表单时处理的窗口位置</var>
            </td>
            
        </tr>
        
        <tr>
            <th>隐藏部分：</th>
            <td>
                <!--label><input type="checkbox" class="checkbox" name="config[HIDE_NAVER]" value="Y" /> 隐藏导航</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_TITLE]" value="Y" /> 隐藏标题</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_STATE]" value="Y" /> 隐藏描述</label>                
                <label><input type="checkbox" class="checkbox" name="config[HIDE_COUNT]" value="Y" /> 隐藏统计</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_NUMBER]" value="Y" /> 隐藏编号</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_RIGHT]" value="Y" /> 隐藏版权</label-->

                <label><input type="checkbox" class="checkbox" name="config[HIDE_NAVER]" value="Y" /> 全局导航</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_RIGHT]" value="Y" /> 全局版权</label>
                
                <label><input type="checkbox" class="checkbox" name="config[HIDE_TITLE]" value="Y" /> 活动标题</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_DESCRIBE]" value="Y" /> 活动描述</label>                
                <label><input type="checkbox" class="checkbox" name="config[HIDE_COUNT]" value="Y" /> 参与人数</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_STATE]" value="Y" /> 详细数据</label>                
                <label><input type="checkbox" class="checkbox" name="config[HIDE_RESULT]" value="Y" /> 统计结果</label>
                <label><input type="checkbox" class="checkbox" name="config[HIDE_NUMBER]" value="Y" /> 隐藏编号</label>
           </td>
        </tr>
        
        <tr>
            <th>邮件通知：</th>
            <td>
                <label><input type="checkbox" class="checkbox" name="config[FORM_NOTICE]" value="Y" /> 有新用户参加此活动，请邮件通知我</label><label>，同时抄送至：
                <input type="text" size="30" class="text" name="config[FORM_SEND]" value="<?php echo $config["FORM_SEND"];?>" />
                <var data-type="tip">使用逗号分隔多个地址</var>
                </label>
           </td>
        </tr>
    
    </tbody>
    
    <input type="hidden" name="config[PAGE_UNIT]" value="<?php echo $config['PAGE_UNIT'];?>" /> 
    <input type="hidden" name="config[PAGE_WIDTH]" value="<?php echo $config['PAGE_WIDTH'];?>" /> 
    <input type="hidden" name="config[PAGE_ALIGN]" value="<?php echo $config['PAGE_ALIGN'];?>" /> 
    
    <script>
	
	Mo("input[name='config[HIDE_NAVER]']").value("<?php echo $config['HIDE_NAVER'];?>");	
	Mo("input[name='config[HIDE_TITLE]']").value("<?php echo $config['HIDE_TITLE'];?>");
	Mo("input[name='config[HIDE_DESCRIBE]']").value("<?php echo $config['HIDE_DESCRIBE'];?>");
	Mo("input[name='config[HIDE_COUNT]']").value("<?php echo $config['HIDE_COUNT'];?>");
	Mo("input[name='config[HIDE_STATE]']").value("<?php echo $config['HIDE_STATE'];?>");
	Mo("input[name='config[HIDE_RESULT]']").value("<?php echo $config['HIDE_RESULT'];?>");
	Mo("input[name='config[HIDE_NUMBER]']").value("<?php echo $config['HIDE_NUMBER'];?>");
	Mo("input[name='config[HIDE_RIGHT]']").value("<?php echo $config['HIDE_RIGHT'];?>");
	
	Mo("select[name='config[PAGE_REDIRECT]']").value("<?php echo $config['PAGE_REDIRECT'];?>");
        
	Mo("select[name='config[PAGE_SOUND-LOOP]']").value("<?php echo $config['PAGE_SOUND-LOOP'];?>");
	Mo("select[name='config[PAGE_BG_REPEAT]']").value("<?php echo $config['PAGE_BG_REPEAT'];?>");
	Mo("select[name='config[PAGE_BG_ATTACHMENT]']").value("<?php echo $config['PAGE_BG_ATTACHMENT'];?>");        
	Mo("select[name='config[PAGE_BG_X]']").value("<?php echo $config['PAGE_BG_X'];?>");
	Mo("select[name='config[PAGE_BG_Y]']").value("<?php echo $config['PAGE_BG_Y'];?>");
	Mo("select[name='config[PAGE_BG_OPACITY]']").value("<?php echo $config['PAGE_BG_OPACITY'];?>");
	
	Mo("select[name='config[VERIFY_MODE]']").value("<?php echo $config['VERIFY_MODE'];?>");
	Mo("select[name='config[FORM_FREQ]']").value("<?php echo $config['FORM_FREQ'];?>");
	Mo("select[name='config[FORM_SPACE]']").value("<?php echo $config['FORM_SPACE'];?>");
	Mo("input[name='config[VERIFY_CAPTCHA]']").value("<?php echo $config['VERIFY_CAPTCHA'];?>");
	
	Mo("select[name='config[FORM_TARGET]']").value("<?php echo $config['FORM_TARGET'];?>");
	
	Mo("select[name='config[FORM_DATEDIFF]']").value("<?php echo $config['FORM_DATEDIFF'];?>");
	Mo("input[name='config[FORM_NOTICE]']").value("<?php echo $config['FORM_NOTICE'];?>");
	
	Mo("input[name='config[FORM_RAND_GROUPS]']").value("<?php echo $config['FORM_RAND_GROUPS'];?>");
	Mo("input[name='config[FORM_RAND_OPTION]']").value("<?php echo $config['FORM_RAND_OPTION'];?>");
	
	/**************/
        
	//绑定事件
	Mo("input[name='config[USER_MODE]']").bind( 'click',function(){

		 var o = this.value;

		 Mo("#MODE_ANY").hide();
		 Mo("#MODE_REG").hide();

		 Mo("#MODE_"+o).show();

	});
	
	//隐藏所有模式
	Mo("input[name='config[USER_MODE]']").each( function(){        
	   Mo("#MODE_"+this.id).hide();        
	}).value("<?php echo $config['USER_MODE'] ? $config['USER_MODE'] : 'ANY';?>");
	
	//显示当前模式
	var o = Mo("input[name='config[USER_MODE]']").value();        
	Mo("#MODE_"+o).show();
	
	/**************/
	
	Mo("input[name='config[ANY_USERNAME]']").value("<?php echo $config['ANY_USERNAME'];?>");
	Mo("input[name='config[ANY_NAME]']").value("<?php echo $config['ANY_NAME'];?>");
	Mo("input[name='config[ANY_PHONE]']").value("<?php echo $config['ANY_PHONE'];?>");
	Mo("input[name='config[ANY_IDCARD]']").value("<?php echo $config['ANY_IDCARD'];?>");
	Mo("input[name='config[ANY_QQ]']").value("<?php echo $config['ANY_QQ'];?>");
	Mo("input[name='config[ANY_EMAIL]']").value("<?php echo $config['ANY_EMAIL'];?>");
	Mo("input[name='config[ANY_COMPANY]']").value("<?php echo $config['ANY_COMPANY'];?>");

	</script>