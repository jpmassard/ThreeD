{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
(function(){
  if ($("input[name='uploadRepresentative']").is(':checked')) {
    $("#3D_Details").show();
  } else {
	$("#3D_Details").hide();
  }

  $("input[name='uploadRepresentative']").change(function(){
    if ($(this).prop('checked')) {
      $("#3D_Details").show();
    }
    else {
      $("#3D_Details").hide();
    }
  });

}());
{/footer_script}

<div class="ThreeD_picture_options">

<form method="post" enctype="multipart/form-data">

<fieldset class="mainConf">
	<ul>
	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="3Dmaterial" {if $is3D}checked{/if}>{'Set if this picture or video is stereoscopic'|translate}
		</label>
	</li>

	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="uploadRepresentative">{'Set or change representative file'|translate}
		</label>
	</li>
	<div id="3D_Details">
		<label>{'Choose a representative picture'|translate}
		<input type="file" name="rFile" {if isset($errors.rFile)} class="dError"{/if}>
		{if isset($errors.rFile)}<span class="dErrorDesc" title="{$errors.rFile}">!</span>{/if}
		</label>
	</div>

	
</fieldset>

<p style="text-align:center;"><input type="submit" name="save_settings" value="{'Save Settings'|translate}"></p>

</form>
</div>

