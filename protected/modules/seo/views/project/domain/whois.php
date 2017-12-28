<h6 class="text-semibold">Whois</h6>

<? if ($data and count($data)): ?>
<div class="table-responsive">
	<table class="table">
		<tbody>
			<? foreach ($data as $j => $i): ?>
				<tr>
					<td><?= $i->name ?></td>
					<td><?= $i->value ?></td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>

<? else: ?>
<div class="alert alert-info alert-styled-left alert-bordered">
	Информация недоступна в данный момент.
</div>
<? endif; ?>