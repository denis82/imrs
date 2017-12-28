<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/data-tables/DT_bootstrap.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/jquery.yiigridview.js"); ?>
<?
$this->widget('CAdminGridView', array(
    'dataProvider' => $list,
    'id' => 'table-list',
    'cssFile' => '',
    'template' => '{items} {pager}',
    'itemsCssClass' => 'table table-striped table-bordered table-hover dataTable',
    'columns' => $columns
));
?>