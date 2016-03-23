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
<meta property="og:description" content="{$DESCRIPTION}">
<meta property="og:author" content="{$AUTHOR}">
{/if}
<script type="text/javascript" src="{$PHPWG_ROOT_PATH}themes/default/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$THREED_PATH}vws/VWS.css" />
<script type="text/javascript" src="{$THREED_PATH}vws/VWS.js"></script>
<script type="text/javascript">
	VWS.START = function() {
	VWS.player.realAnaglyphs = {
		ARCF: 'ARCV',
		AYBF: 'AYBF',
		AGMF: 'AGMF'
	};

	{if $EXTENSION == 'jpg'}
  var viewer = new VWS.player.ImageViewerApp('stereo');
  {else}
  var viewer = new VWS.player.S3dImageViewerApp({if $EXTENSION == 'mpo'}'MPO'{else}'IMG'{/if}, 'stereo');
  {/if}
  
	var aspectRatio= {$FILE_INFO['width']}/{$FILE_INFO['height']};
	{if $EXTENSION == 'jps'} 
	// divide by 2 for non MPO file
	aspectRatio /= 2;
	{/if}
	var resize = function() {
        viewer.width('100%');
        if (aspectRatio > 1)
		   viewer.height(viewer.width() / aspectRatio);
        else
           viewer.height($(window).height());
        if(typeof(Storage) !== "undefined") {
          if (sessionStorage.scroll) {
            $(document).scrollTop(sessionStorage.scroll);
          }
        }
    };
	$(window).on('resize', resize);
    $(window).on('scroll', scrollPos);
	viewer.on('vwsResize', resize);
	viewer.loadImage('{$SRC_IMG}', {if $EXTENSION == 'mpo'}'SQ'{else}'P'{/if});
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
	