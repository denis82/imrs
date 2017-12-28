<? if ($params): ?>
<p>
	Домен <?= $params['host']->value ?> 

	<? if ($params['host']->value != $domain->host()): ?>
		(не введенный вами поддомен - <?= $domain->host() ?>) 
	<? endif; ?>

	<? if ($params['created']->value): ?>
		зарегистрирован: <?= TxtHelper::DateFormat( $params['created']->value ) ?>, 
		возраст домена: <?= TxtHelper::LivePeriod( $params['created']->value ) ?><? else: ?>
		дата регистрации неизвестна<? endif;

	if ($params['expire']->value): ?>,
		очередная оплата домена: до <?= TxtHelper::DateFormat( $params['expire']->value ) ?>
	<? endif; ?>
</p>
<p>
	Name Server (сервер имен): <?= $params['ns']->value ?>
</p>


<? endif; ?>

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