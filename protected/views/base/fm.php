<div class="row-fluid content-blocked">        
    <div class="span12">
        <div class="portlet-body">            
            <div class="row-fluid">
                <div class="span4">
                    <div class="clearfix">
                        <? foreach ($this->treeActions as $action): ?>
                            <a href="<?= $action["url"] ?>" class="btn <?= $action["color"] ?>"><?= $action["label"] ?> <i class="icon-<?= $action["icon"] ?>"></i></a>
                        <? endforeach; ?>
                    </div>                    
                    <? $this->renderPartial('application.views.base._tree', array('tree' => $tree, 'sort' => Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treesort'))); ?>
                </div>
                <div class="span8">
                    <div class="clearfix">
                        <div class="btn-group">
                            <? foreach ($this->actions as $action): ?>
                                <a href="<?= $action["url"] ?>" class="btn <?= $action["color"] ?>"><?= $action["label"] ?> <i class="icon-<?= $action["icon"] ?>"></i></a>
                            <? endforeach; ?>
                        </div>  
                        <? $this->renderPartial('application.views.base._list', array('list' => $list, 'columns' => $this->columns)); ?>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>
<div id="remove-element" title="Удалить элемент" class="hide">
    <p><span class="icon icon-warning-sign"></span>
        Вы уверены что хотите удалить элемент?</p>
</div>