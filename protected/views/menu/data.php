<?/*
<!-- Main navigation -->
<div class="sidebar-category sidebar-category-visible">
	<div class="category-content no-padding">
		<ul class="navigation navigation-main navigation-accordion">

			<!-- Main -->
			<li
					class="<?= (Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id)) == $_SERVER['REQUEST_URI']) ? 'active' : '' ?>"
			>
				<a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id)); ?>"><i class="icon-hammer-wrench"></i> <span>Внутренняя оптимизация</span></a>
				<ul>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'domain')); ?>">Домен</a></li>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'hosting')); ?>">Хостинг</a></li>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'server')); ?>">Сервер</a></li>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'cms')); ?>">Движок (CMS) сайта</a></li>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'structure')); ?>">Структура сайта</a></li>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'traffic')); ?>">Посещаемость сайта</a></li>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'validator')); ?>">Валидатор микроразметки сайта</a></li>
					<li><a href="<?= Yii::app()->urlManager->createUrl("seo/project/internal", array('id' => $model->id, '#' => 'other')); ?>">Прочее</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-magazine"></i> <span>Контент</span></a>
				<ul>
					<li><a href="">Уникальность</a></li>
					<li><a href="">Полнота контента</a></li>
					<li><a href="">Новизна</a></li>
					<li><a href="">Требования к коммерческим сайтам</a></li>
					<li><a href="">Наличие запрещенного контента</a></li>
					<li><a href="">Прочее</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-meter-fast"></i> <span>Внешняя оптимизация</span></a>
				<ul>
					<li><a href="">ТИЦ Яндекс</a></li>
					<li><a href="">PageRank Google</a></li>
					<li><a href="">Основные каталоги</a></li>
					<li><a href="">Внешние ссылки Ahrefs</a></li>
					<li><a href="">Внешние ссылки seo-expert</a></li>
					<li><a href="">Упоминания домена сайта</a></li>
					<li><a href="">Социальные сигналы</a></li>
					<li><a href="">Прочее</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-strategy"></i> <span>Юзабилити</span></a>
				<ul>
					<li><a href="">Адаптивность сайта</a></li>
					<li><a href="">Кросс-браузерность</a></li>
					<li><a href="">Разрешения экрана</a></li>
					<li><a href="">Шрифты</a></li>
					<li><a href="">Анализ "шапки" сайта</a></li>
					<li><a href="">Анализ "подвала" сайта</a></li>
					<li><a href="">Анализ форм сайта</a></li>
					<li><a href="">Прочее</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-shield-check"></i> <span>Безопасность сайта</span></a>
				<ul>
					<li><a href="">SSL-соединение</a></li>
					<li><a href="">Почта (MX/SPF и Blacklist)</a></li>
					<li><a href="">Проверка на вирусы</a></li>
					<li><a href="">Проверка на уязвимости</a></li>
					<li><a href="">Фильтры поисковых систем</a></li>
					<li><a href="">Надежность паролей</a></li>
					<li><a href="">Прочее</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-office"></i> <span>Бизнес-справочники</span></a>
				<ul>
					<li><a href="">Яндекс.Справочник</a></li>
					<li><a href="">Google Business Center</a></li>
					<li><a href="">2Gis</a></li>
					<li><a href="">Прочие</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-cart2"></i> <span>Прайс-площадки</span></a>
				<ul>
					<li><a href="">Участие сайта в Яндекс.Маркет</a></li>
					<li><a href="">Участие сайта в torg.mail.ru</a></li>
					<li><a href="">«Витрины» Бегуна</a></li>
					<li><a href="">Google Merchant Center</a></li>
					<li><a href="">Участие сайта в Price.ru</a></li>
					<li><a href="">Участие сайта в Авито</a></li>
					<li><a href="">Участие сайта в Aport</a></li>
					<li><a href="">Прочие</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-medal"></i> <span>Репутация компании</span></a>
				<ul>
					<li><a href="">Упоминания в пресс-релизах</a></li>
					<li><a href="">Отзывы в "Отзывы@Mail.Ru"</a></li>
					<li><a href="">Отзывы в Yell</a></li>
					<li><a href="">Прочее</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><i class="icon-stats-growth"></i> <span>Отчеты</span></a>
			</li>
			<!-- /main -->

		</ul>
	</div>
</div>
<!-- /main navigation -->
*/?>

<!-- Main navigation -->
<div class="sidebar-category sidebar-category-visible">
	<div class="category-content no-padding">
		<ul class="navigation navigation-main navigation-accordion">

			<!-- Main -->
			<? if (is_array($items) and count($items)): ?>
				<? foreach ($items as $j): ?>
					<li class="<?= $j['active'] ? 'active' : '' ?>">
						<a href="<?= $j['url'] ?>"><i class="<?= $j['icon'] ?>"></i> <span><?= $j['label'] ?></span></a>

						<? if (is_array($j['items']) and count($j['items'])): ?>
						<ul>
							<? foreach ($j['items'] as $i): ?>
								<li class="<?= $i['active'] ? 'active' : '' ?>"><a href="<?= $i['url'] ?>"><?= $i['label'] ?></a></li>
							<? endforeach; ?>
						</ul>
						<? endif; ?>
					</li>
				<? endforeach; ?>
			<? endif; ?>
			<!-- /main -->

		</ul>
	</div>
</div>
<!-- /main navigation -->
