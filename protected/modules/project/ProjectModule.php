<?php

class ProjectModule extends CAdminModule {

    public $name = 'Проекты';
    public $version = '1.0';
    public $icon = 'globe';

    public function getMenu( $project = null ){
    	if (!$project) {
    		return array();
    	}

    	$id = $project->id;

		$menu = array(
			array(
				'label' => 'Внутренняя оптимизация', 
				'url' => Yii::app()->urlManager->createUrl('project/internal/index', array('id' => $id)), 
				'active' => $this->checkActive('project/internal'),
				'icon' => 'icon-hammer-wrench',
				'items' => array(
					array(
						'label' => 'Домен', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/domain', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/domain', true),
					),
					array(
						'label' => 'Хостинг', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/hosting', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/hosting', true),
					),
					array(
						'label' => 'Сервер', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/server', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/server', true),
					),
					array(
						'label' => 'Движок (CMS) сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/cms', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/cms', true),
					),
					array(
						'label' => 'Структура сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/structure', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/structure', true),
					),
					array(
						'label' => 'Структура и вес страниц', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/weight', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/weight', true),
					),
					array(
						'label' => 'Сравнение структуры сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/structcompare', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/structcompare', true),
					),
					/*array(
						'label' => 'Структура по Яндексу', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/yastruct', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/yastruct', true),
					),*/
					array(
						'label' => 'Ошибки 404',
						'url' => Yii::app()->urlManager->createUrl('project/internal/error404', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/error404', true),
					),
					array(
						'label' => 'Редиректы на сайте', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/redirect', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/redirect', true),
					),
					array(
						'label' => 'Посещаемость сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/traffic', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/traffic', true),
					),
					/*array(
						'label' => 'Валидатор разметки сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/validator', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/validator', true),
					),*/
					array(
						'label' => 'Время загрузки сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/speed', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/speed', true),
					),
					array(
						'label' => 'Мета-теги', 
						'url' => Yii::app()->urlManager->createUrl('project/internal/meta', array('id' => $id)), 
						'active' => $this->checkActive('project/internal/meta', true),
					),
				),
			),
			array(
				'label' => 'Контент', 
				'url' => Yii::app()->urlManager->createUrl('project/content/index', array('id' => $id)), 
				'active' => $this->checkActive('project/content'),
				'icon' => 'icon-magazine',
				'items' => array(
					array(
						'label' => 'Уникальность текстов', 
						'url' => Yii::app()->urlManager->createUrl('project/content/uniqtext', array('id' => $id)), 
						'active' => $this->checkActive('project/content/uniqtext', true),
					),
					array(
						'label' => 'Уникальность картинок', 
						'url' => Yii::app()->urlManager->createUrl('project/content/uniqimage', array('id' => $id)), 
						'active' => $this->checkActive('project/content/uniqimage', true),
					),
					array(
						'label' => 'Проверка орфографии страниц', 
						'url' => Yii::app()->urlManager->createUrl('project/content/spelling', array('id' => $id)), 
						'active' => $this->checkActive('project/content/spelling', true),
					),
					array(
						'label' => 'Контакты', 
						'url' => Yii::app()->urlManager->createUrl('project/content/company', array('id' => $id)), 
						'active' => $this->checkActive('project/content/company', true),
					),
					array(
						'label' => 'Политика конфиденциальности и Cookies', 
						'url' => Yii::app()->urlManager->createUrl('project/content/private', array('id' => $id)), 
						'active' => $this->checkActive('project/content/private', true),
					),
					array(
						'label' => 'Наличие запрещенного контента', 
						'url' => Yii::app()->urlManager->createUrl('project/content/porno', array('id' => $id)), 
						'active' => $this->checkActive('project/content/porno', true),
					),
					array(
						'label' => 'Наличие видео', 
						'url' => Yii::app()->urlManager->createUrl('project/content/video', array('id' => $id)), 
						'active' => $this->checkActive('project/content/video', true),
					),
					array(
						'label' => 'Наличие файлов для скачивания', 
						'url' => Yii::app()->urlManager->createUrl('project/content/files', array('id' => $id)), 
						'active' => $this->checkActive('project/content/files', true),
					),
					/*array(
						'label' => 'Проверка на контактные данные и карты на сайте', 
						'url' => Yii::app()->urlManager->createUrl('project/content/contacts', array('id' => $id)), 
						'active' => $this->checkActive('project/content/contacts', true),
					),*/
				),
			),
			array(
				'label' => 'Юзабилити', 
				'url' => Yii::app()->urlManager->createUrl('project/usability/index', array('id' => $id)), 
				'active' => $this->checkActive('project/usability'),
				'icon' => 'icon-strategy',
				'items' => array(
					array(
						'label' => 'Адаптивность сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/adaptive', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/adaptive', true),
					),
					array(
						'label' => 'Мобильная версия', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/mobile', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/mobile', true),
					),
					array(
						'label' => 'Разрешения экрана', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/screensize', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/screensize', true),
					),
					array(
						'label' => 'Проверка в популярных браузерах', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/browser', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/browser', true),
					),
					array(
						'label' => 'Анализ "шапки" сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/header', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/header', true),
					),
					array(
						'label' => 'Анализ "подвала" сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/footer', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/footer', true),
					),
					array(
						'label' => 'Использование сторонних шрифтов', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/fonts', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/fonts', true),
					),
					array(
						'label' => 'Анализ CSS', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/css', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/css', true),
					),
					array(
						'label' => 'Анализ JavaScript', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/js', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/js', true),
					),
					array(
						'label' => 'Системы отзывов на сайте', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/references', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/references', true),
					),
					array(
						'label' => 'Он-лайн консультанты', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/consult', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/consult', true),
					),
					array(
						'label' => 'Цели Яндекс.Метрики', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/goals', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/goals', true),
					),
					/*array(
						'label' => 'Номера телефонов', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/tel', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/tel', true),
					),*/
					array(
						'label' => 'Формы на сайте', 
						'url' => Yii::app()->urlManager->createUrl('project/usability/form', array('id' => $id)), 
						'active' => $this->checkActive('project/usability/form', true),
					),
				),
			),
			array(
				'label' => 'Внешние ссылки', 
				'url' => Yii::app()->urlManager->createUrl('project/extlinks/index', array('id' => $id)), 
				'active' => $this->checkActive('project/extlinks'),
				'icon' => 'icon-earth',
				'items' => array(
					array(
						'label' => 'Входящие ссылки', 
						'url' => Yii::app()->urlManager->createUrl('project/extlinks/incoming', array('id' => $id)), 
						'active' => $this->checkActive('project/extlinks/incoming', true),
					),
					array(
						'label' => 'Исходящие ссылки', 
						'url' => Yii::app()->urlManager->createUrl('project/extlinks/outgoing', array('id' => $id)), 
						'active' => $this->checkActive('project/extlinks/outgoing', true),
					),
					array(
						'label' => 'Упоминания домена', 
						'url' => Yii::app()->urlManager->createUrl('project/extlinks/mention', array('id' => $id)), 
						'active' => $this->checkActive('project/extlinks/mention', true),
					),
					array(
						'label' => 'Социальные сети', 
						'url' => Yii::app()->urlManager->createUrl('project/extlinks/social', array('id' => $id)), 
						'active' => $this->checkActive('project/extlinks/social', true),
					),
					array(
						'label' => 'Формальные признаки', 
						'url' => Yii::app()->urlManager->createUrl('project/extlinks/formal', array('id' => $id)), 
						'active' => $this->checkActive('project/extlinks/formal', true),
					),
				),
			),
			array(
				'label' => 'Анализ позиций', 
				'url' => Yii::app()->urlManager->createUrl('project/positions/index', array('id' => $id)), 
				'active' => $this->checkActive('project/positions'),
				'icon' => 'icon-graph',
				'items' => array(
					array(
						'label' => 'Анализ слов и фраз сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/positions/words', array('id' => $id)), 
						'active' => $this->checkActive('project/positions/words', true),
					),
					array(
						'label' => 'Семантическое ядро сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/positions/semantic', array('id' => $id)), 
						'active' => $this->checkActive('project/positions/semantic', true),
					),
					array(
						'label' => 'Анализ позиций', 
						'url' => Yii::app()->urlManager->createUrl('project/positions/analyze', array('id' => $id)), 
						'active' => $this->checkActive('project/positions/analyze', true),
					),

					array(
						'label' => 'Яндекс.Метрика', 
						'url' => Yii::app()->urlManager->createUrl('project/positions/yametrika', array('id' => $id)), 
						'active' => $this->checkActive('project/positions/yametrika', true),
					),
				),
			),
			array(
				'label' => 'Безопасность сайта', 
				'url' => Yii::app()->urlManager->createUrl('project/security/index', array('id' => $id)), 
				'active' => $this->checkActive('project/security'),
				'icon' => 'icon-shield-check',
				'items' => array(
					array(
						'label' => 'Безопасность соединения', 
						'url' => Yii::app()->urlManager->createUrl('project/security/ssl', array('id' => $id)), 
						'active' => $this->checkActive('project/security/ssl', true),
					),
					array(
						'label' => 'Проверка на вирусы', 
						'url' => Yii::app()->urlManager->createUrl('project/security/virus', array('id' => $id)), 
						'active' => $this->checkActive('project/security/virus', true),
					),
					/*array(
						'label' => 'Проверка надежности паролей', 
						'url' => Yii::app()->urlManager->createUrl('project/security/password', array('id' => $id)), 
						'active' => $this->checkActive('project/security/password', true),
					),*/
					/*array(
						'label' => 'Наличие защиты от DDOS атак', 
						'url' => Yii::app()->urlManager->createUrl('project/security/ddos', array('id' => $id)), 
						'active' => $this->checkActive('project/security/ddos', true),
					),*/
					array(
						'label' => 'Кликджекинг на сайте',
						'url' => Yii::app()->urlManager->createUrl('project/security/clickjacking', array('id' => $id)), 
						'active' => $this->checkActive('project/security/clickjacking', true),
					),
					array(
						'label' => 'Открытые директории сайта',
						'url' => Yii::app()->urlManager->createUrl('project/security/directory', array('id' => $id)), 
						'active' => $this->checkActive('project/security/directory', true),
					),
					array(
						'label' => 'Ошибки на сайте',
						'url' => Yii::app()->urlManager->createUrl('project/security/siteerror', array('id' => $id)), 
						'active' => $this->checkActive('project/security/siteerror', true),
					),
				),
			),
			/*
			array(
				'label' => 'Внешняя оптимизация', 
				'url' => Yii::app()->urlManager->createUrl('project/internal/index', array('id' => $id)), 
				'active' => $this->checkActive('project/internal'),
				'icon' => 'icon-meter-fast',
				'items' => array(
					array(
						'label' => 'ТИЦ Яндекс', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'PageRank Google', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Основные каталоги', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Внешние ссылки Ahrefs', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Внешние ссылки seo-expert', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Упоминания домена сайта', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Социальные сигналы', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Прочее', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
				),
			),
			array(
				'label' => 'Бизнес-справочники', 
				'url' => Yii::app()->urlManager->createUrl('project/internal/index', array('id' => $id)), 
				'active' => $this->checkActive('project/internal'),
				'icon' => 'icon-office',
				'items' => array(
					array(
						'label' => 'Яндекс.Справочник', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Google Business Center', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => '2Gis', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Прочие', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
				),
			),
			array(
				'label' => 'Прайс-площадки', 
				'url' => Yii::app()->urlManager->createUrl('project/internal/index', array('id' => $id)), 
				'active' => $this->checkActive('project/internal'),
				'icon' => 'icon-cart2',
				'items' => array(
					array(
						'label' => 'Участие сайта в Яндекс.Маркет', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Участие сайта в torg.mail.ru', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => '«Витрины» Бегуна', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Google Merchant Center', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Участие сайта в Price.ru', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Участие сайта в Авито', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Участие сайта в Aport', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Прочие', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
				),
			),
			array(
				'label' => 'Репутация компании', 
				'url' => Yii::app()->urlManager->createUrl('project/internal/index', array('id' => $id)), 
				'active' => $this->checkActive('project/internal'),
				'icon' => 'icon-medal',
				'items' => array(
					array(
						'label' => 'Упоминания в пресс-релизах', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Отзывы в "Отзывы@Mail.Ru"', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Отзывы в Yell', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
					array(
						'label' => 'Прочее', 
						'url' => Yii::app()->urlManager->createUrl('project/', array('id' => $id)), 
					),
				),
			),*/
			array(
				'label' => 'Отчеты', 
				'url' => Yii::app()->urlManager->createUrl('project/report/index', array('id' => $id)), 
				'active' => $this->checkActive('project/report'),
				'icon' => 'icon-stats-growth',
			),
		);
	
        return $menu;
    }

    public function init() {
        Yii::import('application.modules.admin.models.*');
        Yii::import('application.modules.project.models.*');
		return parent::init();
    }

}

