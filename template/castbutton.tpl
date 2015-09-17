{strip}
{combine_css path=$THREED_PATH|cat:"ChromeCast/style.css"}
{if $themeconf.name == 'bootstrapdefault'}
<li>
    <a href="#" id="cast_btn_launch" title="{'Cast this picture'|translate}" rel="nofollow">
        <span class="glyphicon glyphicon-phone"></span><span class="glyphicon-text">{'Cast'|translate}</span>
    </a>
</li>
{else}
<a id="cast_btn_launch" title="{'Cast this picture'|translate}" class="pwg-state-default pwg-button" rel="nofollow">
  <span class="pwg-icon pwg-icon-cast"> </span>
  <span class="pwg-button-text">{'Cast'|translate}</span>
</a>
{/if}
{/strip}