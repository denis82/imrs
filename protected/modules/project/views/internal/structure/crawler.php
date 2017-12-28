<?php

if ($pages and count($pages)): 

	$uid = uniqid();

	print '<div id="' . $uid . '" class="sitemap"><ul>';

	$eol = "\n";

	$current = array();
	$level = 0;

	foreach ($pages as $el) {
		$url = parse_url($el->url);
		$url['path'] = explode('/', substr($url['path'], 1));

		$url['path'][ count($url['path']) - 1 ] .= 
			($url['query'] ? '?' . $url['query'] : '') . 
			($url['anchor'] ? '#' . $url['anchor'] : '')
		;

		if ($current[0] != $url['scheme'] . '://' . $url['host']) {
			if ($level > 0) {
				for ($j = 0; $j < $level; $j++) {
					print str_pad('', $level, "\t") . '</ul></li>' . $eol;
				}
			}

			$current = array();
			$current[0] = $url['scheme'] . '://' . $url['host'];
			$level = 1;

			print '<li><span>' . $current[0] . '</span><ul>' . $eol;
		}

		for ($j = 0; $j < count($url['path']); $j++) {
			if (isset($current[$j+1]) and $current[$j+1] == $url['path'][$j]) {
			}
			else {
				if (count($current) > $j + 1) {
					for ($i = $j + 1; $i < count($current); $i++) {
						print str_pad('', $level, "\t") . '</ul></li>' . $eol;
					}

					$current = array_slice($current, 0, $j + 1);
				}


				$current[$j + 1] = $url['path'][$j];
				$level = count($current);

				print str_pad('', $level, "\t") . '<li data-level="' . $level . '"><span data-href="' . implode('/', array_slice($current, 0, -1)) . '" data-status="200" data-title="' . $el->url . '">/' . $url['path'][$j] . '</span><ul>' . $eol;
			}
		}
	}

	for ($j = 0; $j < $level; $j++) {
		print str_pad('', $level, "\t") . '</ul></li>' . $eol;
	}

	print '</ul></div>';

	?>

	<script type="text/javascript">
		$(function(){
			$('#<?= $uid ?>').each(function(){
				var $this = $(this);

				$('ul', $this).each(function(){
					if ($('li', this).length == 0) {
						$(this).remove();
					}
				});
				
				$('li', $this).each(function(){
					if ($('li', this).length == 1 && $('li', this).text() == '/') {
						$('ul', this).remove();
						$('span', this).append('/');
					}
				});
				
				$('li', $this).each(function(){
					if ($(this).text() == '/' && parseInt($(this).data('level')) > 2) {
						$(this).remove();
					}
				});
				
				$('li', $this).each(function(){
					if ($('ul', this).length && parseInt($(this).data('level')) > 1) {
						$('span', this).first().before('<i class="jSitemapExpander icon-plus2"></i>');
					}
				});

				$('li > span', $this).each(function(){
					if (parseInt($(this).closest('li').data('level')) > 1) {
						var href = $(this).data('href');
						var text = $(this).text();
						var status = parseInt( $(this).data('status') );
						var title = $(this).data('title');

						$(this).replaceWith('<a href="' + href + text + '" class="' + ((status > 0 && status !== 200) ? 'text-danger' : '') + '" target="_blank">' + ((status > 0 && status !== 200) ? '<b class="icon-warning22"></b> ' : '') + (title.length ? title : text) + '</a>');
					}
				});

				$this.delegate('.jSitemapExpander', 'click', function(){
					if ($(this).hasClass('icon-plus2')) {
						$(this).removeClass('icon-plus2').addClass('icon-minus2');
						$(this).closest('li').children('ul').show();
					}
					else {
						$(this).addClass('icon-plus2').removeClass('icon-minus2');
						$(this).closest('li').children('ul').hide();
					}
				});
				
			});
		});
	</script>

	<style>
		.sitemap > ul > li > ul > li ul { display: none; }
		.sitemap ul { list-style: none; }
		.sitemap li { position: relative; }
		.sitemap i { position: absolute; margin-left: -20px; margin-top: 3px; cursor: pointer; }
	</style>


	<?

else: 

	?>
		<div class="alert alert-info alert-styled-left alert-bordered">
			Проверка не проводилась.
		</div>
	<? 

endif;    	
