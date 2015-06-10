<?php

/**
 * Cart
 * 
 * 购物车类
 * 
 * @author  doodoo<pWtitle@yahoo.com.cn>
 * @package     Cart
 * @category    Cart
 * @license     PHP License
 * @access      public
 * @version     $Revision: 1.10 $
 */
 
class Cart{

	var $cart;
	var $totalCount; //商品总数量
	var $totalPrices; //商品总金额

	/**
	* Cart Constructor
	* 
	* 类的构造函数，使购物车保持稳定的初始化状态 
	* 
	* @static 
	* @access  public 
	* @return  void   无返回值
	* @param   void   无参数
	*/
	function Cart(){		
		$this->totalCount = 0;
		$this->totalPrice = 0;
		$this->cart = array();
	}
 
 // }}}
    // {{{ add($item)

    /**
	* 增加商品到当前购物车
	*
    * @access public
    * @param  array $item 商品信息（一维数组：array(商品ID,商品名称,商品单价,商品数量)）
    * @return array   返回当前购物车内商品的数组
    */
	function add($item){
		if(!is_array($item)||is_null($item)) return $this->cart;
		if(!is_numeric(end($item))||(!is_numeric(prev($item)))) {
			echo "价格和数量必须是数字";
			return $this->cart;
		}
		reset($item); //这一句是必须的，因为上面的判断已经移动了数组的指标
		$key = current($item);
		if($key=="") return $this->cart;
		if($this->_isExists($key)){  //商品是否已经存在？
			$this->cart[$key]['count']  = end($item);
			return $this->cart;
		}

		$this->cart[$key]['ID']  = $key;
		$this->cart[$key]['name'] = next($item);
		$this->cart[$key]['price'] = next($item);
		$this->cart[$key]['count'] = next($item);

		return $this->cart;
	}

 // }}}
    // {{{ add($item)

    /**
	* 从当前购物车中取出部分或全部商品
	* 当 $key=="" 的时候，清空当前购物车
	* 当 $key!=""&&$count=="" 的时候，从当前购物车中拣出商品ID号为 $key 的全部商品 
	* 当 $key!=""&&$count!="" 的时候，从当前购物车中拣出 $count个 商品ID号为 $key 的商品 
	*
    * @access public
    * @param  string $key 商品ID
    * @return mixed   返回真假或当前购物车内商品的数组
    */
	function remove($key="",$count=""){
		if($key=="") {
			$this->cart = array();
			return true;
		}
		if(!array_key_exists($key,$this->cart)) return false;
		if($count==""){ //移去这一类商品
			unset($this->cart[$key]);
		}else{ //移去$count个商品
			$this->cart[$key]['count'] -= $count;
			if($this->cart[$key]['count']<=0) unset($this->cart[$key]);
		}
		return $this->cart;
	}

 // }}}
    // {{{ modi($key,$value)

    /**
	* 修改购物车内商品ID为 $key 的商品的数量为 $value
	*
    * @access public
    * @param  string $key 商品ID
    * @param  int $value 商品数量
    * @return array  返回当前购物车内商品的数组;
    */
	function modi($key,$value){
		if(!$this->_isExists($key)) return $this->cart();  //不存在此商品，直接返回
		if($value<=0){     // value 太小，全部删除
			unset($this->cart[$key]);
			return $this->cart;
		}
		$this->cart[$key]['count'] = $value;
		return $this->cart;
	}


    /**
	* 返回当前购物车内商品的数组
	*
    * @access public
    * @return array  返回当前购物车内商品的数组;
    */
	function getCart(){
		return $this->cart;
	}

 // }}}
    // {{{ _isExists($key)

    /**
	* 判断当前购物车中是否存在商品ID号为$key的商品
	*
    * @access private
    * @param  string $key 商品ID
    * @return bool   true or false;
    */
	function _isExists($key){
		if(isset($this->cart[$key])&&!empty($this->cart[$key])&&array_key_exists($key,$this->cart))
		return true;
		return false;
	}

 // }}}
    // {{{ isEmpty()

    /**
	* 判断当前购物车是否为空，即没有任何商品
	*
    * @access public
    * @return bool   true or false;
    */
	function isEmpty(){
		return !count($this->cart);
	}

 // }}}
    // {{{ _stat()

    /**
	* 取得部分统计信息
	*
    * @access private
    * @return bool  true or false;
    */
	function _stat(){
		if($this->isEmpty()) return false;
		foreach($this->cart as $item){
			$this->totalCount   = @end($item);
			$this->totalPrices  = @prev($item);
		}
		return true;
	}

 // }}}
    // {{{ totalPrices()

    /**
	* 取得当前购物车所有商品的总金额
	*
    * @access public
    * @return float  返回金额;
    */
	function totalPrices(){
		if($this->_stat())
		return $this->totalPrices;
		return 0;
	}

 // }}}
    // {{{ isEmpty()

    /**
	* 取得当前购物车所有商品的总数量和
	*
	* @access public
	* @return int ;
    */
	function totalCount(){
		if($this->_stat())
		return $this->totalCount;  
		return 0;
	}


}//End Class Cart
