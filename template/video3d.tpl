{html_style}
canvas{
   left : 0px !important;
}
#theImage {
    margin : 0px auto;
}
#theImage img {
  max-width: unset;
} 
{/html_style}
{html_head}
{if $THREED_CONF.openGraph}

<!-- Facebook tags -->
<meta property="og:type" content="video.other">

<meta property="og:url" content="{$URL}">
<meta property="og:description" content="3D Stereoscopic video - {$DESCRIPTION}">
{if $AUTHOR != null }
<meta property="og:site_name" content="{$AUTHOR}">
{/if}

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{$TITLE}" />
<meta name="twitter:description" content="3D Stereoscopic video - {$DESCRIPTION}" />
<meta name="twitter:image" content="{$REPRESENT}" />
{if $AUTHOR != null }
<meta property="twitter:site" content="{$AUTHOR}">
{/if}

{/if}
<script type="text/javascript" src="{$PHPWG_ROOT_PATH}themes/default/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$THREED_PATH}vws/VWS.css" />
<script type="text/javascript" src="{$THREED_PATH}vws/VWS.min.js"></script>
<script type="text/javascript">
	VWS.START = function() {
		VWS.player.realAnaglyphs = {
			ARCF: 'ARCV',
			AYBF: 'AYBF',
			AGMF: 'AGMF'
		};
	    var aspect = 16 / 9;
	    var player = new VWS.player.S3dVideoPlayerApp('stereo');
	    player.autoPlay(false);
	    player.autoLoop(false);
        {if $THREED_CONF.video_autoplay}
        player.autoPlay(true);
        {/if}
        {if $THREED_CONF.video_autoloop}
        player.autoLoop(true);
        {/if}
		var resize = function() {
			player.width('100%');
			var wide = player.width();
			wide = wide < 480 ? 480 : wide;
			player.width(wide);
			var high = player.width() / aspect;
			high = high < 270 ? 270 : high;
			player.height(high);
            if(typeof(Storage) !== "undefined") {
              if (sessionStorage.scroll) {
                $(document).scrollTop(sessionStorage.scroll);
              }
            }
		};
		$(window).on('resize', resize);
        $(window).on('scroll', scrollPos);
		player.on('vwsResize', resize);
		player.loadVideo('{$SRC_IMG}', 'PA');
	};
    
    function scrollPos() {
      if(typeof(Storage) !== "undefined") {
        var stopListener = $(window).mouseup(function(){
        sessionStorage.scroll = $(document).scrollTop();
        });
      }
    }
</script>
{/html_head}
<div id="stereo">
	<FONT color="#ff0000"><B>Your browser do not support HTML5</B></FONT>
</div>
