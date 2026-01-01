{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
(function(){
  if ($("input[name='iconposition']:checked").val() == 'custom') {
    $("#positionCustomDetails").show();
  }

  $("input[name='iconposition']").change(function(){
    if ($(this).val() == 'custom') {
      $("#positionCustomDetails").show();
    }
    else {
      $("#positionCustomDetails").hide();
    }
  });

}());
{/footer_script}

<style>
#threedPositionBox {
  width:400px;
  padding:5px;
  background-color:{if $theme.colorscheme eq 'dark'}#333{else}#DDD{/if}
}

#threedPositionBox label {
  font-weight:normal;
  display:block;
}

#threedPositionBox label.middle {
  margin:50px;
  text-align:center;
}

#threedPositionBox label.right {
  float:right;
}

#positionCustomDetails {
  margin-left:20px;
  display:none;
}

</style>

<div class="titrePage">
	<h2>{'ThreeD configuration'|@translate}</h2>
</div>
<div class="ThreeD_options">

<form method="post" enctype="multipart/form-data">

<fieldset class="mainConf">
	<legend><span class="icon-cog icon-yellow"></span>{'Basic configuration'|translate}</legend>
	<ul>
	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="chromecast" {if $threed.chromeCast}checked{/if}>{'Allow casting to ChromeCast or Android TV'|translate}
		</label>
	</li>

	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="opnGraphAllowed" {if $threed.openGraph}checked{/if}>{'Insert Facebook and Twitter tags to header'|translate}
		</label>
	</li>
	
	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="video_autoplay" {if $threed.video_autoplay}checked{/if}>{'Video start when selected'|translate}
		</label>
	</li>

	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="video_autoloop" {if $threed.video_autoloop}checked{/if}>{'Video restart when finished'|translate}
		</label>
	</li>
	</ul>
</fieldset>

<fieldset class="threedIconConf">
	<legend><span class="icon-cog icon-blue"></span>{'Media type icon'|translate}</legend>
	<ul>
	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="photo2d" {if $threed.icon2Dphoto}checked{/if}>{'draw a 2D icon on a 2D photo'|translate}
		</label>
	</li>

	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="photo3d" {if $threed.icon3Dphoto}checked{/if}>{'draw a 3D icon on a 3D photo'|translate}
		</label>
	</li>
	
	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="video2d" {if $threed.icon2Dvideo}checked{/if}>{'draw a 2D icon on a 2D video'|translate}
		</label>
	</li>

	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="video3d" {if $threed.icon3Dvideo}checked{/if}>{'draw a 3D icon on a 3D video'|translate}
		</label>
	</li>
	
	<li>
		<label class="font-checkbox">{'Position'|translate}</label>
        <br>
        <div id="threedPositionBox">
          <label class="right font-checkbox"> {'top right corner'|translate}
          	<span class="icon-dot-circled"></span>
          	<input name="iconposition" type="radio" value="topright"{if $threed.iconposition eq 'topright'}checked{/if}>
          </label>
          <label class="font-checkbox">
          	<span class="icon-dot-circled"></span>
          	<input name="iconposition" type="radio" value="topleft"{if $threed.iconposition eq 'topleft'}checked{/if}> {'top left corner'|translate}
          </label>
          <label class="middle font-checkbox">
          	<span class="icon-dot-circled"></span>
          	<input name="iconposition" type="radio" value="center"{if $threed.iconposition eq 'center'}checked{/if}> {'center'|translate}
          </label>
          <label class="right font-checkbox"> {'bottom right corner'|translate}
          	<span class="icon-dot-circled"></span>
          	<input name="iconposition" type="radio" value="bottomright"{if $threed.iconposition eq 'bottomright'}checked{/if}>
          </label>
          <label class="font-checkbox">
          	<span class="icon-dot-circled"></span>
          	<input name="iconposition" type="radio" value="bottomleft"{if $threed.iconposition eq 'bottomleft'}checked{/if}> {'bottom left corner'|translate}
          </label>
        </div>

        <label class="font-checkbox" style="display:block;margin-top:10px;font-weight:normal;">
        	<span class="icon-dot-circled"></span>
        	<input name="iconposition" type="radio" value="custom"{if $threed.iconposition eq 'custom'}checked{/if}> {'custom'|translate}
        </label>

        <div id="positionCustomDetails">
          <label>{'X Position'|translate}
            <input size="3" maxlength="3" type="text" name="iconxpos" value="{$threed.iconxpos}"{if isset($errors.xpos)} class="dError"{/if}>%
            {if isset($errors.xpos)}<span class="dErrorDesc" title="{$errors.xpos}">!</span>{/if}
          </label>

          <br>
          <label>{'Y Position'|translate}
            <input size="3" maxlength="3" type="text" name="iconypos" value="{$threed.iconypos}"{if isset($errors.ypos)} class="dError"{/if}>%
            {if isset($errors.ypos)}<span class="dErrorDesc" title="{$errors.ypos}">!</span>{/if}
          </label>

        </div>
      </li>

      <li>
        <label class="font-checkbox">{'Opacity'|translate}</label>
        <input size="3" maxlength="3" type="text" name="iconalpha" value="{$threed.iconalpha}"{if isset($errors.opacity)} class="dError"{/if}> %
        {if isset($errors.opacity)}<span class="dErrorDesc" title="{$errors.opacity}">!</span>{/if}
      </li>
    </ul>
</fieldset>

<p style="text-align:center;"><input type="submit" name="save_config" value="{'Save Settings'|translate}"></p>

</form>
</div>

