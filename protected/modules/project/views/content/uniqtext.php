<? foreach ($data as $sm): 

$p = $sm->page; 

if ($p->uniq >= 50) $color = 'text-success';
elseif ($p->uniq >= 30) $color = 'text-warning';
elseif ($p->uniq >= 0) $color = 'text-danger';
else $color = '';

?>

<div>
	<a href="#" class="jUrl"><?= $sm->url ?></a> &nbsp; 
	<b class="<?= $color ?>"><?= ($p->uniq >= 0) ? $p->uniq . '%' : '-' ?></b> &nbsp; 
	<a href="<?= $sm->url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; 

	<div class="panel panel-body block-result hide">
		<?
			$n = 0;
			if (is_array($p->shingles)) {
				foreach ($p->shingles as $sh):
					foreach (ShinglesResult::model()->findAllByAttributes(array('shingle_id' => $sh->id), array('order' => 'id asc')) as $r):
						if ($n == 0) {
							print '<h6>Найденные страницы:</h6>';
						}

						?>
						<p class="uniq-result">
							<a href="<?= $r->url ?>" target="_blank"><?= $r->url ?></a><br>
							<b><?= $r->title ?></b><br>
							<?= nl2br($r->text) ?>
						</p>
						<?

						$n++;
					endforeach;
				endforeach;
			}

			if ($n == 0) {
				print '<p><b>Ничего не найдено</b></p>';
			}
		?>
	</div>
</div>


<? endforeach; ?>

<script type="text/javascript">
$(function(){
	$('.jUrl').click(function(){
		$(this).parent().find('.panel').toggleClass('hide');
		return false;
	});
});
</script>