<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/data-tables/DT_bootstrap.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/jquery.yiigridview.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-jqueryui.js"); ?>
<? $this->clientScript->registerScript("uilist", "UIJQueryUI.init();"); ?>

<div class="row-fluid  content-blocked">        
    <div class="span12">
        <div class="portlet-body">                        
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
                        <? foreach ($list->getData() as $query): ?>
                            <tr>
                                <td>
                                    <? if ($query->domain == $domain): ?>
                                        <b><?= $query->position ?></b>
                                    <? else: ?>
                                        <?= $query->position ?>
                                    <? endif; ?>
                                </td>
                                <td>
                                    <? if ($query->domain == $domain): ?>
                                        <b><a href="<?= $query->url ?>" class="popovers" data-trigger="hover" data-placement="bottom" data-content="<?= $query->passage ?>" title="<?= $query->url ?>" target="_blank"><?= $query->domain ?></a></b>
                                    <? else: ?>
                                        <a href="<?= $query->url ?>" class="popovers" data-trigger="hover" data-placement="bottom" data-content="<?= $query->passage ?>" title="<?= $query->url ?>" target="_blank"><?= $query->domain ?></a>
                                    <? endif; ?>                                    
                                </td>
                                <td>
                                    <? if ($query->domain == $domain): ?>
                                        <b>                                    <?= $query->label; ?></b>
                                    <? else: ?>
                                        <?= $query->label; ?>
                                    <? endif; ?>                                    


                                </td>
                                <td>
                                    <? if ($query->domain == $domain): ?>
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
    </div>
</div>


<div id="remove-element" title="Удалить элемент" class="hide">
    <p><span class="icon icon-warning-sign"></span>
        Вы уверены что хотите удалить элемент?</p>
</div>
