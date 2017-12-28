<? if ($queue and is_array($queue)): $spinner = false; ?>
	<div class="alert alert-info alert-styled-left alert-bordered">
		<? foreach ($queue as $q): ?>
		<p>
			<?
				if ($q->status == 1) {
			    	print '<span class="icon-checkmark4"></span>';
				}
				else {
					if (!$spinner) {
						$spinner = true;
				    	print '<span class="icon-spinner4 spinner"></span>';
					}
					else {
				    	print '';
					}
				}

				print ' &nbsp; ' . $q->stageDesc() . ' &nbsp; ' . ($q->status == 1 ? '' : $q->stageProgress());
			?>
	    </p>
		<? endforeach; ?>
    </div>
<? else: ?>
	<div class="alert alert-success alert-styled-left alert-bordered">
		Проверка завершена.
	</div>
<? endif; ?>