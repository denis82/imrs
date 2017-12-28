<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/jquery-nestable/jquery.nestable.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/jquery-nestable/jquery.nestable.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-nestable.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-jqueryui.js"); ?>

<? $this->clientScript->registerScript("uinestable", "UINestable.init();   UIJQueryUI.init();"); ?>
<div class="row-fluid content-blocked">        
    <div class="span12">
        <div class="portlet-body">            
            <div class="row-fluid">
                <div class="span12">
                    <div class="clearfix">
                        <? foreach ($this->treeActions as $action): ?>
                            <a href="<?= $action["url"] ?>" class="btn <?= $action["color"] ?>"><?= $action["label"] ?> <i class="icon-<?= $action["icon"] ?>"></i></a>
                        <? endforeach; ?>
                    </div>
                    <div class="dd" id="nestable_list" sort-action="<?= Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treesort') ?>">
                        <?php
                        foreach ($tree as $n => $category) {
                            if ($category->level == $level)
                                echo CHtml::closeTag('li') . "\n";
                            else if ($category->level > $level)
                                echo CHtml::openTag('ol', array('class' => 'dd-list')) . "\n";
                            else {
                                echo CHtml::closeTag('li') . "\n";

                                for ($i = $level - $category->level; $i; $i--) {
                                    echo CHtml::closeTag('ol') . "\n";
                                    echo CHtml::closeTag('li') . "\n";
                                }
                            }
                            echo CHtml::openTag('li', array('class' => 'dd-item dd3-item', 'data-id' => $category->id));
                            echo CHtml::openTag('div', array('class' => 'dd-handle dd3-handle'));
                            echo CHtml::closeTag('div');
                            echo CHtml::openTag('div', array('class' => 'dd3-content'));
                            //echo CHtml::link($this->getTreeLabel($category), Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treeupdate', array('id' => $category->id)));
                            echo $this->getTreeLabel($category);
                            echo CHtml::closeTag('div');
                            echo CHtml::openTag('div', array('class' => 'dd3-tools'));
                            foreach ($this->getItemTreeActions($category) as $item) {
                                echo CHtml::openTag('a', array('href' => $item['url'], 'class' => 'btn mini ' . $item['color'] .' '.$item['class'], 'title' => $item['label']));
                                echo CHtml::tag('i', array('class' => 'icon-' . $item['icon']), '');
                                //echo ' ' . $item['label'];
                                echo CHtml::closeTag('a');
                            }
                            echo CHtml::closeTag('div');
                            $level = $category->level;
                        }

                        for ($i = $level; $i; $i--) {
                            echo CHtml::closeTag('li') . "\n";
                            echo CHtml::closeTag('ol') . "\n";
                        }
                        ?>                      
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
