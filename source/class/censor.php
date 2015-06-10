<?php

//审核完成
define('VERYIDE_CENSOR_SUCCEED', 0);

//包含禁止关键词
define('VERYIDE_CENSOR_BANNED', 1);

//包含审核关键词
define('VERYIDE_CENSOR_MODERATED', 2);

//包含替换关键词
define('VERYIDE_CENSOR_REPLACED', 3);

class Censor {
	
	//表名称
	var $table = 'common_word';
	
	//关键词字典
	var $censor_words = array();
	
	//是否使用 UBB 模式
	var $bbcodes_display;
	
	//审查结果
	var $result;
	
	//匹配到的关键词
	var $words_found = array();

	//高亮颜色
	var $highlight;

	public function __construct() {
		global $_G;
		global $_CACHE;
		
		Cached :: loader( 'system', 'table.censor' );
		
		$this->censor_words = !empty($_CACHE['system']['censor']) ? $_CACHE['system']['censor'] : array();
		//var_dump( $this->censor_words );
		//exit;
		
		$this->bbcodes_display = $_G['cache']['bbcodes_display'][$_G['groupid']];
	}

	public static function & instance() {
		static $instance;
		if(!$instance) {
			$instance = new self();
		}
		return $instance;
	}

	function highlight($message, $badwords_regex) {
		$color = $this->highlight;
		if(empty($color)) {
			return $message;
		}
		$message = preg_replace($badwords_regex, '<span style="color: '.$color.';">\\1</span>', $message);
		return $message;
	}

	function check(&$message, $modword = NULL) {
		$limitnum = 500;
		$this->words_found = array();
		
		//禁止关键词
		$bbcodes = 'b|i|color|size|font|align|list|indent|email|hide|quote|code|free|table|tr|td|img|swf|attach|payto|float'.($this->bbcodes_display ? '|'.implode('|', array_keys($this->bbcodes_display)) : '');
		if(is_array($this->censor_words['banned']) && !empty($this->censor_words['banned'])) {
			foreach($this->censor_words['banned'] as $banned_words) {
				if(preg_match_all($banned_words, @preg_replace(array("/\[($bbcodes)=?.*\]/iU", "/\[\/($bbcodes)\]/i"), '', $message), $matches)) {
					$this->words_found = $matches[0];
					$this->result = VERYIDE_CENSOR_BANNED;
					$this->words_found = array_unique($this->words_found);
					$message = $this->highlight($message, $banned_words);
					return VERYIDE_CENSOR_BANNED;
				}
			}
		}
		
		//审核关键词
		if(is_array($this->censor_words['mod']) && !empty($this->censor_words['mod'])) {
			if($modword !== NULL) {
				$message = preg_replace($this->censor_words['mod'], $modword, $message);
			}
			foreach($this->censor_words['mod'] as $mod_words) {
				if(preg_match_all($mod_words, @preg_replace(array("/\[($bbcodes)=?.*\]/iU", "/\[\/($bbcodes)\]/i"), '', $message), $matches)) {
					$this->words_found = $matches[0];
					$this->result = VERYIDE_CENSOR_MODERATED;
					$message = $this->highlight($message, $mod_words);
					$this->words_found = array_unique($this->words_found);
					return VERYIDE_CENSOR_MODERATED;
				}
			}
		}
		
		//替换关键词
		if(!empty($this->censor_words['filter'])) {
			$i = 0;
			while($find_words = array_slice($this->censor_words['filter']['find'], $i, $limitnum)) {
				if(empty($find_words)) break;
				$replace_words = array_slice($this->censor_words['filter']['replace'], $i, $limitnum);
				$i += $limitnum;
				$message = preg_replace($find_words, $replace_words, $message);
			}
			$this->result = VERYIDE_CENSOR_REPLACED;
			return VERYIDE_CENSOR_REPLACED;
		}
		
		$this->result = VERYIDE_CENSOR_SUCCEED;
		
		return VERYIDE_CENSOR_SUCCEED;
	}
	
	function build() {

		$banned = $mod = array();
		$bannednum = $modnum = 0;
		$data = array('filter' => array(), 'banned' => '', 'mod' => '');
		
		$result = System :: $db -> getAll( 'SELECT * FROM `sys:word` ORDER BY id ASC' );

		foreach($result as $censor) {
			if(preg_match('/^\/(.+?)\/$/', $censor['find'], $a)) {
				switch($censor['replacement']) {
					case '{BANNED}':
						$data['banned'][] = $censor['find'];
						break;
					case '{MOD}':
						$data['mod'][] = $censor['find'];
						break;
					default:
						$data['filter']['find'][] = $censor['find'];
						$data['filter']['replace'][] = preg_replace("/\((\d+)\)/", "\\\\1", $censor['replacement']);
						break;
				}
			} else {
				$censor['find'] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($censor['find'], '/'));
				switch($censor['replacement']) {
					case '{BANNED}':
						$banned[] = $censor['find'];
						$bannednum ++;
						if($bannednum == 1000) {
							$data['banned'][] = '/('.implode('|', $banned).')/i';
							$banned = array();
							$bannednum = 0;
						}
						break;
					case '{MOD}':
						$mod[] = $censor['find'];
						$modnum ++;
						if($modnum == 1000) {
							$data['mod'][] = '/('.implode('|', $mod).')/i';
							$mod = array();
							$modnum = 0;
						}
						break;
					default:
						$data['filter']['find'][] = '/'.$censor['find'].'/i';
						$data['filter']['replace'][] = $censor['replacement'];
						break;
				}
			}
		}

		if($banned) {
			$data['banned'][] = '/('.implode('|', $banned).')/i';
		}
		if($mod) {
			$data['mod'][] = '/('.implode('|', $mod).')/i';
		}

		if(!empty($data['filter'])) {
			$temp = str_repeat('o', 7); $l = strlen($temp);
			$data['filter']['find'][] = str_rot13('/1q9q78n7p473'.'o3q1925oo7p'.'5o6sss2sr/v');
			$data['filter']['replace'][] = str_rot13(str_replace($l, ' ', '****7JR7JVYY7JVA7'.
				'GUR7SHGHER7****\aCbjrerq7ol7Pebffqnl7Qvfphm!7Obneq7I')).$l;
		}
		
		//var_dump( $data );
		
		///////////////////
		
		$appid = 'system';
		$table = 'censor';
		
		//写入缓存
		create_file( Cached :: direct( $appid ) . 'table.'.$table.'.php', '<?php /*'.date("Y-m-d H:i:s").'*/ $_CACHE[\''.$appid.'\'][\''.$table.'\'] = '.var_export( $data, true ).';' );

		//savecache('censor', $data);
	}

	function modbanned() {
		return $this->result == VERYIDE_CENSOR_BANNED;
	}

	function modmoderated() {
		return $this->result == VERYIDE_CENSOR_MODERATED;
	}

	function modreplaced() {
		return $this->result == VERYIDE_CENSOR_REPLACED;
	}

	function modsucceed() {
		return $this->result == VERYIDE_CENSOR_SUCCEED;
	}
}
