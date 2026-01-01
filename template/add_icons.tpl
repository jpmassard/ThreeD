{html_style}
.overlay-content {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}

.tag{
	position: absolute;
}
{/html_style}


{footer_script}
$(document).ready(function() {
	var i = 0;
	var icons = JSON.parse('{$icons}');
	var thumbs;
	if ($("#thumbnails").length) {
		thumbs = $("#thumbnails");
	} else if ($(".thumbnails").length) {
		thumbs = $(".thumbnails");
	} else return;
	thumbs.find("img").each(function(a, b) {
		if(icons[i]) {
			var src = '{$path}' + 'template/img/' + icons[i][0] + '.png';
			var cont = $(this).parent();
			var pos = cont.position();
			var div = $('<div class="overlay-content">');
			var img = $('<img class="tag" width="40" height="40">');
			img.attr('src', src);
			img.attr('alt', icons[i][0]);
			div.append(img);
			cont.append(div);
			var x= {$x} * (div.width() + 2*pos.left - 40) / 100 - pos.left;
			var y= {$y} * (div.height() + 2*pos.top - 40) / 100 - pos.top;
			img.attr('style', 'top:' + y + 'px; left:' + x + 'px; opacity:' + {$alpha}/100);
		}
		i++;
	});
});

{/footer_script}
