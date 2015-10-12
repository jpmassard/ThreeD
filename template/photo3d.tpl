{html_style}
canvas{
   left : 0px !important;
}
#theImage {
    margin : 0px auto;
}
{/html_style}
{html_head}
{if $THREED_CONF.openGraph}
<meta property="og:type" content="article">
<meta property="og:image" content="{$REPRESENT}">
<meta property="og:url" content="{$URL}">
<meta property="og:author" content="{$AUTHOR}">
{/if}
<script type="text/javascript" src="{$PHPWG_ROOT_PATH}themes/default/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$THREED_PATH}vws/VWS.css" />
<script type="text/javascript" src="{$THREED_PATH}vws/VWS.js"></script>
<script type="text/javascript">
	VWS.START = function() {ldelim}
	VWS.player.realAnaglyphs = {
		ARCF: 'ARCV',
		AYBF: 'AYBF',
		AGMF: 'AGMF'
	};

	var viewer = new VWS.player.S3dImageViewerApp({if $EXTENSION == 'MPO'}'MPO'{else}'IMG'{/if}, 'stereo');

	var aspectRatio= {$FILE_INFO['width']}/{$FILE_INFO['height']};
	{if $EXTENSION != 'MPO'} 
	// divide by 2 for non MPO file
	aspectRatio /= 2;
	{/if}
	var resize = function() {
        viewer.width('100%');
		var high = viewer.width() / aspectRatio;
		viewer.height(high);
        if(typeof(Storage) !== "undefined") {
          if (sessionStorage.scroll) {
            $(document).scrollTop(sessionStorage.scroll);
          }
        }
    };
	$(window).on('resize', resize);
    $(window).on('scroll', scrollPos);
	viewer.on('vwsResize', resize);
	viewer.loadImage('{$SRC_IMG}', {if $EXTENSION == 'MPO'}'SQ'{else}'P'{/if});

    function scrollPos() {
      if(typeof(Storage) !== "undefined") {
        var stopListener = $(window).mouseup(function(){
        sessionStorage.scroll = $(document).scrollTop();
        stopListener();
        });
      }
    }
{rdelim};
</script>
{/html_head}
<div id="stereo">
	<FONT color="#ff0000"><B>Your browser do not support HTML5</B></FONT>
</div>
	