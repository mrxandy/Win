(function (window, doc, undefined) {
	function pinLayout(node) {
	   var indicator = arguments.callee, that = this, Pb, Pr;
	   if (!(this instanceof indicator)) return new indicator(node);
	   typeof node == 'string' ? node = doc.getElementById(node) : node;
	   if (!node || node.nodeType !== 1) {
		   return
	   }
	   Pb = indicator.prototype;
	   Pb.constructor = indicator;
	   Pb.node = node;
	   Pb.init = function () {
		   var temp = [
					   ['pins', 'h', 'node', 'minH', 'pinW', 'pinH', 'n'],
					   [
						   null,
						   [],
						   node,
						   0,
						   0,
						   0,
						   n = 0,
					   ]
				   ],
				   i = temp[0].length;
		   while (i--) {
			   this[temp[0][i]] = temp[1][i];
		   }
		   //console.log(this.n,this.pinW);
		   return this;
	   }
	   Pb.setValue = function () {
		   //if (node.children.length === 0) return
		   this.pins = node.children;
		   
		   //第一个格子的尺寸
		   this.pinH = this.pins[0].offsetHeight;
		   this.pinW = this.pins[0].offsetWidth;
		   
		   //每行可容纳格子数
		   this.n = node.offsetWidth / this.pinW | 0;
		   //this.node.style.width = this.n * this.pinW + 'px';
		   //console.log(this.pins + ' ' + this.pinH + ' ' + this.pinW + ' ' + this.n);
		   this.initEnd = true;
		   return this;
	   }
	   Pb.fixTop = function () {
		   //if (!this.pins || this.pins.length === 0)return;
		   this.initEnd || this.setValue();
		   var i = 0, nodes = this.pins, len = nodes.length >>> 0, index;
		   for (; i < len;
				  this.pinH = nodes[i].offsetHeight,
					  //console.log(this.pinH),
						  i < this.n ? (
								  this.h[i] = this.pinH,
										  nodes[i].style.cssText = 'opacity:1;' + this.fixCSS('transition', 'all 1s ease-in')
								  ) : (

								  this.minH = Math.min.apply(null, this.h),
										  index = this.indexOf(this.h, this.minH),
										  this.h[index] += this.pinH,
									  /*nodes[i].style.position = 'absolute',
									   nodes[i].style.top = this.minH + 'px',
									   nodes[i].style.left = (index * this.pinW) + 'px',
									   nodes[i].style.opacity =*/
										  nodes[i].style.cssText = 'position:absolute;top:' + this.minH + 'px;left:' + (index * this.pinW) + 'px;opacity:1;'+this.fixCSS('transition', 'opacity .8s ease-in')
								  ),
						  i++
				   ) {
		   }
		   //console.log( Pb.getMax( this.h ) );
		   return this;
	   }
	   Pb.fixCSS = function (a, p) {
		   return  '-moz-#:@;-ms-#:@;-o-#:@;-webkit-#:@;#:@;'.replace(/#:@/g, a + ':' + p);
	   }
	   Pb.indexOf = function (a, b) {
		   var i = a.length >>> 0;
		   for (; i--;) {
			   if (a[i] === b) {
				   return i
			   }
		   }
		   return null;
	   }
	   Pb.getMin = function(array)
		{
			return Math.min.apply(Math,array);
		}
		Pb.getMax = function(array)
		{
			return Math.max.apply(Math,array);
		}
	   
	   //console.log(this);
	   return this.init(), this;
   }
   window.pinLayout = pinLayout;
})(this, document)
