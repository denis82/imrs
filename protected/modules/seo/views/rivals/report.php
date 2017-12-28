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
                                                <?php echo CHtml::dropDownList("sort", $sort, ReportKeywords::model()->attributeSort(), array('class' => 'm-wrap m-ctrl-medium')); ?>
                                            </td>
                                            <td>
                                                <?= CHtml::label("&nbsp;", ""); ?>
                                                <a href="#" class="btn blue" onclick="$('#date-change-form').submit();" style="margin-top: -10px;">Обновить <i class="icon-refresh"></i></a>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>

                        <div class="row-fluid">
                            <div class="span12" style="font-size: 12px;">                                
                                <? $this->renderPartial($orientation, array("grids" => $grids, "all" => $all, "project"=>$project)); ?>
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