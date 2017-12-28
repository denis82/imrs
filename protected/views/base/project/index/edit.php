<? 
    $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/form_layouts.js'); 
?>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <div class="panel panel-flat">
            <div class="panel-body">
            	<h6 class="text-semibold">Информация</h6>

                <table class="table">
                    <tbody>
                        <tr>
                            <td><?= $model->getAttributeLabel('host') ?></td>
                            <td><?= $model->host ?></td>
                        </tr>
                        <tr>
                            <td><?= $model->getAttributeLabel('name') ?></td>
                            <td><?= $model->name ?></td>
                        </tr>
                        <tr>
                            <td><?= $model->getAttributeLabel('regions') ?></td>
                            <td><?
                                $html = array();

                                if (is_array($model->regions)) {
                                    foreach ($model->regions as $i) {
                                        $html[] = Region::getByPk($i);
                                    }
                                }

                                print implode(', ', $html);
                            ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center" style="margin-top: 1em;">
                    <a href="<?= Yii::app()->urlManager->createUrl('project/index/remove', array('id' => $model->id)) ?>" class="btn btn-danger" onclick="return confirm('Удалить сайт?');"><i class="icon-cross3"></i> Удалить сайт</a>

                    <?// if (Yii::app()->user->role == 'administrator'): ?>
	                    <a href="<?= Yii::app()->urlManager->createUrl('project/index/drop', array('id' => $model->id)) ?>" class="btn btn-danger" style="margin-left: 10px;" onclick="return confirm('Удалить сайт, домен и все данные по сайту для всех пользователей?');"><i class="icon-cross3"></i> Удалить сайт и все данные</a>
                	<?// endif; ?>
                </div>

            </div>
        </div>

        <div class="panel panel-flat">
            <div class="panel-body">
            	<h6 class="text-semibold">Конкуренты</h6>

                <? if ($competitors): ?>
                <table class="table">
                    <tbody>
                    	<? foreach ($competitors as $el): ?>
                        <tr>
                            <td><?= $el->domain->host() ?></td>
                        </tr>
                    	<? endforeach; ?>
                    </tbody>
                </table>
            	<? endif; ?>

                <? if (!$competitors or count($competitors) < 3): ?>
                <div class="text-center" style="margin-top: 1em;">
                    <a href="<?= Yii::app()->urlManager->createUrl('project/index/competitor', array('id' => $model->id)) ?>" class="btn btn-success"><i class="icon-plus2"></i> Добавить</a>
                </div>
            	<? endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6">
        <div class="panel panel-flat">
            <div class="panel-body">
            	<h6 class="text-semibold">Компания</h6>

            	<? if ($org): $btn = '<i class="icon-pencil3"></i> Изменить'; ?>

	                <table class="table">
	                    <tbody>
	                        <tr>
	                            <td>Местоположение</td>
	                            <td><?

	                            $tmp = array();
	                            if ($org->country) $tmp[] = $org->country;
	                            if ($org->region) $tmp[] = $org->region;
	                            if ($org->city) $tmp[] = $org->city;
	                            if ($org->district) $tmp[] = $org->district;

	                            print implode(', ', $tmp);

	                            ?></td>
	                        </tr>
	                        <tr>
	                            <td>Название</td>
	                            <td>
	                            	<?= $org->name ?>
	                            	<?= $org->legal ? '(' . $org->legal . ')' : '' ?>
	                            </td>
	                        </tr>
	                        
	                        <? if ($org->address): ?>
		                        <tr>
		                            <td>Адрес</td>
		                            <td><?= $org->address ?></td>
		                        </tr>
	                    	<? endif; ?>
	                        
	                        <? if ($org->org_phone): ?>
		                        <tr>
		                            <td>Телефон</td>
		                            <td>+<?= $org->org_phone[0]->country ?> (<?= $org->org_phone[0]->code ?>) <?= $org->org_phone[0]->number ?></td>
		                        </tr>
	                    	<? endif; ?>
	                        
	                        <? if ($org->org_site): ?>
		                        <tr>
		                            <td>Сайт</td>
		                            <td><?= $org->org_site[0]->url ?></td>
		                        </tr>
	                    	<? endif; ?>
	                    </tbody>
	                </table>

            	<? else: $btn = '<i class="icon-plus2"></i> Добавить'; ?>

	            	<p>Нет информации о компании.</p>

            	<? endif; ?>

                <div class="text-center" style="margin-top: 1em;">
                    <a href="<?= Yii::app()->urlManager->createUrl('project/index/org', array('id' => $model->id)) ?>" class="btn btn-success"><?= $btn ?></a>
                </div>

            </div>
        </div>
    </div>
</div>
