<? if ($data): ?>

<table class="table">
	<?

		$rows = explode("\n", $data->text);

		foreach ($rows as $line) {
			$j = trim($line);
			$txt = '';

            if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $j)) {
            	$txt = 'Статус';
            }
            else {
            	list($i, $j) = explode(':', $j, 2);

            	$i = trim($i);

            	switch ($i) {
            		case 'Server':
            			$txt = 'Веб-сервер'; break;
            		case 'Date':
            			$txt = 'Дата обращения к серверу'; break;
            		case 'Content-Type':
            			$txt = 'Тип контента, кодировка'; break;
            		case 'Content-Length':
            			$txt = 'Размер страницы, байт'; break;
            		case 'X-Powered-By':
            			$txt = 'Обработчик'; break;
            		case 'ETag':
            			$txt = 'Идентификатор ETag'; break;
            		case 'Expires':
            			$txt = 'Дата экспирации контента'; break;
            		case 'Location':
            			$txt = 'Переадресация'; break;
            		case 'Last-Modified':
            			$txt = 'Дата последнего обновления'; break;
        			case 'Set-Cookie':
        				$txt = 'Параметры cookie'; break;
        			case 'Cache-Control':
        				$txt = 'Параметры кэширования'; break;
            		case 'X-Bitrix-Composite':
            			$txt = 'Код битрикса X-Bitrix-Composite'; break;
        			case 'X-Powered-CMS':
        				$txt = 'Используемая CMS'; break;
            		case 'X-XSS-Protection':
            			$txt = 'Защита от XSS-атак'; break;

            		default: 
            			$txt = $i; break;
            	}



            }
            ?>

            <tr>
            	<td><b><?= $txt ?></b></td>
            	<td><?= $j ?></td>
            </tr>
            
            <?
        }

    ?>

	<tr>
		<td><b>Кэширование на стороне клиента If-Modified-Since</b></td>
		<td>
			<? if ($data->if_modified_since): ?>
				<i class="icon-checkmark4 text-success"></i> &nbsp; используется
			<? else: ?>
				<i class="icon-cross3 text-danger"></i> &nbsp; не используется
			<? endif; ?>
		</td>
	</tr>

</table>


<? else: ?>
<div class="alert alert-info alert-styled-left alert-bordered">
	Информация недоступна в данный момент.
</div>
<? endif; ?>