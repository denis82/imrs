<? if ($data): ?>

<table class="table">
	<tbody>
	<? foreach ($data as $i): ?>
		<tr>
			<td><?= CountersCheck::$items[$i->name] ?></td>
			<td><?= ($i->value ? (($i->name == 'li') ? '<i class="text-success icon-checkmark4"></i>' : $i->value) : '<i class="text-danger icon-minus2"></i>') ?></td>
		</tr>
	<? endforeach; ?>
	</tbody>
</table>	

<? else: ?>
	<div class="alert alert-info alert-styled-left alert-bordered">
		Информация недоступна в данный момент.
	</div>
<? endif; ?>