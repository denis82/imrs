<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/bootstrap-datepicker/css/datepicker.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/flot/jquery.flot.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/flot/jquery.flot.resize.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/flot/jquery.flot.pie.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/flot/jquery.flot.stack.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/flot/jquery.flot.crosshair.js"); ?>


<? $this->clientScript->registerScript("date-picker", "$('.date-picker').datepicker({language:'ru'});"); ?>
<div class="row-fluid">
<div class="span12">
<div class="portlet-body">
<div class="row-fluid">
<div class="span12">
<div class="clearfix">

<div class="control-group">
    <label class="control-label">Дата отчета</label>

    <div class="controls">
        <form id="date-change-form" method="get">
            <div class="input-append date date-picker" data-date-format="dd.mm.yyyy">
                <?php echo CHtml::textField("date", $date, array('class' => "m-wrap m-ctrl-medium date-picker")); ?>
                <span class="add-on"><i class="icon-calendar"></i></span>
            </div>
            <a href="#" class="btn blue" onclick="$('#date-change-form').submit();" style="margin-top: -10px;">Обновить
                <i class="icon-refresh"></i></a>
        </form>
    </div>
</div>

<div class="tabbable tabbable-custom tabbable-full-width">
<ul class="nav nav-tabs">
    <li<? if ($tab == 1): ?> class="active"<? endif; ?>><a href="#tab_1" data-toggle="tab">Информация по домену</a></li>

    <li<? if ($tab == 2): ?> class="active"<? endif; ?>><a href="#tab_2" data-toggle="tab">Статистика по ключевым
            словам</a></li>
    <li<? if ($tab == 3): ?> class="active"<? endif; ?>><a href="#tab_3" data-toggle="tab">Конкуренты</a></li>
	<li><a href="#tab_4" data-toggle="tab">Мониторинги</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane row-fluid <? if ($tab == 1): ?> active<? endif; ?>" id="tab_1">
<? if (isset($hostinfo)): ?>
    <table class="items table table-striped">
    <thead>
    <tr>
        <th>Параметр</th>
        <th>Значение</th>
    </tr>
    </thead>
    <tr>
        <td>Домен зарегистрирован:</td>
        <td><?= $hostinfo->created ?></td>
    </tr>
    <tr>
        <td>Домены оплачен до:</td>
        <td><?= $hostinfo->paid ?></td>
    </tr>
    <tr>
        <td>Регистратор домена:</td>
        <td><?= $hostinfo->registrar ?></td>

    </tr>
    <tr>
        <td>NS-серверы:</td>
        <td>
            <?= $hostinfo->nsservers; ?>
        </td>

    </tr>
    <tr>
        <td>АйПи сайта:</td>
        <td><?= $hostinfo->ip ?></td>

    </tr>
    <tr>
        <td>Хостинг сайта:</td>
        <td><?= $hostinfo->hoster ?></td>

    </tr>
    <tr>
        <td>Время ответа страницы:</td>
        <td><?= $hostinfo->starttransfer_time ?></td>
    </tr>
    <tr>
        <td>Время загрузки страницы:</td>
        <td><?= $hostinfo->time ?></td>
    </tr>
    <tr>
        <td>Обработка ошибки 404:</td>
        <td><?= ($hostinfo->error404) ? "да" : "нет" ?></td>

    </tr>
    <tr>
        <td>Файл инструкций для поисковых систем robots.txt:</td>
        <td><?= ($hostinfo->robots) ? "да" : "нет" ?></td>

    </tr>
    <tr>
        <td>Карта сайта:</td>
        <td><?= ($hostinfo->sitemap) ? 'есть <a href="' . $hostinfo->sitemap . '">посмотреть</a>' : "нет"; ?></td>

    </tr>
    <tr>
        <td>Предположение о системе управления сайтом (CMS):</td>
        <td><?= $hostinfo->cms ?></td>

    </tr>
    <tr>
        <td>Заголовок (Title):</td>
        <td><?= $hostinfo->title ?></td>

    </tr>
    <tr>
        <td>Мета-тег description:</td>
        <td><?= $hostinfo->description ?></td>
        <td></td>
    </tr>
    <tr>
        <td>Мета-тег keywords:</td>
        <td><?= $hostinfo->keywords ?></td>

    </tr>
    <tr>
        <td>Заголовки H1-H6 на главной странице:</td>
        <td><?= ($hostinfo->h1h6) ? "присутствуют" : "нет" ?></td>

    </tr>
    <tr>
        <td>Alt и title картинок на главной странице:</td>
        <td><?= ($hostinfo->alts) ? "присутствуют" : "нет" ?></td>
        <td></td>
    </tr>
    <tr>
        <td>ТИЦ:</td>
        <td><?= $hostinfo->tic ?></td>

    </tr>
    <tr>
        <td>PageRank:</td>
        <td><?= $hostinfo->pr ?></td>

    </tr>
    <tr>
        <td>Яндекс-каталог:</td>
        <td><?= ($hostinfo->yac) ? "зарегистрирован" : "не зарегистрирован" ?></td>

    </tr>
    <tr>
        <td>Яндекс-метрика:</td>
        <td><?= ($hostinfo->yam) ? "подключена" : "нет" ?></td>

    </tr>
    <tr>
        <td>Яндекс-вебмастер:</td>
        <td><?= ($hostinfo->yaw) ? "подключена" : "код подтверждения не найден" ?></td>

    </tr>
    <tr>
        <td>Google-analytics:</td>
        <td><?= ($hostinfo->ga) ? "подключена" : "код аналитики не установлен" ?></td>

    </tr>
    <tr>
        <td>Google-webmaster:</td>
        <td><?= ($hostinfo->gw) ? "подключена" : "код подтверждения не найден" ?></td>

    </tr>

    <tr>
        <td>Google-webmaster:</td>
        <td><?= ($hostinfo->gw) ? "подключена" : "код подтверждения не найден" ?></td>

    </tr>
    <tr>
        <td>Ежемесячно просмотров страниц:</td>
        <td><?= $hostinfo->limp; ?></td>

    </tr>
    <tr>
        <td>Ежемесячно уникальный посетителей:</td>
        <td><?= $hostinfo->limv; ?></td>

    </tr>
    <tr>
        <td>Ежедневно просмотров страниц:</td>
        <td><?= $hostinfo->lidp; ?></td>

    </tr>
    <tr>
        <td>Ежедневно уникальных посетителей:</td>
        <td><?= $hostinfo->lidv; ?></td>

    </tr>
    <tr>
        <td>Страницы в индексе:</td>
        <td><?= $hostinfo->index_count; ?></td>

    </tr>
    <tr>
        <td>Дата индексации:</td>
        <td><?= $hostinfo->index_date; ?></td>

    </tr>
    <tr>
        <td>Зеркала домена:</td>
        <td><?= $hostinfo->mr_sites; ?></td>

    </tr>
    <tr>
        <td>Сайты на том же IP:</td>
        <td><?= $hostinfo->ip_sites; ?></td>

    </tr>
    <tr>
        <td>Доноры:</td>
        <td><?= $hostinfo->din; ?></td>

    </tr>
    <tr>
        <td>Доноры уровень 1:</td>
        <td><?= $hostinfo->din_l1; ?></td>

    </tr>
    <tr>
        <td>Доноры уровень 2:</td>
        <td><?= $hostinfo->din_l2; ?></td>

    </tr>
    <tr>
        <td>Доноры уровень 3:</td>
        <td><?= $hostinfo->din_l3; ?></td>

    </tr>
    <tr>
        <td>Доноры уровень 4:</td>
        <td><?= $hostinfo->din_l4; ?></td>

    </tr>
    <tr>
        <td>Внешние ссылки:</td>
        <td><?= $hostinfo->hin; ?></td>

    </tr>
    <tr>
        <td>Внешние ссылки уровень 1:</td>
        <td><?= $hostinfo->hin_l1; ?></td>

    </tr>
    <tr>
        <td>Внешние ссылки уровень 2:</td>
        <td><?= $hostinfo->hin_l2; ?></td>

    </tr>
    <tr>
        <td>Внешние ссылки уровень 3:</td>
        <td><?= $hostinfo->hin_l3; ?></td>

    </tr>
    <tr>
        <td>Внешние ссылки уровень 4:</td>
        <td><?= $hostinfo->hin_l4; ?></td>

    </tr>
    <tr>
        <td>Ссылки на сайте:</td>
        <td><?= $hostinfo->hout; ?></td>

    </tr>
    <tr>
        <td>Ссылки на сайте уровень 1:</td>
        <td><?= $hostinfo->hout_l1; ?></td>

    </tr>
    <tr>
        <td>Ссылки на сайте уровень 2:</td>
        <td><?= $hostinfo->hout_l2; ?></td>

    </tr>
    <tr>
        <td>Ссылки на сайте уровень 3:</td>
        <td><?= $hostinfo->hout_l3; ?></td>

    </tr>
    <tr>
        <td>Ссылки на сайте уровень 4:</td>
        <td><?= $hostinfo->hout_l4; ?></td>

    </tr>
    <tr>
        <td>Получатели:</td>
        <td><?= $hostinfo->dout; ?></td>

    </tr>
    <tr>
        <td>Анкоры:</td>
        <td><?= $hostinfo->anchors; ?></td>

    </tr>
    <tr>
        <td>Исходящие анкоры:</td>
        <td><?= $hostinfo->anchors_out; ?></td>

    </tr>
    <tr>
        <td>iGood доноров:</td>
        <td><?= $hostinfo->igood; ?></td>

    </tr>
    </table>
<? else: ?>
    Нет информации на эту дату.
<? endif; ?>
</div>
<div class="tab-pane row-fluid <? if ($tab == 2): ?> active<? endif; ?>" id="tab_2">

    <div class="row-fluid">
        <div class="span12">
            <form method="get" id="region-change-form">
                <input type="hidden" name="tab" value="2"/>

                <div class="dataTables_filter">
                    <label
                        class="filters">Регион:  <?php echo CHtml::dropDownList("region", $region, $regions, array('class' => 'm-wrap medium', 'onchange' => '$("#region-change-form").submit();')); ?></label>
                </div>
            </form>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>Ключевая фраза</th>

                    <? foreach ($dates as $d => $key): ?>
                        <td>
                            <?= Yii::app()->dateFormatter->formatDateTime($d, 'medium', false); ?>
                        </td>
                    <? endforeach; ?>
                </tr>
                </thead>
                <? foreach ($statkeys as $key => $positions): ?>
                    <tr>
                        <td>
                            <? if (!isset($info[$key]) || $info[$key]->url == '0'): ?>
                                <?= $keywordsData[$key] ?>
                            <? else: ?>
                                <a href="<?= $info[$key]->url ?>" class="popovers" data-trigger="hover"
                                   data-placement="bottom" data-content="<?= $info[$key]->passage ?>"
                                   title="<?= $info[$key]->url ?>" target="_blank"><?=$keywordsData[$key];?></a>
                            <? endif; ?>
                        </td>

                        <? foreach ($positions as $pos): ?>
                            <td>
                                <?//=(($pos>100 || $pos<1)? '&mdash;' : $pos);?>
                                <?=$pos;?>
                            </td>
                        <? endforeach; ?>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
    </div>


</div>

<div class="tab-pane row-fluid <? if ($tab == 3): ?> active<? endif; ?>" id="tab_3">

    <div class="row-fluid">
        <div class="span12">
            <form method="get" id="region-change-form2">
                <input type="hidden" name="tab" value="3"/>
                <table>
                    <tr>
                        <td>
                            <label
                                class="filters">Регион:  <?php echo CHtml::dropDownList("region", $region, $regions, array('class' => 'm-wrap medium', 'onchange' => '$("#region-change-form2").submit();')); ?></label>
                        </td>
                        <td>
                            <label class="filters">&nbsp;&nbsp;Ключевая
                                фраза:  <?php echo CHtml::dropDownList('keyword', $keyword, $keywordsData, array('class' => 'm-wrap medium', 'onchange' => '$("#region-change-form2").submit();')); ?></label>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>Позиция</th>
                    <th>Ссылка</th>
                    <th>Заголовок</th>
                    <th>Фраза</th>
                </tr>
                </thead>
                <? foreach ($rivals->getData() as $query): ?>
                    <tr>
                        <td>
                            <? if ($query->domain == $project->domain): ?>
                                <b><?= $query->position ?></b>
                            <? else: ?>
                                <?= $query->position ?>
                            <? endif; ?>
                        </td>
                        <td>
                            <? if ($query->domain == $project->domain): ?>
                                <b><a href="<?= $query->url ?>" class="popovers" data-trigger="hover"
                                      data-placement="bottom" data-content="<?= $query->passage ?>"
                                      title="<?= $query->url ?>" target="_blank"><?= $query->domain ?></a></b>
                            <? else: ?>
                                <a href="<?= $query->url ?>" class="popovers" data-trigger="hover"
                                   data-placement="bottom" data-content="<?= $query->passage ?>"
                                   title="<?= $query->url ?>" target="_blank"><?= $query->domain ?></a>
                            <? endif; ?>
                        </td>
                        <td>
                            <? if ($query->domain_id == $project->domain_id): ?>
                                <b>                                    <?= $query->domain; ?></b>
                            <? else: ?>
                                <?= $query->domain; ?>
                            <? endif; ?>


                        </td>
                        <td>
                            <? if ($query->domain_id == $project->domain_id): ?>
                                <b>                                                    <?= $query->passage; ?></b>
                            <? else: ?>
                                <?= $query->passage; ?>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
    </div>

</div>

<div class="tab-pane row-fluid <? if ($tab == 4): ?> active<? endif; ?>" id="tab_4">
	<div class="row-fluid">
        <div class="span12">
            <form method="get" id="region-change-form4">
                <input type="hidden" name="tab" value="4"/>
                <table>
                    <tr>
                        <td>
                            <label
                                class="filters">Срок:  <?php echo CHtml::dropDownList('days', $days, $daysData, array('class' => 'm-wrap medium', 'onchange' => '$("#region-change-form4").submit();')); ?></label>
                        </td>
                        <td>
                            <label class="filters">Format:  <?php echo CHtml::dropDownList('format', $format, $formatData, array('class' => 'm-wrap medium', 'onchange' => '$("#region-change-form4").submit();')); ?></label>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
	<a class='btn green' href='http://seo.seo-experts.com/seo/project/xls/<?=$project->id?>?type=html&days=7' target='_blank'>Скачать мониторинги за 7 дн. в HTML</a>
<a class='btn green' href='http://seo.seo-experts.com/seo/project/xls/<?=$project->id?>?type=html&days=30' target='_blank'>Скачать мониторинги за 30 дн. в HTML</a>
<a class='btn green' href='http://seo.seo-experts.com/seo/project/xls/<?=$project->id?>?type=xls&days=7' target='_blank'>Скачать мониторинги за 7 дн. в XLSx</a>
<a class='btn green' href='http://seo.seo-experts.com/seo/project/xls/<?=$project->id?>?type=xls&days=30' target='_blank'>Скачать мониторинги за 30 дн. в XLSx</a>

<a class='btn red' href='http://seo.seo-experts.com/seo/project/xls/<?=$project->id?>?type=pdf&days=7' target='_blank'>Скачать мониторинги за 7 дн. в PDF</a>
<a class='btn red' href='http://seo.seo-experts.com/seo/project/xls/<?=$project->id?>?type=pdf&days=30' target='_blank'>Скачать мониторинги за 30 дн. в PDF</a>
</div>

</div>
</div>

</div>
</div>
</div>
</div>
</div>
</div>