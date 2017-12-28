<?php

class ContentController extends CProjectController
{

    public $name = 'Контент';
    public $title = 'Контент';
    public $description = '';

    public function actionIndex($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->checkProject();
    	$this->genBreadcrumbs();

        $this->render('project.internal.index');
    }

    public function actionUniqtext($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Уникальность Текстов';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.content.uniqtext', array(
            "model" => $model,
        ));
    }

    public function actionUniqimage($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Уникальность картинок';
        $this->checkProject();
        $this->genBreadcrumbs();

		$criteria = new CDbCriteria;
		$criteria->order = 'id';

		$total = DomainsResource::model()->countByAttributes(
			array(
            	'domain_id' => $model->domain_id,
            	'type' => DomainsResource::T_IMAGE,
        	)
        );

		$pages = new CPagination( $total );
		$pages->pageSize = 50;

		$items = DomainsResource::model()->findAllByAttributes(
			array(
            	'domain_id' => $model->domain_id,
            	'type' => DomainsResource::T_IMAGE,
        	),
        	array(
        		'limit' => $pages->getLimit(),
        		'offset' => $pages->getOffset(),
        	)
		);

    	$last_update = Queue::model()->findStageForProject( $model, 10 );

        $this->render('project.content.uniqimage', array(
        	"last_update" => $last_update ? $last_update->updated_date : null,
            "model" => $model,
            "items" => $items,
            "paginator" => $pages,
        ));
    }

    public function actionSpelling($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Проверка орфографии страниц';
        $this->checkProject();
        $this->genBreadcrumbs();

        $data = Sitemap::model()->findAllByAttributes(array('domain_id' => $model->domain->id, 'status' => 200), array('order' => 'id asc'));
        $pages = array();

        foreach ($data as $item) {
        	$spell = $item->page->spell;

        	if ($spell and $spell->hasErrors()) {
        		$pages[] = (object) array(
        			'url' => $item->url,
        			'page' => $item->page,
        			'short' => $spell->shortText(),
        		);
        	}
        }

    	$last_update = Queue::model()->findStageForProject( $model, 17 );

        $this->render('project.content.spelling', array(
	    	"last_update" => $last_update ? $last_update->updated_date : null,
            "model" => $model,
            "pages" => $pages,
        ));
    }

    public function actionCompany($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Наличие ИНН / ОГРН';
        $this->checkProject();
        $this->genBreadcrumbs();

        $inn = array();
        $ogrn = array();

        foreach ($model->domain->sitemap as $sm) {
            $page = $sm->page;

            if ($page) {
                foreach ($page->params as $param) {
                    if ($param->name == 'inn' or $param->name == 'ogrn') {
                        $name = $param->name;

                        if (!in_array($param->value, $$name)) {
                            ${$name}[] = $param->value;
                        }
                    }
                }
            }
        }

        $tel_criteria = new CDbCriteria;
        $tel_criteria->alias = 't';
        $tel_criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $tel_criteria->condition = 'p.domain_id = :id and t.href like :tel';
        $tel_criteria->group = 't.href';
        $tel_criteria->params = array(
        	'id' => $model->domain_id,
        	'tel' => 'tel:%'
        );

        $phone_criteria = new CDbCriteria;
        $phone_criteria->alias = 't';
        $phone_criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $phone_criteria->condition = 'p.domain_id = :id and t.name like :name';
        $phone_criteria->group = 't.value';
        $phone_criteria->params = array(
        	'id' => $model->domain_id,
        	'name' => 'phone'
        );

        $mail_criteria = new CDbCriteria;
        $mail_criteria->alias = 't';
        $mail_criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $mail_criteria->condition = 'p.domain_id = :id and t.href like :href';
        $mail_criteria->group = 't.href';
        $mail_criteria->params = array(
        	'id' => $model->domain_id,
        	'href' => 'mailto:%'
        );

        $mails = array();
        foreach (PagesLink::model()->findAll($mail_criteria) as $link) {
        	list($j, $i) = explode('?', substr($link->href, 7), 2);

        	if (!isset( $mails[ strtolower($j) ] )) {
        		$mails[ strtolower($j) ] = $j;
        	}
        }

    	$last_update = Queue::model()->findStageForProject( $model, 10 );

        $this->render('project.content.company', array(
	    	"last_update" => $last_update ? $last_update->updated_date : null,
            "model" => $model,
            "pages" => $model->domain->sitemap,
            "params" => array(
                'inn' => $inn, 'ogrn' => $ogrn
            ),

        	'tel' => PagesLink::model()->findAll( $tel_criteria ),
        	'phone' => PagesParam::model()->findAll( $phone_criteria ),

        	'mail' => $mails,
        ));
    }

    public function actionPorno($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Наличие запрещенного контента';
        $this->checkProject();
        $this->genBreadcrumbs();

		$page_ids = Yii::app()->db->createCommand()
		    ->select('distinct(p.page_id)')
		    ->from('tbl_pages_phrase as p left join tbl_vocab_stop as v on (p.phrase = v.word)')
		    ->where('p.domain_id = :id and !(v.word is null)', array(':id' => $model->domain_id))
		    ->queryColumn();

		$pages = array();

		if (is_array($page_ids) and count($page_ids)) {
	        $criteria = new CDbCriteria;
	        $criteria->addInCondition('id', $page_ids);
	        $pages = Page::model()->findAll( $criteria );
		}


        $this->render('project.content.porno', array(
            "model" => $model,
            "pages" => $pages,
        ));
    }

    public function actionPrivate($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Политика конфиденциальности и Cookies';
        $this->checkProject();
        $this->genBreadcrumbs();

        $criteria = new CDbCriteria;
        $criteria->condition = '`domain_id` like :id and (`text` like :polit or `text` like :personal)';
        $criteria->params = array(
        	'id' => $model->domain_id,
        	'polit' => '%Политика конфиденциальности%',
        	'personal' => '%Персональных данных%',
        );
        $criteria->limit = 101;

        $pages = Page::model()->findAll( $criteria );

        $headers = array();
        if ($model->domain->headers[0]) {
        	$headers = $model->domain->headers[0]->getCookie();
        }

        $this->render('project.content.private', array(
            "model" => $model,
            "pages" => $pages,

            "cookies" => $headers,
        ));
    }

    public function actionVideo($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Наличие видео';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.content.video', array(
            "model" => $model,
            "pages" => $model->domain->sitemap,
        ));
    }

    public function actionFiles($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Наличие файлов для скачивания';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.content.files', array(
            "model" => $model,
            "pages" => $model->domain->sitemap,
        ));
    }

    public function actionContacts($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Контактные данные и карта на сайте';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.content.contacts', array(
            "model" => $model,
        ));
    }

    protected function loadSpelling( $project ) {
        $data = Sitemap::model()->findAllByAttributes(array('domain_id' => $project->domain->id, 'status' => 200), array('order' => 'id asc', 'limit' => '10'));

        die();

        echo CJavaScript::jsonEncode(array(
            'html' => $this->renderPartial(
                'spelling', 
                array(
                    'data' => $data, 
                ), 
                true
            )
        ));

        Yii::app()->end();
    }

    protected function loadPagetext( $project ) {
        $data = Sitemap::model()->findByAttributes(array(
            'id' => intval($_POST['sid']),
            'domain_id' => $project->domain->id, 
            'status' => 200
        ));

        $page = $data->pages[0];

        if ($page and $page->id) {
            echo CJavaScript::jsonEncode(array(
                'text' => $page->getText()
            ));
        }
        else {
            echo CJavaScript::jsonEncode(array(
                'html' => 'Страница не найдена'
            ));
        }

        Yii::app()->end();
    }

    protected function loadUniqtext( $project ) {
        $data = Sitemap::model()->findAllByAttributes(array('domain_id' => $project->domain->id, 'status' => 200), array('order' => 'id asc'));

        echo CJavaScript::jsonEncode(array(
            'last_update' => ($data[0] or isset($load_result)) ? TxtHelper::DateTimeFormat( $data[0] ? $data[0]->page->date : time() ) : '',
            'html' => $this->renderPartial(
                'uniqtext', 
                array(
                    'data' => $data, 
                ), 
                true
            )
        ));

        Yii::app()->end();
    }

}
