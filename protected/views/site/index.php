<? foreach ($dashboards as $dashboard): ?>
    <div class="row-fluid">
        <? foreach ($dashboard as $d): ?>
            <div class="span<?= $d['grid'] ?>">
                <? $this->renderPartial($d['view'], $d['options']) ?>
            </div>
        <? endforeach; ?>
    </div>
<? endforeach; ?>