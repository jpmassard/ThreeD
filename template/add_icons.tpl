{html_style}
.overlay-content {
	position: absolute;
	top: 0;
	width: 100%;
	height: 100%;
}

.tag{
	position: absolute;
	right: 20px;
	top: 5px;
}
{/html_style}

{footer_script}

$(document).ready(function() {
	var i = 0;
	var icons = JSON.parse('{$icons}');

	$("#thumbnails").find("img").each(function() {
		if(icons[i]) {
		debugger;
			var src = '{$path}' + 'template/img/' + icons[i][0] + '.png';
			var div = $('<div class="overlay-content">');
			var img = $('<img class="tag" width="40" height="40">');
			img.attr('src', src);
			img.attr('alt', icons[i][0]);
			div.append(img);
			$(this).parent().append(div);
			
		}
		i++;
	});
	
});

{/footer_script}
