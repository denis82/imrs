<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/data-tables/DT_bootstrap.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/jquery.yiigridview.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-jqueryui.js"); ?>
<? $this->clientScript->registerScript("uilist", "UIJQueryUI.init();"); ?>

<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/bootstrap-datepicker/css/datepicker.css"); ?>
<? $this->clientScript->registerScript("date-picker", "$('.date-picker').datepicker({language:'ru'});"); ?>
<div class="row-fluid">
    <div class="span12">
        <div class="portlet-body">
            <div class="row-fluid">
                <div class="span12">

                    <div class="control-group">
                        <div class="controls">
                            <form id="date-change-form" method="get">
                                <table>
                                    <tr>
                                        <td>
                                            <?= CHtml::label("Дата:", ""); ?>
                                            <div class="input-append date date-picker" data-date-format="dd.mm.yyyy">
                                                <?php echo CHtml::textField("date", $date, array('class' => "m-wrap m-ctrl-medium date-picker")); ?>
                                                <span class="add-on"><i class="icon-calendar"></i></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?= CHtml::label("Регион:", ""); ?>
                                            <?php echo CHtml::dropDownList("region", $region, $regions, array('class' => 'm-wrap m-ctrl-medium')); ?>
                                        </td>
                                        <td>
                                            <?= CHtml::label("Парамтеры:", ""); ?>
                                            <?php echo CHtml::dropDownList("all", $all, array("Основные", "Все"), array('class' => 'm-wrap m-ctrl-medium')); ?>
                                        </td>
                                        <td>
                                            <?= CHtml::label("Ориентация:", ""); ?>
                                            <?php echo CHtml::dropDownList("orientation", $orientation, array("vertical" => "Вертикальная", "horizontal" => "Горизонтальная"), array('class' => 'm-wrap m-ctrl-medium')); ?>
                                        </td>
                                        <td>
                                            <?= CHtml::label("Сортировка:", ""); ?>
                                            <?php echo CHtml::dropDownList("sort", $sort, ReportRivals::model()->attributeSort(), array('class' => 'm-wrap m-ctrl-medium')); ?>
                                        </td>
                                        <td>
                                            <?= CHtml::label("&nbsp;", ""); ?>
                                            <a href="#" class="btn blue" onclick="$('#date-change-form').submit();"
                                               style="margin-top: -10px;">Обновить <i class="icon-refresh"></i></a>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>

                    <div class="tabbable tabbable-custom boxless">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_2" data-toggle="tab">Сравнение</a></li>
                            <li><a href="#tab_1" data-toggle="tab">Количество</a></li>

                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="tab_1">
                                <div class="row-fluid">
                                    <div class="span12">
                                        <?
                                        $this->widget('CAdminGridView', array(
                                            'dataProvider' => $dataProvider,
                                            'id' => 'table-list',
                                            'cssFile' => '',
                                            'template' => '{items} {pager}',
                                            'itemsCssClass' => 'table table-striped table-bordered table-hover dataTable',
                                            'enableSorting' => false,
                                            'columns' => array(
                                                'domain',
                                                array(
                                                    'name' => 'position',
                                                    'type' => 'html',
                                                    'value' => 'CHtml::link(
                        $data->position,
                        Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/domain", array("id"=>' . $project->id . ', "domain_id"=>$data->domain_id, "region_id"=>' . $region . ')),
                        array("target"=>"_blank"));'
                                                ),
                                            )
                                        ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane active" id="tab_2">
                                <div class="scroller">
                                    <div class="row-fluid">
                                        <div class="span12" style="font-size: 12px;">
                                            <? $this->renderPartial("rivals_" . $orientation, array("rows" => $rows, "project" => $project, 'all'=>$all)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    tr.red {
        background-color: #FFA0A2;
    }
    tr.green {
        background-color: lightgreen;
    }
</style>
<style>
    td.red {
        background-color: #FFA0A2;
    }
    td.green {
        background-color: lightgreen;
    }
    td.b {
        font-weight: bold;
    }
    tr.b {
        font-weight: bold;
    }
</style>