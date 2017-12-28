<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/data-tables/DT_bootstrap.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/jquery.yiigridview.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-jqueryui.js"); ?>
<? $this->clientScript->registerScript("uilist", "UIJQueryUI.init();"); ?>

<h2>Регион: <?=$regions[$region]?></h2>
<div class="row-fluid">
    <div class="span12">
        <?
        $this->widget('CAdminGridView', array(
            'dataProvider' => $rows,
            'id' => 'table-list',
            'cssFile' => '',
            'template' => '{items} {pager}',
            'itemsCssClass' => 'table table-striped table-bordered table-hover',
            'enableSorting' => false,
            'columns' => array(
                'keyword',
                'position',
            )
        ));
        ?>   
    </div>
</div>