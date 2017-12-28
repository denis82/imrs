<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/jquery-nestable/jquery.nestable.css"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/jquery-nestable/jquery.nestable.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/scripts/ui-nestable.js"); ?>
<? $this->clientScript->registerScript("uinestable", "UINestable.init();"); ?>
<div class="dd" id="nestable_list" sort-action="<?= $sort ?>">
    <?php
    foreach ($tree as $n => $node) {
        if ($node['level'] == $level)
            echo CHtml::closeTag('li') . "\n";
        else if ($node['level'] > $level)
            echo CHtml::openTag('ol', array('class' => 'dd-list')) . "\n";
        else {
            echo CHtml::closeTag('li') . "\n";
            for ($i = $level - $node['level']; $i; $i--) {
                echo CHtml::closeTag('ol') . "\n";
                echo CHtml::closeTag('li') . "\n";
            }
        }

        echo CHtml::openTag('li', array('class' => 'dd-item dd3-item', 'data-id' => $node['id']));
        echo CHtml::openTag('div', array('class' => 'dd-handle dd3-handle'));
        echo CHtml::closeTag('div');
        echo CHtml::openTag('div', array('class' => 'dd3-content'));
        echo CHtml::link($node['label'], $node['url']);
        echo CHtml::closeTag('div');
        echo CHtml::openTag('div', array('class' => 'dd3-tools'));
        foreach ($node['actions'] as $item) {
            echo CHtml::openTag('a', array('href' => $item['url'], 'class' => 'btn mini ' . $item['color'] . ' ' . $item['class'], 'title' => $item['label']));
            echo CHtml::tag('i', array('class' => 'icon-' . $item['icon']), '');
            echo CHtml::closeTag('a');
        }
        echo CHtml::closeTag('div');
        

        $level = $node['level'];
    }

    for ($i = $level; $i; $i--) {
        echo CHtml::closeTag('li') . "\n";
        echo CHtml::closeTag('ol') . "\n";
    }
    ?>                      
</div>