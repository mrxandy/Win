<?php

///通用验证码类
//@copyright wangsl@500wan.com
class Utils_Caption
{
	var $Width      = 60;           //图片宽
	var $Height     = 30;           //图片高
	var $Length     = 4;            //验证码位数
	var $textLang   = 'en';         //验证码位数
	var $BgColor    = "#FFFFFF";    //背景色

	var $TFonts = array( 'airbus.ttf', 'arial.ttf', 'grotesk.ttf' );
	var $TFontSize =array(17,20); 	//字体大小范围
	var $TFontAngle =array(-20,20); //旋转角度

	var $Code    = array();          //验证码
	var $Image   = "";              //图形对象
	
	//字体颜色,红绿蓝黄紫黑
	var $FontColors = array('#f36161','#6bc146','#5368bd','#FD6F00','#7A00FF','#444444');
	
	///字符间距
	var $TPadden = 0.75;
	
	///x轴两边距离
	var $Txbase = 5;
	
	///y轴两边距离
	var $Tybase =5 ;
	
	///画干扰线
	var $TLine =true;
	
	///自计算尺寸
	var $AutoSize =false;

	///生成验证码
	public  function RandRSI(){
		global $_G;	
	
		$this->TFontAngle=range($this->TFontAngle[0],$this->TFontAngle[1]);
		$this->TFontSize=range($this->TFontSize[0],$this->TFontSize[1]);

		$arr=array();
		$TFontAngle=$this->TFontAngle;
		$TFontSize=$this->TFontSize;
		$FontColors=$this->FontColors;
		$code="";
		
		if( $this->textLang == 'cn' ){
			$font = VI_ROOT.'source/fonts/heiti.ttc';
			$this->TPadden = $this->TPadden * 0.7;
			$this->TFontSize = range( 12, 16 );
		}else{
			$font = VI_ROOT.'source/fonts/' . $this->TFonts[ array_rand( $this->TFonts ) ];
		}

		$anglelen=count($TFontAngle)-1; // 角度范围
		$fontsizelen=count($TFontSize)-1; // 角度范围
		$fontcolorlen=count($FontColors)-1; // 角度范围
		
		$textArray = $this->randText($this->textLang);

		///得到字符与颜色
		foreach( $textArray as $i => $char ){
			
			$angle=$TFontAngle[rand(0,$anglelen)]; ///旋转角度
			$fontsize=$TFontSize[rand(0,$fontsizelen)]; ///字体大小
			$fontcolor=$FontColors[rand(0,$fontcolorlen)]; ///字体颜色

			$bound=$this->_calculateTextBox($fontsize,$angle,$font,$char); ///得到范围

			$arr[] = array($fontsize,$angle,$fontcolor,$char,$font,$bound);  ///得到矩形框
		}
		
		$this->Code = $arr; //验证码
		
		return iconv( 'UTF-8', $_G['product']['charset'], implode( '', $textArray ) );
	}

    //@产生随机字符
    public function randText($type){
		global $_G;
		
        $string = array();
		
        switch( $type ){
            case 'en':
                $str = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
				$len = strlen($str) - 1;
                for($i=0;$i<$this->Length;$i++){
                     $string[] = $str[mt_rand( 0, $len )];
                }
			break;
			
			//转换编码到utf8
            case 'cn':
                for($i=0;$i<$this->Length;$i++) {
					$string[] = iconv( $_G['product']['charset'], 'UTF-8', chr(rand(0xB0,0xCC)).chr(rand(0xA1,0xBB)) );
                }
			break;
        }
		
        return $string;
    }

	///画图
	public function Draw(){
	
		if(empty($this->Code)) $this->RandRSI();
		
		$codes=$this->Code; ///用户验证码


		$wh = $this->_getImageWH($codes);

		$width=$wh[0];
		$height=$wh[1]; ///高度

		$this->Width=$width;
		$this->Height=$height;

		$this->Image = imageCreate( $width, $height );
		$image=$this->Image;

		///背景颜色
		$back = $this->_getColor2($this->_getColor( $this->BgColor));
		
		///填充背景 
		imagefilledrectangle($image, 0, 0, $width, $height, $back); 

		$TPadden=$this->TPadden;

		$basex=$this->Txbase;
		$color=null;
		
		///逐个画字符
		foreach ($codes as $v){
			$bound=$v[5];
			$color=$this->_getColor2($this->_getColor($v[2]));
			imagettftext($image, $v[0], $v[1], $basex, $bound['height'],$color , $v[4], $v[3]);
			
			///计算下一个左边距
			$basex=$basex+$bound['width']*$TPadden-$bound['left'];
		}
		
		 ///画干扰线
		$this->TLine ? $this->_wirteSinLine($color,$basex) : null;
		
		header("Content-type: image/png");
		imagepng( $image);
		imagedestroy($image);

	}

	/**
	 *通过字体角度得到字体矩形宽度*
	 *
	 * @param int $font_size 字体尺寸
	 * @param float $font_angle 旋转角度
	 * @param string $font_file 字体文件路径
	 * @param string $text 写入字符
	 * @return array 返回长宽高
	 */
	private function _calculateTextBox( $font_size, $font_angle, $font_file, $text ) {
		$box = imagettfbbox($font_size, $font_angle, $font_file, $text);

		$min_x = min(array($box[0], $box[2], $box[4], $box[6]));
		$max_x = max(array($box[0], $box[2], $box[4], $box[6]));
		$min_y = min(array($box[1], $box[3], $box[5], $box[7]));
		$max_y = max(array($box[1], $box[3], $box[5], $box[7]));

		return array(
			'left' => ($min_x >= -1) ? -abs($min_x + 1) : abs($min_x + 2),
			'top' => abs($min_y),
			'width' => $max_x - $min_x,
			'height' => $max_y - $min_y,
			'box' => $box
		);
	}

	//#ffffff
	private function  _getColor( $color ){
		return array(hexdec($color[1].$color[2]),hexdec($color[3].$color[4]),hexdec($color[5].$color[6]));
	}

	//#ffffff
	private function  _getColor2( $color ){
		return imagecolorallocate ($this->Image, $color[0], $color[1], $color[2]);
	}

	private function _getImageWH($data){
		
		if( $this->AutoSize ){
			
			$TPadden=$this->TPadden;
			$w=$this->Txbase;
			$h=0;
			
			foreach ($data as $v){
				$w=$w+$v[5]['width']*$TPadden-$v[5]['left'];
				$h=$h>$v[5]['height']?$h:$v[5]['height'];
			}
			
			return array(max($w,$this->Width),max($h,$this->Height));
			
		}else{
			
			return array( $this->Width, $this->Height );
			
		}
		
	}

	//画正弦干扰线
	private function _wirteSinLine($color,$w){
		$img=$this->Image;

		$h=$this->Height;
		$h1=rand(-5,5);
		$h2=rand(-1,1);
		$w2=rand(10,15);
		$h3=rand(4,6);

		for($i=-$w/2;$i<$w/2;$i=$i+0.1)
		{
			$y=$h/$h3*sin($i/$w2)+$h/2+$h1;
			imagesetpixel($img,$i+$w/2,$y,$color);
			$h2!=0?imagesetpixel($img,$i+$w/2,$y+$h2,$color):null;
		}
	}
}