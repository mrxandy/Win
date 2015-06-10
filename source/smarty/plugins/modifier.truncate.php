<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**  
 * Smarty truncate modifier plugin  (support chinese characters )
 *  
 * Type:     modifier<br>  
 * Name:     truncate<br>  
 * Purpose:  Truncate a string to a certain length if necessary,  
 *           optionally splitting in the middle of a word, and  
 *           appending the $etc string or inserting $etc into the middle.  
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php  
 *          truncate (Smarty online manual)  
 * @author   Monte Ohrt <monte at ohrt dot com>    
 * @modify by Chelin Tsien  
 * @param string  
 * @param integer  
 * @param string  
 * @param boolean  
 * @param boolean  
 * @return string  
 */
 
 /*
function smarty_modifier_truncate($string, $length = 80, $etc = '...',   
                                  $break_words = false, $middle = false)   
{    
    if ($length == 0)   
        return '';   
            
    if (_strlen($string) > $length) {   
        $length -= min($length, strlen($etc));   
          
        if (!$break_words && !$middle) {   
            $string = preg_replace('/\s+?(\S+)?$/', '', _substr($string, 0, $length+1));   
        }   
          
           
        if(!$middle) {   
         $temp_str = _substr($string, 0, $length);      
            return $temp_str . $etc;   
        } else {       
            return _substr($string, 0, $length/2) . $etc . _substr($string, -$length/2);   
        }   
    } else {   
        return $string;   
    }   
}
*/

function smarty_modifier_truncate($string, $length = 80, $etc = '...',
                                    $break_words = false, $middle = false)
{
     if ($length == 0)
          return '';

      if (strlen($string) > $length) {
          $length -= min($length, strlen($etc));
          for($i = 0; $i < $length ; $i++) {
     $strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
    }
    return $strcut.$etc;  
        
      } else {
          return $string;
      }
}


  
/*  
 * Returns the portion of string specified by the start and length parameters (support chinese characters )
 * @author   Chelin Tsien  
 * @param string  
 * @param integer  
 * @param integer  
 * @return string  
 */  
function _substr($string, $start, $length = false) {   
    $flag = preg_match_all("/[\x{0800}-\x{ffff}\w !\"#$%&'()*+,-.\/:;<=>?@[\\]^_`{|}~\s]{1}/u", $string, $match);   
    if($flag) {
    	 $input = $match[0];      
         $str = '';   
         $str_arr = array_slice($input,$start, $length); 
         $str = implode('', $str_arr);
         return $str;   
    }   
}   
  
/*  
 * Return the length of the given string (support chinese characters )
 * @author  Chelin Tsien  
 * @param string  
 * @param integer  
 * @param integer  
 * @return string  
 */  
function _strlen($string) {   
     $tmp_string = preg_replace("/[\x{0800}-\x{ffff}\w !\"#$%&'()*+,-.\/:;<=>?@[\\]^_`{|}~\s]{1}/u", '*', $string);       
     $tmp_length = strlen($tmp_string);   
     return (int)$tmp_length;   
}

/* vim: SET expandtab: */

?>
