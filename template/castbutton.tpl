{combine_css path="plugins/ThreeD/ChromeCast/style.css"}

{if $BOOTSTRAP}
	<li class="nav-item">
		<a id="castbutton" title="{'Cast pictures in this directory to ChromeCast or Android TV'|translate}" class="nav-link" rel="nofollow">
			<google-cast-launcher ></google-cast-launcher>
		</a>
	</li>
{else}
	{if $TYPE}<li>{/if}
		<a id="castbutton" title="{'Cast pictures in this directory to ChromeCast or Android TV'|translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<google-cast-launcher ></google-cast-launcher>
		</a>
	{if $TYPE}</li>{/if}
{/if}
