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
  <h2>{'3D video Uploader'|@translate}</h2>
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
			<legend>{'Choose a file'|@translate}</legend>
			<p class="file_uploader_center">
				<label>
					<span class="property">{'3D File to upload'|@translate}</span>
				</label>
				<input type="hidden" name="MAX_FILE_SIZE" value="{$upload_max_filesize}" />
				<input name="file" type="file" value=""{if isset($file_uploader_errors.file)} class="file_uploader_error"{/if}>
				{foreach from=$file_uploader_errors.file item=error_description}<span class="file_uploader_error_description" title="{$error_description}">!</span>{/foreach}
			</p>
			<p class="file_uploader_center">
				{'3D file max filesize'|@translate} {$upload_max_filesize_display} Ko.
			</p>
		</fieldset>
		<fieldset>
			<legend>{'Choose a thumbnail'|@translate}</legend>
			<p class="file_uploader_center">
				<label>
					<span class="property">{'Thumbnail to upload'|@translate}</span>
				</label>
				<input name="file_uploader_thumbnail" type="file" value=""{if isset($file_uploader_errors.thumbnail)} class="file_uploader_error"{/if}>
				{foreach from=$file_uploader_errors.thumbnail item=error_description}<span class="file_uploader_error_description" title="{$error_description}">!</span>{/foreach}
			</p>
			<p class="file_uploader_center">
				{'Let the plugin do the job or use a personnal image'|@translate}.
			</p>
		</fieldset>
		<fieldset>
			<legend>{'Properties'|@translate}</legend>
			<p class="file_uploader_center">
				<label for="file_uploader_title_input">
					<span class="property">{'Title'|@translate}</span>
				</label>
				<input size="50" id="file_uploader_title_input" type="text" name="file_uploader[title]" value="{$file_uploader.title}"{if isset($file_uploader_errors.title)} class="file_uploader_error"{/if}>
				{if isset($file_uploader_errors.title)}<span class="file_uploader_error_description" title="{$file_uploader_errors.title}">!</span>{/if}
			</p>
			<p class="file_uploader_center">
				<label for="file_uploader_title_input">
					<span class="property">{'Author'|@translate}</span>
				</label>
				<input size="50" id="file_uploader_author_input" type="text" name="file_uploader[author]" value="{$file_uploader.author}"{if isset($threed_uploader_errors.author)} class="file_uploader_error"{/if}>
				{if isset($threed_uploader_errors.Author)}<span class="file_uploader_error_description" title="{$threed_uploader_errors.author}">!</span>{/if}
			</p>
			<p class="file_uploader_center">
				<label for="file_uploader_description_input">
					<span class="property">{'Description'|@translate}</span>
				</label><br />
				<textarea cols="50" rows="5" id="file_uploader_description_input" type="text" name="file_uploader[description]"{if isset($file_uploader_errors.description)} class="file_uploader_error"{/if}>{$file_uploader.description}</textarea>
				{if isset($file_uploader_errors.description)}<span class="file_uploader_error_description" title="{$file_uploader_errors.description}">!</span>{/if}
			</p>
			</fieldset>
		<p>
			<input class="submit" name="submit" type="submit" value="{'Submit'|@translate}" />
		</p>
	</form>
</div>