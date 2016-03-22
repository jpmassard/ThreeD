/**
 * menu-handler.js
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 */
(function(k,a){var f,g,b;f=a.getElementById("layout");g=a.getElementById("menu");b=a.getElementById("menuLink");var e=function(h,a){var c,b,d;c=h.className.split(/\s+/);b=c.length;for(d=0;d<b;d++)if(c[d]===a){c.splice(d,1);break}b===c.length&&c.push(a);h.className=c.join(" ")};b.onclick=function(a){a.preventDefault();e(f,"active");e(g,"active");e(b,"active")}})(this,this.document);