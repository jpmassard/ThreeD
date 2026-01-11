{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
(function(){
	{if $isPano}
		$("#unzipPano").hide();
		$("#deleteZip").hide();
	{else}
		$("#deleteZip").show();
	{/if}
	$("input[name='unzipArchive']").change(function(ev) {
		if (ev.target.checked) {
			$("#deleteZip").show();
		} else {
			$("#deleteZip").hide();
		}
	});
}());
{/footer_script}

<div class="ThreeD_pano_options">

<form method="post" enctype="multipart/form-data">

<fieldset class="mainConf">
	<ul>
	<li id="unzipPano">
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="unzipArchive" {if not $isPano}checked{/if}>{'Extract panorama archive'|translate}
		</label>
	</li>

	<li id="deleteZip">
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="delArchive" >{'Delete archive after extraction'|translate}
		</label>
	</li>

	<li>
		<label class="font-checkbox">
			<span class="icon-check"></span>
			<input type="checkbox" name="uploadRepresentative">{'Change representative file'|translate}
		</label>
	</li>
</fieldset>

<p style="text-align:center;"><input type="submit" name="save_settings" value="{'Save Settings'|translate}"></p>

</form>
</div>

