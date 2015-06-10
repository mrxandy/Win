<?php

class Captcha{
	
	//exit($_G['setting']['global']["domain"]);

	//////////////////////////////////
		
	//输出验证码图片
	public static function display( ){
		
		//关闭缓冲
		ob_end_clean();
	
		header("Content-type: image/png");

		//图片参数
		$width			= getnum("width",90);			//图片宽
		$height			= getnum("height",30);			//图片高
		$len			= 4;								//生成几位验证码
		
		$bgcolor		= "#ffffff";						//背景色
		
		$noise			= true;								//生成杂点
		$noisenum		= 150;								//杂点数量
		
		$border			= false;							//边框
		$bordercolor	= "#CCCCCC";						//边框颜色
		
		//////////////////////////////////////
		
		//创建图片
		$image = imagecreate($width, $height);
		
		$back = self  :: getcolor( $image, $bgcolor );
		
		imagefilledrectangle($image, 0, 0, $width, $height, $back);
		
		$size = $width/$len;
		
		if($size>$height) $size = $height;
		
		$left = ($width-$len*($size+$size/10))/$size;

		//字体位置
		$self = VI_ROOT.'source/fonts/';
		if( !ini_get("safe_mode") ){
			putenv( 'GDFONTPATH=' . $self );
		}

		//生成字符
		for ($i=0; $i<$len; $i++){
			
			//当前字符
			$text = rand(0, 9);
			
			//累加验证码
			$code .= $text;
			
			//创建颜色
			$color = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100));
			
			//字体路径
			$font = $self.rand(1,4).".ttf"; 
			
			//随机大小
			$randsize = rand($size-$size/10, $size+$size/10);
			
			//文字起始位置
			$location = $left+($i*$size+$size/10);

			imagettftext($image, $randsize, rand(-18,18), $location, rand($size-$size/10, $size+$size/10), $color, $font, $text ); 
		}

		//生成杂点
		if($noise == true) self  :: setnoise( $image, $width, $height, $back, $noisenum );
		
		$_SESSION['captcha'] = $code;

		//生成边框
		$bordercolor = self  :: getcolor( $image, $bordercolor );
		
		if($border==true) imagerectangle($image, 0, 0, $width-1, $height-1, $bordercolor);

		//输出图片
		imagepng($image);
		imagedestroy($image);
	
	}

	//获得颜色
	public static function getcolor( $image, $color ){
		 $color = preg_replace("/^#/", "", $color);
		 $r = $color[0].$color[1];
		 $r = hexdec ($r);
		 $b = $color[2].$color[3];
		 $b = hexdec ($b);
		 $g = $color[4].$color[5];
		 $g = hexdec ($g);
		 $color = imagecolorallocate ($image, $r, $b, $g); 
		 return $color;
	}

	//生成杂点
	public static function setnoise( $image, $width, $height, $back, $noisenum ){
		for ($i=0; $i<$noisenum; $i++){
			$randColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));  
			imagesetpixel($image, rand(0, $width), rand(0, $height), $randColor);
		} 
	}
	
	//////////////////////////////
	
	public static function phone( $phone, $size = 20, $color = '#000000' ){
		
		//关闭缓冲
		ob_end_clean();
		
		$phone = (string) trim( $phone );
	
		$width		= strlen($phone) * $size * 0.75;
		$height		= $size;
		
		header("Content-type: image/png");
		
		$image = imagecreate( $width, $height ) or die("Cannot Initialize new GD image stream");
		
		imagecolortransparent( $image, imagecolorallocate( $image, 255, 255, 255 ) );
		
		//字体位置
		$self = VI_ROOT.'source/fonts/';
		if( !ini_get("safe_mode") ){
			putenv( 'GDFONTPATH=' . $self );
		}
		
		//字体位置
		$font = $self.'arial.ttf';
		
		//创建颜色
		$color = self :: getcolor( $image, $color );
		
		imagettftext($image, $size, 0, 0, $size, $color, $font, $phone ); 
		
		imagepng($image);
		
	}

}

