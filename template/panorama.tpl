{footer_script src=""}
	embedpano({ldelim}xml:"{$SRC_XML}", target:"pano", html5:"auto", mobilescale:1.0, passQueryParameters:"false"{rdelim});
{/footer_script}

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
<meta property="og:description" content="360 Panoramic image - {$DESCRIPTION}">
{if $AUTHOR != null }
<meta property="og:site_name" content="{$AUTHOR}">
{/if}

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{$TITLE}" />
<meta name="twitter:description" content="360 panoramic image - {$DESCRIPTION}" />
<meta name="twitter:image" content="{$REPRESENT}" />
{if $AUTHOR != null }
<meta property="twitter:site" content="{$AUTHOR}">
{/if}

{/if}
{/html_head}

<div id="pano" style="width:100%;height:768px;">
	<noscript><table style="width:100%;height:100%;"><tr style="vertical-align:middle;"><td><div style="text-align:center;">ERROR:<br/><br/>Javascript not activated<br/><br/></div></td></tr></table></noscript>
	<script src="{$THREED_PATH|cat:'krpano/krpano.js'}" ></script>
</div>
	