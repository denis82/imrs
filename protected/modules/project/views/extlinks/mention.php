<? if (count($data) > 0): ?>

	<p>
		Домен <b><?= $domain->host() ?></b> упоминается <b><?= count($data) ?> раз</b>, на следующих сайтах: 
	</p>

	<? foreach ($data as $m): ?>
	<p>
		<b><?= $m->title ?></b><br>
		<a href="<?= $m->url ?>" target="_blank"><?= txtHelper::utf8Urldecode($m->url) ?></a><br>
		<?= nl2br($m->text) ?>
	</p>
	<? endforeach; ?>

<? else: ?>

	<p>
		Домен <?= $domain->host() ?> нигде не упоминается.
	</p>

<? endif; ?>
