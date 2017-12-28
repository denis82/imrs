<?php

class UsabilityController extends CProjectController
{

    public $name = 'Юзабилити';
    public $title = 'Юзабилити';
    public $description = '';

    public function actionIndex($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->checkProject();
    	$this->genBreadcrumbs();

        $this->render('project.usability.index');
    }

    public function actionAdaptive($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Адаптивность сайта';
        $this->checkProject();
        $this->genBreadcrumbs();

        if ($page = $model->domain->sitemap[0]->page) {
            if ($viewport = $page->param('meta-viewport')) {
                $parsed = new Viewport($viewport->value);
            }
        }

        $this->render('project.usability.adaptive', array(
            "model" => $model,
            "param" => $viewport,
            "viewport" => $parsed,
        ));
    }

    public function actionMobile($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Мобильная версия';
        $this->checkProject();
        $this->genBreadcrumbs();

        $useragent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25';


        $context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false,
                    'header' => "User-Agent: " . $useragent . "\r\n"
                )
            )
        );

        $data = @file_get_contents($model->domain->url(), false, $context);

        foreach ($http_response_header as $line) {
            list($a, $b) = explode(':', $line, 2);

            if (trim($a) == 'Location') {
                $mobile = trim($b);
            }
        }

        $this->render('project.usability.mobile', array(
            "model" => $model,
            "mobile" => $mobile,
        ));
    }

    public function actionScreensize($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Разрешение экрана';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.dev');
    }

    public function actionBrowser($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Проверка в популярных браузерах';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.dev');
    }

    public function actionHeader($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Анализ "шапки" сайта';
        $this->checkProject();
        $this->genBreadcrumbs();

        $page = $model->domain->mainpage;

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
		$doc->loadHTML( $page->text );
		$result = $this->getAllNodes( $doc );


		$n = 0; $page = null;
		while (!$page) {
			$sm = Sitemap::model()->findByAttributes(array('domain_id' => $model->domain_id), array('order' => 'rand()'));
			$page = $sm->page;
			if (++$n > 100) break;
		}

		if ($page) {
	        $doc = new DOMDocument();
	        libxml_use_internal_errors(true);
			$doc->loadHTML( $page->text );
			$result_r = $this->getAllNodes( $doc );
		}
		else {
			$result_r = array();
		}



        $this->render(
        	'project.usability.header',
        	array(
	            "model" => $model,
	            "dom" => $result,
	            "dom_rand" => $result_r,

	            "page" => $sm,
    		)
        );
    }

    private function getAllNodes( $doc, $n = 0 ) {

    	$result = array();

		foreach ($doc->childNodes as $item) {

			if ($item instanceof DOMElement) {
				$line = str_pad('', $n * 4, ' ') . $item->tagName ;

				if ($item->hasAttributes()) {
					foreach ($item->attributes as $attr) { 
						$line.= ' ' .$attr->name . '="' . $attr->value . '"';
			        } 					
				}

				$result[] = array($line, $n);

				if ($item->hasChildNodes()) {
					$result_sub = $this->getAllNodes($item, $n+1);

					$result = array_merge($result, $result_sub);
				}
			}
		}

		return $result;
    }

    public function actionFooter($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Анализ "подвала" сайта';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.dev');
    }

    public function actionFonts($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Использование сторонних шрифтов';
        $this->checkProject();
        $this->genBreadcrumbs();

        $data = new DataModel( $model );
        $result = $data->usabilityFonts();

        $fonts = $result['fonts'];
        $fonts_source = $result['fonts_source'];

        $this->render('project.usability.fonts', array(
            "model" => $model,
            "fonts" => $fonts,
            "fonts_source" => $fonts_source,
        ));
    }

    public function actionCSS($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Анализ CSS';
        $this->checkProject();
        $this->genBreadcrumbs();

        $total = 0;
        $items = array();

        foreach (DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_CSS)) as $r) {
        	$items[ $r->url ] = array();

        	foreach (PagesResource::model()->findAllByAttributes(array('domain_resource_id' => $r->id)) as $el) {
        		$items[ $r->url ][] = $el->page_id;
        	}
        }

        $this->render('project.usability.css', array(
            "model" => $model,
            "total" => $total,
            "css" => $items,
        ));
    }

    public function actionJS($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Анализ JavaScript';
        $this->checkProject();
        $this->genBreadcrumbs();

        $total = 0;
        $items = array();

        foreach (DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_SCRIPT)) as $r) {
        	$items[ $r->url ] = array();

        	foreach (PagesResource::model()->findAllByAttributes(array('domain_resource_id' => $r->id)) as $el) {
        		$items[ $r->url ][] = $el->page_id;
        	}
        }

        $this->render('project.usability.js', array(
            "model" => $model,
            "total" => $total,
            "js" => $items,
        ));
    }

    public function actionReferences($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Системы отзывов на сайте';
        $this->checkProject();
        $this->genBreadcrumbs();

        $codes = array(
	        'cackle.me' => 'cackle.me',
	        'disqus.com' => 'disqus.com',
	        'bazaarvoice.com' => 'bazaarvoice.com',
	        'mneniya.pro' => 'mneniya.pro',
	        'shoppilot.ru' => 'shoppilot.ru',
	        'commentbook.ru' => 'commentbook.ru',

	        'roistat.com' => 'roistat.com'
        );

        $founded = array();

        foreach ($codes as $j => $i) {
	        $criteria = new CDbCriteria;
	        $criteria->condition = 'domain_id = :id and type = :type and html like :code';
	        $criteria->params = array(
	        	'id' => $model->domain_id,
	        	'type' => DomainsResource::T_SCRIPT,
	        	'code' => '%' . $i . '%'
	        );

	        $result = DomainsResource::model()->findAll( $criteria );

	        if ($result and count($result)) {
	        	$founded[$j] = $result;
	        }
        }

        $result = array();
        foreach (DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_SCRIPT)) as $res) {
        	$code = str_replace("'+'", "", $res->html);

        	if (strpos($code, 'livetex.ru') !== false) {
        		$result[] = $res;
        	}
        }

        if ($result and count($result)) {
	        $founded['livetex.ru'] = $result;
        }

        $this->render('project.usability.references', array(
            "model" => $model,
            "items" => $founded,
        ));
    }

    public function actionConsult($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Он-лайн консультанты';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.dev');
    }

    public function actionGoals($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Цели Яндекс.Метрики';
        $this->checkProject();
        $this->genBreadcrumbs();

        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and t.name like :name';
        $criteria->group = 't.value';
        $criteria->params = array(
        	'id' => $model->domain_id,
        	'name' => 'yandex-goal'
        );

        $this->render('project.usability.goal', array(
        	"model" => $model,
        	"items" => PagesParam::model()->findAll( $criteria ),
        ));
    }

    public function actionTel($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Номера телефонов';
        $this->checkProject();
        $this->genBreadcrumbs();

        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and t.href like :tel';
        $criteria->group = 't.href';
        $criteria->params = array(
        	'id' => $model->domain_id,
        	'tel' => 'tel:%'
        );

        $this->render('project.usability.tel', array(
        	'model' => $model,
        	'tel' => PagesLink::model()->findAll($criteria)
        ));
    }

    public function actionForm($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Формы на сайте';
        $this->checkProject();
        $this->genBreadcrumbs();

        $total = 0;
        $items = array();

        foreach ($model->domain->sitemap as $sm) {
        	$page = $sm->page;

        	if (!$page) continue;

        	$total++;

            foreach ($page->form as $resource) {
            	$j = $resource->hash();

            	if ($j) {
            		$items[ $j ][] = $page->id;
            	}
            }
        }

        $this->render('project.usability.form', array(
            "model" => $model,
            "total" => $total,
            "res" => $items,

            "forms" => DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_FORM))
        ));
    }

    protected function loadTel( $project ) {
        $pages = array();

        $criteria = new CDbCriteria;
        $criteria->select = 'p.*';
        $criteria->alias = 'p';
        $criteria->join = 'left join tbl_pages_links as t on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and t.href like :tel';
        $criteria->group = 'p.id';
        $criteria->limit = '100';
        $criteria->params = array(
        	'id' => $project->domain_id,
        	'tel' => $_GET['tel']
        );

        foreach (Page::model()->findAll($criteria) as $page) {
        	$pages[$page->id] = $page;
        }

        $criteria = new CDbCriteria;
        $criteria->select = 'p.*';
        $criteria->alias = 'p';
        $criteria->join = 'left join tbl_pages_params as t on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and t.name like :name and t.value like :tel';
        $criteria->group = 'p.id';
        $criteria->limit = '100';
        $criteria->params = array(
        	'id' => $project->domain_id,
        	'name' => 'phone',
        	'tel' => $_GET['tel']
        );

        foreach (Page::model()->findAll($criteria) as $page) {
        	$pages[$page->id] = $page;

        	if (count($pages) >= 100) break;
        }

        $this->renderPartial('//../modules/project/views/usability/tel', array(
        	'model' => $model,
        	'pages' => $pages,
        ));

        Yii::app()->end();
    }

    protected function loadMail( $project ) {
        $criteria = new CDbCriteria;
        $criteria->select = 'p.*';
        $criteria->alias = 'p';
        $criteria->join = 'left join tbl_pages_links as t on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and (t.href like :mail or t.href like :mailtheme)';
        $criteria->group = 'p.id';
        $criteria->limit = '100';
        $criteria->params = array(
        	'id' => $project->domain_id,
        	'mail' => 'mailto:' . $_GET['link'],
        	'mailtheme' => 'mailto:' . $_GET['link'] . "?%"
        );

        $this->renderPartial('//../modules/project/views/usability/mail', array(
        	'model' => $model,
        	'pages' => Page::model()->findAll($criteria)
        ));

        Yii::app()->end();
    }

    protected function loadGoal( $project ) {
        $criteria = new CDbCriteria;
        $criteria->select = 'p.*';
        $criteria->alias = 'p';
        $criteria->join = 'left join tbl_pages_params as t on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and t.name like :name and t.value like :value';
        $criteria->group = 'p.id';
        $criteria->limit = '100';
        $criteria->params = array(
        	'id' => $project->domain_id,
        	'name' => 'yandex-goal',
        	'value' => $_GET['value']
        );

        $this->renderPartial('//../modules/project/views/usability/goal', array(
        	'model' => $model,
        	'pages' => Page::model()->findAll($criteria)
        ));

        Yii::app()->end();
    }

    protected function loadForm( $project ) {
        $criteria = new CDbCriteria;
        $criteria->select = 'p.*';
        $criteria->alias = 'p';
        $criteria->join = 'left join tbl_pages_resources as t on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and t.domain_resource_id = :dres and t.type = :type';
        $criteria->group = 'p.id';
        $criteria->limit = '100';
        $criteria->params = array(
        	'id' => $project->domain_id,
        	'dres' => $_GET['form_id'],
        	'type' => DomainsResource::T_FORM
        );

        $this->renderPartial('//../modules/project/views/usability/pages', array(
        	'model' => $model,
        	'pages' => Page::model()->findAll($criteria)
        ));

        Yii::app()->end();
    }

}
