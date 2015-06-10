<?php

class Vote extends Module{

	public static function parse_option( $params ){
		global $_CACHE;
		global $_G;
		
		extract($params);
		
		$appid = "vote";

		$config = $_CACHE[$appid]['group'][$gid]["config"];
		
		//展示模板
		$template = $config["GROUP_STYLE"] ? $_G['module']['vote']["style"][$config["GROUP_STYLE"]] : $config["GROUP_TEMPLATE"];
		
		$content = str_replace("{LABEL}",$option["name"],$template);
		
		//如果有链接
		if( $option["quote"] ){
			$content = str_replace("{LINK}", $option["quote"] ,$content);
		}else{
			$content = str_replace("{LINK}", "javascript:void(0);" ,$content);
		}
		
		//如果没有链接，也没有使用详细页，就过滤新窗口
		if( !$option["quote"] && strpos($content,"{DETAIL}") === false ){
			$content = str_replace("_blank", "" ,$content);	
		}
		
		$content = str_replace("{DETAIL}", "?action=detail&id=".$_CACHE[$appid]['form']["id"]."&gid=".$option['gid']."&oid=".$option['id'] ,$content);
		$content = str_replace("{DESC}",$option["description"],$content);
		$content = str_replace("{IMAGE}", ( $option['image'] ? $option['image'] : '' ) ,$content);
		$content = str_replace("{COUNT}",$option['stat'],$content);
		$content = str_replace(chr(13),"<br />",$content);
		
		$content = str_replace("{WIDTH}",$config["GROUP_WIDTH"],$content);
		$content = str_replace("{HEIGHT}",$config["GROUP_HEIGHT"],$content);
		
		$content = str_replace(' width=""','',$content);
		$content = str_replace(' height=""','',$content);
		
		///////////////
		
		$temp = 'G-'.$gid;

		//类型换算
		switch($group["type"]){
			case 'radio':
				$input = "<label for='".$temp."-".$oid."'><input type='radio' class='radio' name='".$temp."[]' id='".$temp."[]' value='".$option['id']."' data-valid-name='".$group["name"]."' ".($config["GROUP_MUST"]?"data-valid-empty='yes'":'')." /></label>";
			break;
				
			case 'checkbox':
				$input = "<label for='".$temp."-".$oid."'><input type='checkbox' class='checkbox' name='".$temp."[]' value='".$option['id']."' data-valid-name='".$group["name"]."' ".($config["GROUP_MAX"]?"data-valid-maxsize='".$config["GROUP_MAX"]."'":'')." ".($config["GROUP_MIN"]?"data-valid-minsize='".$config["GROUP_MIN"]."'":'')." /></label>";
			break;
				
			case 'button':
				$input = "<label for='".$temp."-".$oid."'><button type='submit' class='button' name='".$temp."[]' value='".$option['id']."'>投票</button></label>";
			break;
		}
		
		//////////////
		
		if(stripos($content,"{INPUT}") !== false ){
			$content = str_replace("{INPUT}",$input,$content);
		}else{
			$content .= $input;
		}
		
		$content = ubb_basic($content);
		
		return $content;

	}

	/*
		$gid		选项组ID
		$page		当前页码
		$length		每页数量
	*/
	public static function slice( $gid, $page, $size ){
		global $_CACHE;
		
		if( !$size ) return FALSE;
		
		//索引开始位置
		$offset = ( $page - 1 ) * $size;		
		
		//索引结束位置
		$length = $size;
		
		//原始索引长度
		$index = count( $_CACHE['vote']['option'][$gid] );
		
		//echo '<!--$offset:'.$offset.' - $length:'.$length.'-->';
		
		/*
	
		//筛选本组全部子选项
		//$tmp = array();
		
		$index = 0;
		foreach( $_CACHE['vote']['option'][$gid] as $oid => $option ){
			//删除掉索引以外的
			//echo $index .'<'. $offset .'||'. $index .'>'. $length;
			//echo '<br />';
			if( $index < $offset || $index > $length ){
				unset( $_CACHE['vote']['option'][$oid] );
			}
			//$tmp[ $oid ] = $option;				
			$index++;
		}
		
		return $index;
		
		*/
		
		$_CACHE['vote']['option'][$gid] = array_slice( $_CACHE['vote']['option'][$gid], $offset, $length, true  );
		
		return $index;
		
	}

	/*
		计算真实票数
		$fid		表单ID
		$gid	组ID
		$oid	选项ID
	*/
	public static function stat( $fid, $gid, $oid ){
		
		$val = array();
		
		//投票值
		$val[0] = '"G-'.$gid.'":"'.$oid.'"';
		$val[1] = '"G-'.$gid.'":"'.$oid.',%"';
		$val[2] = '"G-'.$gid.'":"%,'.$oid.'"';
		$val[3] = '"G-'.$gid.'":"%,'.$oid.',%"';
		
		//计算准确值
		$sql = "SELECT count(id) FROM `mod:form_data` WHERE (";
		
		$sql .= "config like '%".$val[0]."%' or ";
		$sql .= "config like '%".$val[1]."%' or ";
		$sql .= "config like '%".$val[2]."%' or ";
		$sql .= "config like '%".$val[3]."%'";
		
		$sql .= ") and fid = ".$fid;
		
		$stat = System :: $db -> getValue( $sql );
		
		return $stat;
		
	}

}

?>