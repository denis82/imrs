<h6 class="text-semibold">Вебархив</h6>

<? if ($data and count($data)): ?>
<div class="row">
	<? foreach ($data as $j => $i): ?>
		<div class="col-md-4">
			<a href="<?= $i->url ?>" target="_blank"><?= date("d.m.Y H:i:s", strtotime($i->date)) ?></a><br>
			<img src="<?= $i->image ?>" style="max-width: 100%; height: auto;">
		</div>
	<? endforeach; ?>
</div>

<? else: ?>
<div class="alert alert-info alert-styled-left alert-bordered">
	Информация недоступна в данный момент.
</div>
<? endif; ?>