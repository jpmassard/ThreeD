{html_style}
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
<meta property="og:type" content="website">
<meta property="og:image" content="{$REPRESENT}">
<meta property="og:url" content="{$URL}">
<meta property="og:description" content="360 Panoramic image - {$IMAGE['comment']}" >
{if $IMAGE['author'] != null }
<meta property="og:site_name" content="{$IMAGE['author']}">
{/if}

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{$IMAGE['TITLE']}" />
<meta name="twitter:description" content="360 panoramic image - {$IMAGE['comment']}" />
<meta name="twitter:image" content="{$REPRESENT}" />
{if $IMAGE['author'] != null }
<meta property="twitter:site" content="{$IMAGE['author']}">
{/if}

{/if}

{if $IMAGE['pano_type'] == 'pannellum'}
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
{/if}
{/html_head}

<div id="pano" style="width:100%;height:768px;">
	<noscript><table style="width:100%;height:100%;"><tr style="vertical-align:middle;"><td><div style="text-align:center;">ERROR:<br/><br/>Javascript not activated<br/><br/></div></td></tr></table></noscript>
{if $IMAGE['pano_type'] == 'krpano'}
	<script src="{$THREED_PATH|cat:'krpano/krpano.js'}" ></script>
{elseif $IMAGE['pano_type'] == 'pannellum'}
	<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
{elseif $IMAGE['pano_type'] == '3dvista'}
{/if}
</div>

{footer_script}
{if  $IMAGE['pano_type'] == 'krpano'}
	embedpano({ldelim}xml:"{$SRC_XML}", target:"pano", html5:"auto", mobilescale:1.0, passQueryParameters:"false"{rdelim});
{elseif $IMAGE['pano_type'] == 'pannellum'}
pannellum.viewer('pano', {ldelim}
	"type": {if $EXT == 'zip'}"multires",
	"multiRes": {ldelim}
		"basePath": "/images/multires/library",
		"path": "/%l/%s%y_%x",
		"fallbackPath": "/fallback/%s",
		"extension": "jpg",
		"tileResolution": 512,
		"maxLevel": 6,
		"cubeResolution": 8432
	{rdelim}
	{else} "equirectangular",
    "panorama": "{$REPRESENT}",
    "autoLoad": true
	{/if}
{rdelim});
{elseif $IMAGE['pano_type'] == '3dvista'}
{/if}
{/footer_script}

