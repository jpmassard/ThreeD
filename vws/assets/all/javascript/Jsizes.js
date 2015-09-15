/**
 * JSizes 0.33
 * https://github.com/bramstein/jsizes
 * 27. October 2013
 */
(function(c){var d=function(d){return parseInt(d,10)||0};c.each(["min","max"],function(e,a){c.fn[a+"Size"]=function(b){var c;if(b)void 0!==b.width&&this.css(a+"-width",b.width),void 0!==b.height&&this.css(a+"-height",b.height);else return b=this.css(a+"-width"),c=this.css(a+"-height"),{width:"max"===a&&(void 0===b||"none"===b||-1===d(b))&&Number.MAX_VALUE||d(b),height:"max"===a&&(void 0===c||"none"===c||-1===d(c))&&Number.MAX_VALUE||d(c)};return this}});c.fn.isVisible=function(){return this.is(":visible")};c.each(["border","margin","padding"],function(e,a){c.fn[a]=function(b){if(b)void 0!==b.top&&this.css(a+"-top"+("border"===a?"-width":""),b.top),void 0!==b.bottom&&this.css(a+"-bottom"+("border"===a?"-width":""),b.bottom),void 0!==b.left&&this.css(a+"-left"+("border"===a?"-width":""),b.left),void 0!==b.right&&this.css(a+"-right"+("border"===a?"-width":""),b.right);else return{top:d(this.css(a+"-top"+("border"===a?"-width":""))),bottom:d(this.css(a+"-bottom"+("border"===a?"-width":""))),left:d(this.css(a+"-left"+("border"===a?"-width":""))),right:d(this.css(a+"-right"+("border"===a?"-width":"")))};return this}})})(jQuery);

/**
 * jQuery Mouse Wheel Plugin 3.1.3
 * https://github.com/brandonaaron/jquery-mousewheel
 * 27. October 2013
 */
(function(c){"function"===typeof define&&define.amd?define(["jquery"],c):"object"===typeof exports?module.exports=c:c(jQuery)})(function(c){function m(b){var a=b||window.event,g=[].slice.call(arguments,1),d=0,e=0,h=0,f=0,f=0;b=c.event.fix(a);b.type="mousewheel";a.wheelDelta&&(d=a.wheelDelta);a.detail&&(d=-1*a.detail);a.deltaY&&(d=h=-1*a.deltaY);a.deltaX&&(e=a.deltaX,d=-1*e);void 0!==a.wheelDeltaY&&(h=a.wheelDeltaY);void 0!==a.wheelDeltaX&&(e=-1*a.wheelDeltaX);f=Math.abs(d);if(!l||f<l)l=f;f=Math.max(Math.abs(h),Math.abs(e));if(!k||f<k)k=f;a=0<d?"floor":"ceil";d=Math[a](d/l);e=Math[a](e/k);h=Math[a](h/k);g.unshift(b,d,e,h);return(c.event.dispatch||c.event.handle).apply(this,g)}var n=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],g="onwheel"in document||9<=document.documentMode?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],l,k;if(c.event.fixHooks)for(var p=n.length;p;)c.event.fixHooks[n[--p]]=c.event.mouseHooks;c.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var b=g.length;b;)this.addEventListener(g[--b],m,!1);else this.onmousewheel=m},teardown:function(){if(this.removeEventListener)for(var b=g.length;b;)this.removeEventListener(g[--b],m,!1);else this.onmousewheel=null}};c.fn.extend({mousewheel:function(b){return b?this.bind("mousewheel",b):this.trigger("mousewheel")},unmousewheel:function(b){return this.unbind("mousewheel",b)}})});

