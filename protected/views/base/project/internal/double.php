

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Дублирование страниц</h5>

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

    
    
    <div>
                <div class="panel block-result">
                	<table class="table">
                		<thead>
                			<tr>
                				<th>Имя</th>
                				<th>Значение</th>
                			</tr>
                		</thead>
                		<tbody>

                        	<tr>
                        		<td class="text-nowrap">Редирект с www на без www (или наоборот)</td>
                        		<td><? if($www):?>настроен<?else:?>не настроен <?endif;?></td>
                        	</tr>
				<tr>
					<td class="text-nowrap">Редирект с http на https</td>
					<td><? if($https):?>настроен<?else:?>не настроен<?endif;?></td>
				</tr>
				<tr>
					<td class="text-nowrap">ТОЛЬКО со / на конце или ТОЛЬКО без / на конце</td>
					<td>настроен/не настроен</td>
				</tr>
				<tr>
					<td class="text-nowrap">Найдено дубликатов страниц:</td>
					<td>{цифра}</td>
				</tr>
		                    <?/*
		                        foreach ($meta as $param) {
		                        	list($tmp, $name) = explode('-', $param->name, 2);

		                        	?>
		                        	<tr>
		                        		<td class="text-nowrap"><?= $name ?></td>
		                        		<td><?= $param->value ?></td>
		                        	</tr>
		                        	<?

		                        }

		                        foreach ($htag as $param) {
		                        	?>
		                        	<tr>
		                        		<td class="text-nowrap"><?= $param->name ?></td>
		                        		<td><?= htmlspecialchars($param->value) ?></td>
		                        	</tr>
		                        	<?

		                        }*/
		                    ?>
                    	</tbody>
                    </table>
                </div>
            </div>
        <?

        foreach ($pages as $sm): 
            $p = $sm->page; 

            if (!$p) continue;

            $meta = $p->meta();
            $htag = $p->hTag();

            $n = count($meta) + count($htag);

            if ($sm->title) $n++;

            ?>

            <div>
                <a href="#" class="jUrl"><?= $sm->url ?></a> (<?= $n ?>) &nbsp; 

                <a href="<?= $sm->url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; 

                <div class="panel block-result hide">
                	<table class="table">
                		<thead>
                			<tr>
                				<th>Имя</th>
                				<th>Значение</th>
                			</tr>
                		</thead>
                		<tbody>

                        	<tr>
                        		<td class="text-nowrap">title</td>
                        		<td><?= $sm->title ?></td>
                        	</tr>

		                    <?
		                        foreach ($meta as $param) {
		                        	list($tmp, $name) = explode('-', $param->name, 2);

		                        	?>
		                        	<tr>
		                        		<td class="text-nowrap"><?= $name ?></td>
		                        		<td><?= $param->value ?></td>
		                        	</tr>
		                        	<?

		                        }

		                        foreach ($htag as $param) {
		                        	?>
		                        	<tr>
		                        		<td class="text-nowrap"><?= $param->name ?></td>
		                        		<td><?= htmlspecialchars($param->value) ?></td>
		                        	</tr>
		                        	<?

		                        }
		                    ?>
                    	</tbody>
                    </table>
                </div>
            </div>


            <? 
        endforeach; 

        ?>

        <script type="text/javascript">
        $(function(){
            $('.jUrl').click(function(){
                $(this).parent().find('.panel').toggleClass('hide');
                return false;
            });
        });
        </script>

    </div>
</div>

