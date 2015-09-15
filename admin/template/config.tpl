
<div class="titrePage">
	<h2>{'Common ThreeD configuration'|@translate}</h2>
</div>
<div class="ThreeD_options">
<form method="post" enctype="multipart/form-data">

<fieldset>
  <legend>{'Cast'|translate}</legend>
    <p>
      <span class="property">{'ChromeCast'|translate}</span>
      <label><input type="radio" name="chrCastAllowed" value="1" {if $threed.chromeCast}checked="checked"{/if}> {'Yes'|translate}</label>
      <label><input type="radio" name="chrCastAllowed" value="0" {if not $threed.chromeCast}checked="checked"{/if}> {'No'|translate}</label>
    </p>
</fieldset>

<fieldset>
  <legend>{'OpenGraph'|translate}</legend>
    <p>
      <span class="property">{'OpenGraph'|translate}</span>
      <label><input type="radio" name="opnGraphAllowed" value="1" {if $threed.openGraph}checked="checked"{/if}> {'Yes'|translate}</label>
      <label><input type="radio" name="opnGraphAllowed" value="0" {if not $threed.openGraph}checked="checked"{/if}> {'No'|translate}</label>
    </p>
</fieldset>


<fieldset>
  <legend>{'Videos'|translate}</legend>
    <p>
      <span class="property">{'Autoplay'|translate}</span>
      <label><input type="radio" name="video_autoplay" value="1" {if $threed.video_autoplay}checked="checked"{/if}> {'Yes'|translate}</label>
      <label><input type="radio" name="video_autoplay" value="0" {if not $threed.video_autoplay}checked="checked"{/if}> {'No'|translate}</label>
    </p>
    <p>
      <span class="property">{'Description'|translate}</span>
      <input type="text" name="video_description" value="{$threed.video_description}" size="100">
    </p>
</fieldset>

<p style="text-align:center;"><input type="submit" name="save_config" value="{'Save Settings'|translate}"></p>

</form>
</div>
