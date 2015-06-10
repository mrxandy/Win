<?php

$html = '<ul id="subtab">';

$html .= '<li><a href="module.setting.php?appid='.$appid.'">模块设置</a></li>';

if( file_exists( VI_ROOT.'module/'.$appid.'/navigate.php' ) ){
	$html .= '<li><a href="module.navigate.php?appid='.$appid.'">导航设置</a></li>';
}

$html .= '</ul>';

echo str_replace( '<li><a href="'.GetCurFile(), '<li class="active"><a href="'.GetCurFile(), $html );

?>