<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/data-tables/DT_bootstrap.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/jquery.yiigridview.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-jqueryui.js"); ?>
<? $this->clientScript->registerScript("uilist", "UIJQueryUI.init();"); ?>

<div class="row-fluid  content-blocked">        
    <div class="span12">
        <div class="portlet-body">            
            <div class="row-fluid">
                <div class="span4">
                    <div class="clearfix">
                        <div class="btn-group">
                            <? foreach ($this->actions as $action): ?>
                                <a href="<?= $action["url"] ?>" class="btn <?= $action["color"] ?>"><?= $action["label"] ?> <i class="icon-<?= $action["icon"] ?>"></i></a>
                            <? endforeach; ?>
                        </div>                        
                    </div>
                </div>
                <div class="span8">
                    <? foreach ($this->filters as $key => $filter): ?>
                        <? if (count($filter["items"]) > 0): ?>
                            <div class="dataTables_filter" >
                                <label class="filters"><?= $filter["label"] ?>:  <?php echo CHtml::dropDownList($key, $filter['value'], $filter["items"], array('class' => 'm-wrap medium')); ?> </label>
                            </div>
                        <? endif; ?>
                    <? endforeach; ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <?
                    $this->widget('CAdminGridView', array(
                        'dataProvider' => $list,
                        'id' => 'table-list',
                        'cssFile' => '',
                        'template' => '{items} {pager}',
                        'itemsCssClass' => 'table table-striped table-bordered table-hover dataTable',
                        'enableSorting' => true,
                        'currentOrder' => $this->order,
                        'columns' => (isset($columns)) ? $columns : $this->columns
                    ));
                    ?>   
                </div>
            </div>
        </div>
    </div>
</div>


<div id="remove-element" title="Удалить элемент" class="hide">
    <p><span class="icon icon-warning-sign"></span>
        Вы уверены что хотите удалить элемент?</p>
</div>
