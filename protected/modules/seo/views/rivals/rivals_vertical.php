    <? if (count($rows) > 0): ?>
        <table class="table  table-bordered table-hover">
            <tr>
                <? foreach (ReportRivals::model()->attributeMain() as $code): ?>
                    <td class="b"><?= ReportKeywords::model()->getAttributeLabel($code) ?></td>
                <? endforeach; ?>
                <? if ($all): ?>
                    <? foreach (ReportRivals::model()->attributeExt() as $code): ?>
                        <td class="b"><?= ReportKeywords::model()->getAttributeLabel($code) ?></td>
                    <? endforeach; ?>
                <? endif; ?>
            </tr>
            <? foreach ($rows as $row): ?>
                <tr>
                    <? foreach (ReportRivals::model()->attributeMain() as $code): ?>
                        <td><?= $row->{$code} ?></td>
                    <? endforeach; ?>
                    <? if ($all): ?>
                        <? foreach (ReportRivals::model()->attributeExt() as $code): ?>
                            <td ><?= $row->{$code} ?></td>
                        <? endforeach; ?>
                    <? endif; ?>
                </tr>
            <? endforeach; ?>
            <tr>
                <? foreach (ReportRivals::model()->attributeMain() as $code): ?>
                    <? if ($code == "vposition"): ?>
                        <td>Медиана</td>
                    <? else: ?>
                        <td><?= $this->calcMediana($code, $rows); ?></td>
                    <? endif; ?>
                <? endforeach; ?>
                <? if ($all): ?>
                    <? foreach (ReportRivals::model()->attributeExt() as $code): ?>
                        <td><?= $this->calcMediana($code, $rows); ?></td>
                    <? endforeach; ?>
                <? endif; ?>
            </tr>
            <tr>
                <? foreach (ReportRivals::model()->attributeMain() as $code): ?>
                    <? if ($code == "vposition"): ?>
                        <td>Отклонение</td>
                    <? else: ?>
                        <? $d = $this->calcDivergence($code, $rows, $project); ?>
                        <td class="<?= is_numeric($d) ? ($d >= 0 ? "green" : "red") : "" ?>">
                            <?= $d; ?>
                        </td>
                    <? endif; ?>
                <? endforeach; ?>
                <? if ($all): ?>
                    <? foreach (ReportRivals::model()->attributeExt() as $code): ?>
                        <? $d = $this->calcDivergence($code, $rows, $project); ?>
                        <td class="<?= is_numeric($d) ? ($d >= 0 ? "green" : "red") : "" ?>">
                            <?= $d; ?>
                        </td>
                    <? endforeach; ?>
                <? endif; ?>
            </tr>
        </table>
    <? else: ?>
        Нет данных.
    <? endif; ?>
