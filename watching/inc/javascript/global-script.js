/**
 * global-script.js
 * Auxiliary JavaScript functions for all FlashOver3D.com pages.
 *
 * @author Volker "Bill" Schuelbe
 * @copy (c)2014 - All Rights Reserved
 */
var updateYear=function(){$("#cr-year").text((new Date).getFullYear())},activateMailLinks=function(){$.each($(".e-mail-link"),function(b,a){a.href="mailto:customer.service@flashover3d.com";a.innerHTML=a.innerHTML.replace("#ADDRESS#","customer.service@flashover3d.com")})},more=function(b,a){b&&$("#"+b).fadeOut("slow");$("#"+a).show("slow");return!1},less=function(b,a,c){$("#"+a).hide("slow");b?$("#"+b).fadeIn("slow",function(){c&&(location.href="#"+c)}):c&&(location.href="#"+c);return!1};