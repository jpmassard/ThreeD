{html_head}
{if $THREED_CONF.openGraph}
<meta property="og:type" content="video">
<meta property="og:url" content="{$URL}">
{/if}
<script type="text/javascript" 
	src="{$PHPWG_ROOT_PATH}themes/default/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$THREED_PATH}vws/VWS.css" />
<script type="text/javascript" src="{$THREED_PATH}vws/VWS.js"></script>
<script type="text/javascript">
	VWS.START = function() {ldelim}
		VWS.player.realAnaglyphs = {
			ARCF: 'ARCV',
			AYBF: 'AYBF',
			AGMF: 'AGMF'
		};
	   $('#theImage').css('margin', '0 auto');
	   var aspect = 16 / 9;
	   var player = new VWS.player.S3dVideoPlayerApp('stereo');
        // player.height ({$THREED_CONF.video_height});
        // player.width ({$THREED_CONF.video_width});
		// player.autoPlay(true);
		// player.autoLoop(true);
		var resize = function() {
			if(player.enlarged()) {
				return;
			}
			player.width('100%');
			var wide = player.width();
			wide = wide < 480 ? 480 : wide;
			player.width(wide);
			var high = player.width() / aspect;
			high = high < 270 ? 270 : high;
			player.height(high);
		};
		$(window).on('resize', resize);
	   document.addEventListener('canResize', resize);

		player.on('vwsResize', resize);

		player.loadVideo('{$SRC_IMG}', 'P');
	{rdelim};
</script>
{/html_head}
<div id="stereo">
	<FONT color="#ff0000"><B>Your browser do not support HTML5</B></FONT>
</div>
