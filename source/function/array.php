<?php

/*
*	Copyright VeryIDE 2009-2012
*	http://www.veryide.com/
*
*	Array 修正函数包
*
*	$Id: array.php,v2.0 14:01 2009-03-31 Lay $
*/

/*
	将 POST 过来的数组重新索引
*/
function array_post_index( $array, $clean = '' ){
	
	$keys = array_keys( $array );
	$vals = array_values( $array );
	
	$news = array();
	
	if( isset( $vals[0] ) ){
		
		foreach( $vals[0] as $k => $v ){
	
			$news[$k] = array();
			
			foreach( $keys as $i => $n ){
				
				$news[$k][$n] = $vals[$i][$k];
				
				//忽略空值
				if( $clean && $clean == $n && !$news[$k][$n] ){
					unset( $news[$k] );
					continue 2;
				}
				
			}
			
		}
		
	}
	
	return $news;
	
}

//获取多维数组下特定键下的值，并生成一维数组
function getall_by_key(array $arr, $key){  
	if (!trim($key)) return false;  
	preg_match_all("/\"$key\";\w{1}:(?:\d+:|)(.*?);/", serialize($arr), $output);
	foreach( $output[1] as $i => $val ){
		if( strpos( $val, '"' ) === 0 ) $output[1][$i] = substr( $val, 1, -1 );
	}
	return $output[1];  
}

/**
 * 获取数组中某键对应的索引值
 * @author Nate Ferrero
 */
function array_key_index(&$arr, $key) {
	if( array_key_exists( $key, $arr ) ){
		$i = 0;
		foreach( array_keys($arr) as $k ) {
			if($k == $key) return $i;
			$i++;
		}
	}else{
		return false;
	}
}

function array_insert(&$array, $position, $insert_array) { 
  $first_array = array_splice ($array, 0, $position); 
  $array = array_merge ($first_array, $insert_array, $array); 
} 

//从小到大排序
function sort_asc($a,$b){
	if( $a["stat"] == $b["stat"] ){
		return 0;
	}else{
		return ($a["stat"]<$b["stat"]) ? -1 : 1;
	}
}

//从大到小排序
function sort_des($a,$b){
	if( $a["stat"] == $b["stat"] ){
		return 0;
	}else{
		return ($a["stat"]>$b["stat"]) ? -1 : 1;
	}
}

function array_htmlspecialchars( &$val, $key ){
	$val = dhtmlspecialchars( $val );
}

/*
	将数组根据不同键和排序方式进行排序
	$tab	数组
	$key	键
	$sort	排序方式
*/
function multi_sort( &$tab, $key, $sort = 'asc' ){ 
	$compare = create_function('$a,$b','if ($a["'.$key.'"] == $b["'.$key.'"]) {return 0;}else {return ($a["'.$key.'"] '.( $sort == 'asc' ? '<' : '>' ).' $b["'.$key.'"]) ? -1 : 1;}'); 
	uasort( $tab, $compare ) ; 
}

//随机打乱数组并保持索引
function rand_array( $array ){
	
	//重建数组
	$temp = array();
	
	//数组长度
	$leng = count($array);
	
	//遍历数组
	for( $i=0; $i<$leng; $i++ ) {
		
		//随机索引
		$rand = array_rand($array);
		
		//重复索引
		if( isset($temp[$rand]) ){
			$i--;
			continue;
		}
		
		//新增到数组
		$temp[$rand] = $array[$rand];
		
		//删除原索引
		//array_splice($array, array_index($array,$rand) , 1);

	}
	
	return $temp;
}

/*
	从数组中随机抽取定量的数据
	$array	原数组
	$size	截取长度
*/
function array_limit( $array, $size ){
	
	//临时数组
	$temp = array();
	
	$rand = array_rand( $array, $size );
	
	if( is_array( $rand ) ){
		
		foreach( $rand as $id ){
			$temp[$id] = $array[$id];	
		}
		
	}else{
		$temp[$rand] = $array[$rand];
	}
	
	return $temp;
	
}

/*
	清理数组中空白键
	$array		原始数组
*/
function clear_array( $array ){
	
	foreach( $array as $key => $val ){
		if( !$val ){
			unset( $array[$key] );
		}
	}
	
	return $array;
	
}

function array_var_convert( $text, $var, $key = null ){    
    
    if( !is_array( $key ) ){        
        $text = str_replace(  array_keys( $var ) , array_values( $var ), $text );        
    }else{        
        $text = str_replace(  $var , $key, $text );        
    }
    
    return $text;
    
}

//////////////////////////////////

/** 
 * xml2array() will convert the given XML text to an array in the XML structure. 
 * Link: http://www.bin-co.com/php/scripts/xml2array/ 
 * Arguments : $contents - The XML text 
 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value. 
 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance. 
 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure. 
 * Examples: $array =  xml2array(file_get_contents('feed.xml')); 
 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute')); 
 */ 
function xml_array($contents, $get_attributes=1, $priority = 'tag') { 
    if(!$contents) return array(); 

    if(!function_exists('xml_parser_create')) { 
        //print "'xml_parser_create()' function not found!"; 
        return array(); 
    } 

    //Get the XML parser of PHP - PHP must have this module for the parser to work 
    $parser = xml_parser_create(''); 
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
    xml_parse_into_struct($parser, trim($contents), $xml_values); 
    xml_parser_free($parser); 

    if(!$xml_values) return;//Hmm... 

    //Initializations 
    $xml_array = array(); 
    $parents = array(); 
    $opened_tags = array(); 
    $arr = array(); 

    $current = &$xml_array; //Refference 

    //Go through the tags. 
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
    foreach($xml_values as $data) { 
        unset($attributes,$value);//Remove existing values, or there will be trouble 

        //This command will extract these variables into the foreach scope 
        // tag(string), type(string), level(int), attributes(array). 
        extract($data);//We could use the array by itself, but this cooler. 

        $result = array(); 
        $attributes_data = array(); 
         
        if(isset($value)) { 
            if($priority == 'tag') $result = $value; 
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
        } 

        //Set the attributes too. 
        if(isset($attributes) and $get_attributes) { 
            foreach($attributes as $attr => $val) { 
                if($priority == 'tag') $attributes_data[$attr] = $val; 
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
            } 
        } 

        //See tag status and do the needed. 
        if($type == "open") {//The starting of the tag '<tag>' 
            $parent[$level-1] = &$current; 
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
                $current[$tag] = $result; 
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 

                $current = &$current[$tag]; 

            } else { //There was another element with the same tag name 

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                    $repeated_tag_index[$tag.'_'.$level]++; 
                } else {//This section will make the value an array if multiple tags with the same name appear together 
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array 
                    $repeated_tag_index[$tag.'_'.$level] = 2; 
                     
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                        $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                        unset($current[$tag.'_attr']); 
                    } 

                } 
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
                $current = &$current[$tag][$last_item_index]; 
            } 

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
            //See if the key is already taken. 
            if(!isset($current[$tag])) { //New Key 
                $current[$tag] = $result; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 

            } else { //If taken, put all things inside a list(array) 
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 

                    // ...push the new element into that array. 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                     
                    if($priority == 'tag' and $get_attributes and $attributes_data) { 
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; 

                } else { //If it is not an array... 
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value 
                    $repeated_tag_index[$tag.'_'.$level] = 1; 
                    if($priority == 'tag' and $get_attributes) { 
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                             
                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                            unset($current[$tag.'_attr']); 
                        } 
                         
                        if($attributes_data) { 
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                        } 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
                } 
            } 

        } elseif($type == 'close') { //End of tag '</tag>' 
            $current = &$parent[$level-1]; 
        } 
    } 
     
    return($xml_array); 
}  


?>