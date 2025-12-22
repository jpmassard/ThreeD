<div class="titrePage">
  <h2>{$name}{' > set 3D representative file'|@translate}</h2>
</div>
<div class="file_uploader_form">
	<form method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>{'Choose file'|@translate}</legend>
			<p class="file_uploader_center">
				<label>
					<span class="property">{'JPEG representative file to upload'|@translate}</span>
				</label>
				<input name="file" type="file" value="" {if isset($uploader_errors.file)} class="uploader_error"{/if} multiple>
				{if isset($uploader_errors.file)}
					{foreach from=$uploader_errors.file item=error_description}<span style="color:red" class="file_uploader_error_description">{$error_description}</span>{/foreach}
				{/if}
			</p>
		</fieldset>
		<p>
			<input class="submit" name="submit" type="submit" value="{'Submit'|@translate}" />
		</p>
	</form>
</div>
