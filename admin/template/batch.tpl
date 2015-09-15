{include file='include/colorbox.inc.tpl'}
{include file='include/add_album.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}
{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
{* <!-- CATEGORIES --> *}
var categoriesCache = new CategoriesCache({
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

categoriesCache.selectize(jQuery('[data-selectize=categories]'));

jQuery('[data-add-album]').pwgAddAlbum({ cache: categoriesCache });
{/footer_script}

<div class="titrePage">
  <h2>{'3D Batch Mode Uploader'|@translate}</h2>
</div>
<div class="file_uploader_form">
	<form method="post" enctype="multipart/form-data">
	    <fieldset class="selectAlbum">
	      <legend>{'Drop into album'|@translate}</legend>
	
	      <span id="albumSelection">
			<select data-selectize="categories" data-value="{$file_uploader.category|@json_encode|escape:html}"
			  data-default="first" name="file_uploader[category]" style="width:400px"></select>
			<br>{'... or '|@translate}</span>
			<a href="#" data-add-album="file_uploader[category]" title="{'create a new album'|translate}"> {'create a new album'|@translate}</a>
	    </fieldset>
		<fieldset>
			<legend>{'Choose files'|@translate}</legend>
			<p class="file_uploader_center">
				<label>
					<span class="property">{'3D Files to upload'|@translate}</span>
				</label>
				<input name="file[]" type="file" value=""{if isset($threed_uploader_errors.file)} class="file_uploader_error"{/if} multiple>
				{foreach from=$threed_uploader_errors.file item=error_description}<span style="color:red" class="file_uploader_error_description">{$error_description}</span>{/foreach}
			</p>
			<p class="file_uploader_center">
				{'3D file max filesize'|@translate} {$upload_max_filesize_display} Ko.
			</p>
			<p class="file_uploader_center">
				{'You cannot transfer more than '|@translate} {$max_upload_files} {'files due to php.ini parameters.'|@translate}
			</p>
		</fieldset>
		<fieldset>
			<legend>{'Properties'|@translate}</legend>
			<p class="file_uploader_center">
				<label for="file_uploader_title_input">
					<span class="property">{'Author'|@translate}</span>
				</label>
				<input size="50" id="file_uploader_author_input" type="text" name="file_uploader[author]" value="{$file_uploader.author}"{if isset($threed_uploader_errors.author)} class="file_uploader_error"{/if}>
				{if isset($threed_uploader_errors.Author)}<span class="file_uploader_error_description" title="{$threed_uploader_errors.author}">!</span>{/if}
			</p>
			</fieldset>
		<p>
			<input class="submit" name="submit" type="submit" value="{'Submit'|@translate}" />
		</p>
	</form>
</div>