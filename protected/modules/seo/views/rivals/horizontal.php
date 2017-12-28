<? foreach ($grids as $keyword => $rows): ?>
    <h2><?= $keyword ?></h2>                                    
    <? if (count($rows) > 0): ?>
        <table class="table  table-bordered table-hover">    
            <? foreach (ReportKeywords::model()->attributeMain() as $code): ?>
                <tr>
                    <td class="b"><?= ReportKeywords::model()->getAttributeLabel($code) ?></td>
                    <? foreach ($rows as $row): ?>
                        <td>
                            <?= $row->{$code}; ?>
                        </td>
                    <? endforeach; ?>
                    <? if ($code == "vposition"): ?>
                        <td>
                            Медиана
                        </td>
                        <td>
                            Отклонение
                        </td>
                    <? else: ?>
                        <td>
                            <?= $this->calcMediana($code, $rows); ?>
                        </td>
                        <? $d = $this->calcDivergence($code, $rows, $project); ?>
                        <td class="<?= is_numeric($d) ? ($d >= 0 ? "green" : "red") : "" ?>">
                            <?= $d; ?>
                        </td>
                    <? endif; ?>
                </tr>
            <? endforeach; ?>
            <? if ($all): ?>
                <? foreach (ReportKeywords::model()->attributeExt() as $code): ?>
                    <tr>
                        <td class="b"><?= ReportKeywords::model()->getAttributeLabel($code) ?></td>
                        <? foreach ($rows as $row): ?>
                            <td>
                                <?= $row->{$code}; ?>
                            </td>
                        <? endforeach; ?>
                        <? if ($code == "vposition"): ?>
                            <td>
                                Медиана
                            </td>
                            <td>
                                Отклонение
                            </td>
                        <? else: ?>
                            <td>
                                <?= $this->calcMediana($code, $rows); ?>
                            </td>
                            <? $d = $this->calcDivergence($code, $rows, $project); ?>
                            <td class="<?= is_numeric($d) ? ($d >= 0 ? "green" : "red") : "" ?>">
                                <?= $d; ?>
                            </td>
                        <? endif; ?>
                    </tr>
                <? endforeach; ?>

            <? endif; ?>
        </table>
    <? else: ?>
        Нет данных.
    <? endif; ?>
<? endforeach; ?>