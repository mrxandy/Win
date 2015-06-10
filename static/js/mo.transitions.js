FX.transitions.quadIn = function(t, b, c, d){
	return c*(t/=d)*t + b;
};
	
FX.transitions.quadOut = function(t, b, c, d){
	return -c *(t/=d)*(t-2) + b;
};

FX.transitions.quadInOut = function(t, b, c, d){
	if((t/=d/2) < 1) return c/2*t*t + b;
	return -c/2 *((--t)*(t-2) - 1) + b;
};  

FX.transitions.cubicIn = function(t, b, c, d){
	return c*(t/=d)*t*t + b;
}; 

FX.transitions.cubicOut = function(t, b, c, d){
	return c*((t=t/d-1)*t*t + 1) + b;
}; 

FX.transitions.cubicInOut = function(t, b, c, d){
	if((t/=d/2) < 1) return c/2*t*t*t + b;
	return c/2*((t-=2)*t*t + 2) + b;
};

FX.transitions.quartIn = function(t, b, c, d){
	return c*(t/=d)*t*t*t + b;
};

FX.transitions.quartOut = function(t, b, c, d){
	return -c *((t=t/d-1)*t*t*t - 1) + b;
};

FX.transitions.quartInOut = function(t, b, c, d){
	if((t/=d/2) < 1) return c/2*t*t*t*t + b;
	return -c/2 *((t-=2)*t*t*t - 2) + b;
};
	
FX.transitions.quintIn = function(t, b, c, d){
	return c*(t/=d)*t*t*t*t + b;
}; 

FX.transitions.quintOut = function(t, b, c, d){
	return c*((t=t/d-1)*t*t*t*t + 1) + b;
};  

FX.transitions.quintInOut = function(t, b, c, d){
	if((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
	return c/2*((t-=2)*t*t*t*t + 2) + b;
}; 

FX.transitions.expoIn = function(t, b, c, d){
	return(t==0) ? b : c * Math.pow(2, 10 *(t/d - 1)) + b - c * 0.001;
}; 

FX.transitions.expoOut = function(t, b, c, d){
	return(t==d) ? b+c : c * 1.001 *(-Math.pow(2, -10 * t/d) + 1) + b;
}; 

FX.transitions.expoInOut = function(t, b, c, d){
	if(t==0) return b;
	if(t==d) return b+c;
	if((t/=d/2) < 1) return c/2 * Math.pow(2, 10 *(t - 1)) + b - c * 0.0005;
	return c/2 * 1.0005 *(-Math.pow(2, -10 * --t) + 2) + b;
}; 

FX.transitions.circIn = function(t, b, c, d){
	return -c *(Math.sqrt(1 -(t/=d)*t) - 1) + b;
}; 

FX.transitions.circOut = function(t, b, c, d){
	return c * Math.sqrt(1 -(t=t/d-1)*t) + b;
}; 
	
FX.transitions.circInOut = function(t, b, c, d){
	if((t/=d/2) < 1) return -c/2 *(Math.sqrt(1 - t*t) - 1) + b;
	return c/2 *(Math.sqrt(1 -(t-=2)*t) + 1) + b;
}; 

FX.transitions.backIn = function(t, b, c, d, s){
	s = s || 1.70158;
	return c * (t /= d) * t * ((s + 1) * t - s) + b;
};

FX.transitions.backOut = function(t, b, c, d, s){
	s = s || 1.70158;
	return c * ((t = t / d - 1) * t * ((s + 1) * t + s) + 1) + b;
};

FX.transitions.backBoth = function(t, b, c, d, s){
	s = s || 1.70158;
	if((t /= d / 2 ) < 1){
		return c / 2 * (t * t * (((s *= (1.525)) + 1) * t - s)) + b;
	}
	return c / 2 * ((t -= 2) * t * (((s *= (1.525)) + 1) * t + s) + 2) + b;
};

FX.transitions.elasticIn = function (t, b, c, d, a, p){
	if(t == 0){
		return b;
	}
	if((t /= d) == 1){
		return b+c;
	}
	if(!p){
		p=d*.3;
	}      
	if(!a || a < Math.abs(c)){
		a = c; 
		var s = p/4;
	}else{
		var s = p/(2*Math.PI) * Math.asin(c/a);
	}  
	return -(a*Math.pow(2,10*(t-=1)) * Math.sin((t*d-s)*(2*Math.PI)/p)) + b;
};

FX.transitions.elasticOut = function (t, b, c, d, a, p){
	if(t == 0){
		return b;
	}
	if((t /= d) == 1){
		return b+c;
	}
	if(!p){
		p=d*.3;
	}
	if(!a || a < Math.abs(c)){
		a = c;
		var s = p / 4;
	}else{
		var s = p/(2*Math.PI) * Math.asin(c/a);
	}
	return a*Math.pow(2,-10*t) * Math.sin((t*d-s)*(2*Math.PI)/p) + c + b;
};
    
FX.transitions.elasticBoth = function (t, b, c, d, a, p){
	if(t == 0){
		return b;
	}
	if((t /= d/2) == 2 ){
		return b+c;
	}      
	if(!p){
		p = d*(.3*1.5);
	}
	if(!a || a < Math.abs(c)){
		a = c; 
		var s = p/4;
	}else{
		var s = p/(2*Math.PI) * Math.asin(c/a);
	}     
	if(t < 1){
		return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin((t*d-s)*(2*Math.PI)/p)) + b;
	}
	return a*Math.pow(2,-10*(t-=1)) * Math.sin((t*d-s)*(2*Math.PI)/p)*.5 + c + b;
};

FX.transitions.backIn = function (t, b, c, d, s){
	if(typeof s == 'undefined'){
		s = 1.70158;
	}
	return c*(t/=d)*t*((s+1)*t - s) + b;
};

FX.transitions.backOut = function (t, b, c, d, s){
	if(typeof s == 'undefined'){
		s = 1.70158;
	}
	return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
};
    
FX.transitions.backBoth = function (t, b, c, d, s){
	if(typeof s == 'undefined'){
		s = 1.70158; 
	}
	if((t /= d/2 ) < 1){
		return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
	}
	return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
};

FX.transitions.bounceIn = function (t, b, c, d){
	return c - FX.transitions.bounceOut(d-t, 0, c, d) + b;
};

FX.transitions.bounceOut = function (t, b, c, d){
	if((t/=d) < (1/2.75)){
		return c*(7.5625*t*t) + b;
	}else if(t < (2/2.75)){
		return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
	}else if(t < (2.5/2.75)){
		return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
	}
	return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
};

FX.transitions.bounceBoth = function (t, b, c, d){
	if(t < d/2){
		return FX.transitions.bounceIn(t*2, 0, c, d) * .5 + b;
	}
	return FX.transitions.bounceOut(t*2-d, 0, c, d) * .5 + c*.5 + b;
};

