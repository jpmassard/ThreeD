(function(){VWS={PRODUCT:"VWS Framework",VERSION:"1.0.603",PATH:"plugins/ThreeD/vws/",CLASSES:["Modules.js","Classes.js"],base:{},display:{Item_clickDuration:250,Item_resizeRefresh:150,Stage_tabletHeightIncrease:270},loader:{VideoInfo_resolutions:"240p 360p 480p 540p 720p 1080p SD HD 2K 4K".split(" "),VideoLoader_seekThrottling:250},player:{initialStereoDisplay:"ARCF",multiPlayerDisplaySync:1E3,preEncBoost:{P:"NO-PRE",X:"NO-PRE",L:"L",KMQ:"NO-PRE",HOL:"NO-PRE",MIR:"NO-PRE",RILO:"NO-PRE",CILO:"NO-PRE",DI00:"NO-PRE",ARCF:"ARCF",AYBF:"AYBF",AGMF:"AGMF",TVP:"NO-PRE",TV:"NO-PRE",NVD:"NO-PRE"},
preEncBoostSwapped:{P:"NO-PRE",X:"NO-PRE",L:"R",KMQ:"NO-PRE",HOL:"NO-PRE",MIR:"NO-PRE",RILO:"NO-PRE",CILO:"NO-PRE",DI00:"NO-PRE",ARCF:"ACRF",AYBF:"ABYF",AGMF:"AMGF",TVP:"NO-PRE",TV:"NO-PRE",NVD:"NO-PRE"},realAnaglyphs:{ARCF:"ARCF",AYBF:"AYBF",AGMF:"AGMF"},ui:{autoHide:333,fadeEffect:0,layoutUnitSize:7}},s3d:{},timer:{},ui:{Slider_animateClick:250,Slider_changeOnKey:.01,Slider_changeOnWheel:.05},ASSETS:{loader:{VideoInfo:{provider:"assets/loader/VideoInfo/VideoInfo.php?video=#VIDEO#"}},
nullImage:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAB3RJTUUH3QcJBjkJ3ol/ZwAAAAlwSFlzAAALEgAACxIB0t1+/AAAAARnQU1BAACxjwv8YQUAAAALSURBVHjaY2AAAgAABQAB6frc2AAAAABJRU5ErkJggg=="},STRINGS:{4E3:"Close",4001:"Runtime Error",4100:"3D Video Player",4101:"Video Player",4102:"3D Image Viewer",4103:"Image Viewer",4104:"Video Starter",4105:"3D Video Starter",5E3:"VWS.Ajax:\nCan't get a HTTP request object from this browser.",5001:"VWS.Ajax:\nCannot load asset over the network.",
5002:"VWS.init:\nA required class or asset is missing. VWS Framework will not work. This web app may produce unexpected results and should be closed.",5050:"VWS.display.Canvas:\nWebGL and/or GPU hardware accelleration are not available or turned off in this browser.",5101:"VWS.loader.VideoLoader:\nVideo playback aborted.",5102:"VWS.loader.VideoLoader:\nNetwork streaming error.",5103:"VWS.loader.VideoLoader:\nVideo file appears corrupted.",5104:"VWS.loader.VideoLoader:\nVideo format unsupported or video file not found.",
5105:"VWS.loader.VideoLoader:\nUnknown video error!",5204:"VWS.loader.VideoInfo:\nVideo format unsupported or video file not found.",5301:"VWS.player.MultiDisplay:\nThe NVIDIA 3D Vision driver is missing.",5302:"VWS.player.MultiDisplay:\nThis display cannot show NVIDIA 3D Vision contents properly.",5303:"VWS.player.MultiDisplay:\nNVIDIA 3D Vision is currently disabled.",5320:"VWS.s3d.AnaglyphMuxer:\nSecurity error. The bitmap of the loaded media is tainted by cross-origin data.",5350:'VWS.player.S3dVideoPlayerApp:\nWrong stereo layout given. The stereo layout must be one of "P", "PA", "X", "XA", "OU", "OUA", "UO", "UOA", "KMQ", "NS".',
5351:'VWS.player.S3dImageViewerApp:\nWrong stereo layout given. The stereo layout must be one of "P", "PA", "X", "XA", "OU", "OUA", "UO", "UOA", "KMQ", "SQ", "SQR", "NS".',5401:"VWS.ui.FileChooser:\nThis browser does not support the HTML5 File API.",9999:"VWS Framework loaded and ready. To use, override VWS.START() method in JavaScript."},STATIC:{staging:void 0,storage:{},loZ:0,hiZ:0,licenseInfo:void 0,videoInfo:void 0},uid:function(){s4=function(){return(65536*(1+Math.random())|0).toString(16).substring(1)};return s4()+s4()+s4()},staging:function(a){return a?VWS.STATIC.staging.get(0):VWS.STATIC.staging},mobileDevice:function(){var a=navigator.userAgent;
return!!(/Android/i.test(a)||/iPad/i.test(a)||/iPhone/i.test(a)||/BlackBerry/i.test(a)||/webOS/i.test(a))},touchDevice:function(){return!!("ontouchstart"in window)||!!navigator.msMaxTouchPoints},alert:function(a,d,b,c){b=new VWS.ui.Dialog(b,c);a=a||VWS.PRODUCT+" "+VWS.VERSION;b.title(a);d=(d||"").replace(/\n/,"<br/>");b.content(d);b.show()},errorAlert:function(a){VWS.alert(VWS.STRINGS[4001]+"&nbsp;&nbsp;("+VWS.PRODUCT+"&nbsp;"+VWS.VERSION+")","#"+a+",&nbsp;"+VWS.STRINGS[a])},init:function(){$("html").append('<div id="vws-staging-area"></div>');VWS.STATIC.staging=$("#vws-staging-area");var a;try{a=VWS.PATH+"classes/"+VWS.CLASSES[0],VWS.Ajax.loadJS(a,!1),VWS.ClassLoader.fetch(),delete VWS.ClassLoader}
catch(b){throw b=VWS.STRINGS[5002],b;}VWS.Debug.addDebugFunctions()},START:function(){VWS.alert(null,VWS.STRINGS[9999])},Ajax:{httpRequest:function(){try{return new XMLHttpRequest}catch(a){}try{return new ActiveXObject("Msxml2.XMLHTTP")}catch(d){}try{return new ActiveXObject("Microsoft.XMLHTTP")}catch(b){}return null},loadData:function(a,d,b){d=d||"text/plain";b=b||function(a){return a};var c=VWS.Ajax.httpRequest();if(null===c)throw VWS.STRINGS[5E3];try{c.open("GET",a,!1),c.setRequestHeader("Accept",d),c.send(null)}catch(e){throw e=VWS.STRINGS[5001]+"\n("+e.name+": "+e.message+")",e;}if(200!==c.status)throw VWS.STRINGS[5001]+"\n("+c.status+": "+c.statusText+")";return b(c.responseText)},
loadJS:function(a,d,b){var c;c=VWS.uid();a=b?b:VWS.Ajax.loadData(a,"text/javascript");b='<script id="'+c+'" type="text/javascript">'+a+"\x3c/script>";VWS.staging().append(b);d||$("#"+c).remove();return a},loadJS2:function(a,d){var b=document.createElement("script");b.id=VWS.uid();b.type="text/javascript";b.addEventListener("load",d,!1);b.src=a;document.getElementsByTagName("head")[0].appendChild(b);return b.id},loadCSS:function(a,d){var b=document.createElement("link");b.rel="stylesheet";b.type="text/css";b.id=VWS.uid();b.href=a;var c;d&&(c=setTimeout(d,500));b.onload=function(){clearTimeout(c);d&&d()};document.getElementsByTagName("head")[0].appendChild(b)}},
Base64:{decode:function(a){var d,b,c,e,h,g,f,k;g=VWS.Base64.chars(0);e=VWS.Base64.chars(1);h=VWS.Base64.chars(2);f="";k=0;a=a.replace(e,"");for(a=a.replace(h,"");k<a.length;)d=g.indexOf(a.charAt(k++)),b=g.indexOf(a.charAt(k++)),e=g.indexOf(a.charAt(k++)),h=g.indexOf(a.charAt(k++)),d=d<<2|b>>4,b=(b&15)<<4|e>>2,c=(e&3)<<6|h,f+=String.fromCharCode(d),64!=e&&(f+=String.fromCharCode(b)),64!=h&&(f+=String.fromCharCode(c));return f},encode:function(a,d){if(!a)return a;var b,c,e,h,g,f,k,l,m=VWS.Base64.chars(),p=l=0;h="";var n=[];do{try{b=a.charCodeAt(l++)&255,c=a.charCodeAt(l++)&255,e=a.charCodeAt(l++)&255}catch(q){}k=b<<16|c<<8|e;h=k>>18&63;g=k>>12&63;f=k>>6&63;k&=63;
n[p++]=m.charAt(h)+m.charAt(g)+m.charAt(f)+m.charAt(k)}while(l<a.length);h=n.join("");l=a.length%3;b=(l?h.slice(0,l-3):h)+"===".slice(l||3);if(d){c="";for(l=0;l<b.length;l+=d)c+=b.substring(l,l+d)+"\n";return c}return b},chars:function(a){return 1==a?/[^A-Za-z0-9\+\/\=]/g:2==a?/64z32Y16x8W4v2U1t0/g:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="}},Debug:{addDebugFunctions:function(){window.trace=function(a){if(void 0!==a){var d=Array.prototype.slice.call(arguments);console.log(d.join(" "))}};window.enumerate=function(a){window.trace(VWS.Debug.enumObject(a))};window.clear=function(){console.clear()}},
enumObject:function(a,d){var b,c,e,h,g,f;b=[];f=0;for(c in a){try{e=a[c],h=typeof e,"string"==h?(100<e.length&&(e=e.substr(0,100)+" ..."),g=' = "'+e+'"'):g="number"==h||"boolean"==h?" = "+e:"",b[f]=c+" ("+h+")"+g}catch(k){}f++}b.sort();c="OBJECT ENUMERATION:\n";for(f=0;f<b.length;f++)c+=b[f]+"\n";d&&(c=c.replace(/\n/g,"<br/>"));return c},gibberish:function(a,d){a=a||1;d=d-1||5;var b,c,e,h,g,f,k,l;b="Pax Ipsum Dolor Mundi Lux Raritatum Castix Perbonum Nolo Lorem Talus Alivia Carpi Per Prosecundum Poena Cis Etaris Geocuri Mobilitur Sic Hic Caloriam Immortalis Ex Pos Astaria".split(" ");
c="ipsum pecunia mundi lux raritatum aluviae est prosecundum carpus trelinium ars artis danubiae etaris et ex ilius larinium mons lorem naturalis opisticum per quam resinus sagitario talus uranis ver aquae bebit cis sutum pane geocurum immobilis multum sic in hic quadrantur caloribus bibliae immortalis os pinum pentilis qui noquitur steres phonum de".split(" ");e="..!?...!?.....!?".split("");g="";f=VWS.Math.random;for(k=1;k<=a;k++){h=f(1,d);g+=b[f(0,b.length-1)];for(l=1;l<=h;l++)g+=" "+c[f(0,c.length-1)];g+=e[f(0,e.length-1)];g+=" "}return g}}};$(document).ready(function(){try{VWS.init(),delete VWS.init}catch(a){alert(a)}VWS.START()})})();