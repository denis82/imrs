<?php

class SecurityController extends CProjectController
{

    public $name = 'Безопасность сайта';
    public $title = 'Безопасность сайта';
    public $description = '';

    public function actionIndex($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->checkProject();
    	$this->genBreadcrumbs();

        $this->render('project.position.index');
    }

    public function actionSsl($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Безопасность соединения';
        $this->checkProject();
        $this->genBreadcrumbs();

        $result = DomainsResult::model()->findByAttributes(array(
        	'domain_id' => $model->domain->id,
        	'name' => 'ssl'
        ));

        if (!$result) {
        	Queue::startCheck($model, 15);
        }

        $this->render('project.security.ssl', array('result' => $result));
    }

    public function actionVirus($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Проверка на вирусы';
        $this->checkProject();
        $this->genBreadcrumbs();

        $result = SecurityVirus::model( $model )->data();

        $this->render('project.security.virus', array('result' => $result));
    }

    public function actionPassword($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Проверка надежности паролей';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.dev');
    }

    public function actionDdos($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Наличие защиты от DDOS атак';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.dev');
    }

    public function actionClickjacking($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Кликджекинг на сайте';
        $this->checkProject();
        $this->genBreadcrumbs();

        $result = array();
        foreach (DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_SCRIPT)) as $r) {
        	$result = array_merge($result, Clickjacking::Test( $r->html ));
        }

        $this->render('project.security.clickjacking', array(
            "model" => $model,
            "codes" => $result,
        ));
    }

    public function actionDirectory($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Просмотр директории сайта';
        $this->checkProject();
        $this->genBreadcrumbs();

        $result = SecurityDirectory::model( $model )->data();

        $this->render('project.security.directory', array('result' => $result));
    }

    public function actionSiteerror($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Ошибки на сайте';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.dev');
    }

}
