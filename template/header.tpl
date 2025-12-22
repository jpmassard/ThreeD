
<!-- Facebook tags -->
<meta property="og:type" content="website">
<meta property="og:title" content="{$TITLE}">
{if $IMAGE != '' }
<meta property="og:image" content="{$IMAGE}">
{/if}
<meta property="og:url" content="{$URL}">
<meta property="og:description" content="{if $TYPE == gallery}3D stereoscopic gallery{else}3D stereoscopic and 360 panoramas site{/if} - {$DESCRIPTION}">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{$TITLE}" />
<meta name="twitter:description" content="{if $TYPE == gallery}3D stereoscopic gallery{else}3D stereoscopic and 360 panoramas site{/if} - {$DESCRIPTION}" />
<meta name="twitter:image" content="{$IMAGE}" />

