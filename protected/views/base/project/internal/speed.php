<?

$tpl_name = explode('/', str_replace('.php', '', __FILE__));
$tpl_name = implode('/', array_slice($tpl_name, -3) );

$tpl = TplText::model()->findByAttributes(array('name' => $tpl_name));

if (!$tpl) {
	$tpl = new TplText;
	$tpl->name = $tpl_name;
	$tpl->save();
}

if ($tpl) {
	print '<div class="panel panel-flat"><div class="panel-body jEditablePanel" data-id="'. $tpl->id .'">'. $tpl->html .'</div></div>';
}

if (Yii::app()->user->role == 'administrator') {
	print '<div class="panel panel-flat"><div class="panel-body jStaffPanel" data-name="'. $tpl_name .'">';

	foreach (TplStaff::model()->findAllByAttributes(array('name' => $tpl_name)) as $j) {
		print 
			'<div class="jStaffItem" data-id="' . $j->id . '">' . 
				$j->staff->name . ' // ' . 
				$j->staff->price . 'р * ' . $j->timer . ' = ' . ($j->staff->price * $j->timer) . 
			'</div>';
	}

	print '</div></div>';
}

?>


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Скорость загрузки сайта</h5>
        
        <div class="heading-elements">
            <span class="heading-text jLastUpdate">
                    	<?= $last_update ? '<i class="icon-history position-left text-success"></i> ' . TxtHelper::DateTimeFormat( $last_update ) : '' ?>
            </span>

            <ul class="icons-list">
                <li><a data-action="reload"></a></li>
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">

    	<? $prev = 0; ?>

		<div class="table-responsive col-lg-4">
			<table class="table">
				<tbody>
					<tr>
						<td>DNS</td>
						<td><?
							if ($timer['namelookup']) {
								print number_format($timer['namelookup']->value - $prev, 3) . ' сек';
								$prev = $timer['namelookup']->value;
							} else { print 'нет данных'; }
						?></td>
					</tr>
					<tr>
						<td>Соединение</td>
						<td><?
							if ($timer['connect']) {
								print number_format($timer['connect']->value - $prev, 3) . ' сек';
								$prev = $timer['connect']->value;
							} else { print 'нет данных'; }
						?></td>
					</tr>
					<tr>
						<td>Отправка данных</td>
						<td><?
							if ($timer['pretransfer']) {
								print number_format($timer['pretransfer']->value - $prev, 3) . ' сек';
								$prev = $timer['pretransfer']->value;
							} else { print 'нет данных'; }
						?></td>
					</tr>
					<tr>
						<td>Ожидание ответа</td>
						<td><?
							if ($timer['starttransfer']) {
								print number_format($timer['starttransfer']->value - $prev, 3) . ' сек';
								$prev = $timer['starttransfer']->value;
							} else { print 'нет данных'; }
						?></td>
					</tr>
					<tr>
						<td>Загрузка страницы</td>
						<td><?
							if ($timer['total']) {
								print number_format($timer['total']->value - $prev, 3) . ' сек';
								$prev = $timer['total']->value;
							} else { print 'нет данных'; }
						?></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th>Общее время</th>
						<th><?= $timer['total'] ? $timer['total']->value . ' сек' : 'нет данных' ?></th>
					</tr>
				</tbody>
			</table>
		</div>    	

    </div>
</div>

<div class="row">

	<div class="col-lg-6">

		<div class="panel panel-flat">
		    <div class="panel-heading">
		        <h5 class="panel-title text-semiold">Проверка PageSpeed Desktop</h5>
		    </div>
		    <div class="panel-body">
		    	<? if ($desktop): ?>

			    	<div class="row">
				    	<div class="page-speed-result" id="page_speed_desktop"></div>
				    	<div class="page-speed-desc">
				    		<h2><?= $desktop->score ?>%</h2>
				    	</div>
			    	</div>

			    	<? if ($desktop->rules and count($desktop->rules)): ?>

			    		<? foreach ($desktop->rules as $r): ?>
			    			<?
			    				if ($r->ruleImpact == 0) continue;
			    				elseif ($r->ruleImpact < 10) $style = 'warning';
			    				else $style = 'danger';
			    			?>

							<div class="alert alert-<?= $style ?> no-border">
								<p class="text-semibold"><?= $r->localizedRuleName ?></p>
								<p><?= TxtHelper::googleFormatString($r->summary->format, $r->summary->args) ?></p>
								<p><a href="#desktop<?= $r->name ?>" onclick="$( $(this).attr('href') ).toggleClass('hide'); return false;">Как исправить?</a></p>
							</div>

							<div class="hide mb-20" id="desktop<?= $r->name ?>">
								<?

								if ($r->urlBlocks and is_array($r->urlBlocks)) {
									foreach ($r->urlBlocks as $b) {
										?>
										<p class="text-semibold">
											<?= TxtHelper::googleFormatString($b->header->format, $b->header->args) ?>
										</p>
										<?
										if ($b->urls and is_array($b->urls)) {
											foreach ($b->urls as $u) {
												?>
													<p><?= TxtHelper::googleFormatString($u->result->format, $u->result->args) ?></p>
												<?
											}
										}
									}
								}

								?>
							</div>
						<? endforeach; ?>

			    	<? endif; ?>

			    	<script type="text/javascript">
				    	$(function(){
				    	    progressCounter('#page_speed_desktop', 38, 2, <?= ($desktop->score / 100) ?>, "icon-display4")
				    	});
			    	</script>

			    <? else: ?>

					<div class="alert alert-info alert-styled-left alert-bordered">
						Нет результатов.
					</div>

				<? endif; ?>
		    </div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel panel-flat">
		    <div class="panel-heading">
		        <h5 class="panel-title text-semiold">Проверка PageSpeed Mobile</h5>
		    </div>
		    <div class="panel-body">
		    	<? if ($mobile): ?>

			    	<div class="row">
				    	<div class="page-speed-result" id="page_speed_mobile"></div>
				    	<div class="page-speed-desc">
				    		<h2><?= $mobile->score ?>%</h2>
				    	</div>
			    	</div>

			    	<? if ($mobile->rules and count($mobile->rules)): ?>

			    		<? foreach ($mobile->rules as $r): ?>
			    			<?
			    				if ($r->ruleImpact == 0) continue;
			    				elseif ($r->ruleImpact < 10) $style = 'warning';
			    				else $style = 'danger';
			    			?>

							<div class="alert alert-<?= $style ?> no-border">
								<p class="text-semibold"><?= $r->localizedRuleName ?></p>
								<p><?= TxtHelper::googleFormatString($r->summary->format, $r->summary->args) ?></p>
								<p><a href="#mobile<?= $r->name ?>" onclick="$( $(this).attr('href') ).toggleClass('hide'); return false;">Как исправить?</a></p>
							</div>

							<div class="hide mb-20" id="mobile<?= $r->name ?>">
								<?

								if ($r->urlBlocks and is_array($r->urlBlocks)) {
									foreach ($r->urlBlocks as $b) {
										?>
										<p class="text-semibold">
											<?= TxtHelper::googleFormatString($b->header->format, $b->header->args) ?>
										</p>
										<?
										if ($b->urls and is_array($b->urls)) {
											foreach ($b->urls as $u) {
												?>
													<p><?= (TxtHelper::googleFormatString($u->result->format, $u->result->args)) ?></p>
												<?
											}
										}
									}
								}

								?>
							</div>
						<? endforeach; ?>

			    	<? endif; ?>

			    	<script type="text/javascript">
				    	$(function(){
				    	    progressCounter('#page_speed_mobile', 38, 2, <?= ($mobile->score / 100) ?>, "icon-mobile")
				    	});
			    	</script>

			    <? else: ?>

					<div class="alert alert-info alert-styled-left alert-bordered">
						Нет результатов.
					</div>

				<? endif; ?>
		    </div>
	    </div>
	</div>

</div>

<script type="text/javascript">
    // Initialize charts
    
    //progressCounter('#hours-available-progress', 38, 2, "#F06292", 0.68, "icon-watch text-pink-400", 'Hours available', '64% average')
    //progressCounter('#goal-progress', 38, 2, "#5C6BC0", 0.82, "icon-trophy3 text-indigo-400", 'Productivity goal', '87% average')

    // Chart setup
    function progressCounter(element, radius, border, end, iconClass, textTitle, textAverage) {
    	var color = '#000000';

    	if (end <= 0.25) {
    		color = '#F44336';
    		iconClass += ' text-danger';
    	}
    	else if (end <= 0.50) {
    		color = '#D84315';
    		iconClass += ' text-warning-800';
    	}
    	else if (end <= 0.75) {
    		color = '#FF5722';
    		iconClass += ' text-warning';
    	}
    	else {
    		color = '#4CAF50';
    		iconClass += ' text-success';
    	}


        // Basic setup
        // ------------------------------

        // Main variables
        var d3Container = d3.select(element),
            startPercent = 0,
            iconSize = 32,
            endPercent = end,
            twoPi = Math.PI * 2,
            formatPercent = d3.format('.0%'),
            boxSize = radius * 2;

        // Values count
        var count = Math.abs((endPercent - startPercent) / 0.01);

        // Values step
        var step = endPercent < startPercent ? -0.01 : 0.01;



        // Create chart
        // ------------------------------

        // Add SVG element
        var container = d3Container.append('svg');

        // Add SVG group
        var svg = container
            .attr('width', boxSize)
            .attr('height', boxSize)
            .append('g')
                .attr('transform', 'translate(' + (boxSize / 2) + ',' + (boxSize / 2) + ')');



        // Construct chart layout
        // ------------------------------

        // Arc
        var arc = d3.svg.arc()
            .startAngle(0)
            .innerRadius(radius)
            .outerRadius(radius - border);



        //
        // Append chart elements
        //

        // Paths
        // ------------------------------

        // Background path
        svg.append('path')
            .attr('class', 'd3-progress-background')
            .attr('d', arc.endAngle(twoPi))
            .style('fill', '#eee');

        // Foreground path
        var foreground = svg.append('path')
            .attr('class', 'd3-progress-foreground')
            .attr('filter', 'url(#blur)')
            .style('fill', color)
            .style('stroke', color);

        // Front path
        var front = svg.append('path')
            .attr('class', 'd3-progress-front')
            .style('fill', color)
            .style('fill-opacity', 1);



        // Text
        // ------------------------------

        // Percentage text value
        var numberText = d3.select(element)
            .append('h2')
                .attr('class', 'mt-15 mb-5 hide')

        // Icon
        d3.select(element)
            .append("i")
                .attr("class", iconClass + " counter-icon")
                .attr('style', 'top: ' + ((boxSize - iconSize) / 2) + 'px');

        // Title
        d3.select(element)
            .append('div')
                .text(textTitle);

        // Subtitle
        d3.select(element)
            .append('div')
                .attr('class', 'text-size-small text-muted')
                .text(textAverage);



        // Animation
        // ------------------------------

        // Animate path
        function updateProgress(progress) {
            foreground.attr('d', arc.endAngle(twoPi * progress));
            front.attr('d', arc.endAngle(twoPi * progress));
            numberText.text(formatPercent(progress));
        }

        // Animate text
        var progress = startPercent;
        (function loops() {
            updateProgress(progress);
            if (count > 0) {
                count--;
                progress += step;
                setTimeout(loops, 10);
            }
        })();
    }
</script>
