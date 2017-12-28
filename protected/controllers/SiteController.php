<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/* class SiteController extends CAdminController { */

class SiteController extends CSiteController {
    public $layout = '//layouts/main';
    public $breadcrumbs = array();

    public function actionIndex() {
        $this->redirect(Yii::app()->urlManager->createUrl('seo/project/index'));
        
		//echo 'ZZZ';
        $this->title = 'Рабочий стол';

        $dashboards = array();
        foreach (Yii::app()->modules as $id => $module) {
            $moduleObject = Yii::app()->getModule($id);
            if (is_a($moduleObject, "CAdminModule")) {
                $dashboard = $moduleObject->getDashboard();
                if (isset($dashboard))
                    $dashboards[] = $dashboard;
            }
        }

        $this->render('index', array('dashboards' => $dashboards));
    }

    public function actionLogin() {
        $this->layout = "//layouts/auth";

        $model = new LoginForm; /* @var $model LoginForm */

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
			
            if($model->validate() && $model->login()){
				$this->redirect(Yii::app()->urlManager->createUrl('seo/project/index'));
                Yii::app()->end();
			}
        }

        $reg_model = new RegistrationForm;

        if (isset($_POST['RegistrationForm'])) {
            $reg_model->attributes = $_POST['RegistrationForm'];
            
            if ($reg_model->validate() && $reg_model->save()) {
                $this->redirect(Yii::app()->urlManager->createUrl('main/user/profile'));
                Yii::app()->end();
            }
        }

        $this->render('login', array(
        	'model' => $model,
        	'reg_model' => $reg_model,
        ));
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->user->returnUrl);
    }

    public function actionRecovery() {
        $this->layout = "//layouts/auth";

        $model = new RecoveryForm;

        if (isset($_POST['RecoveryForm'])) {
            $model->attributes = $_POST['RecoveryForm'];
            
            if ($model->validate() && $model->save()) {

                $this->render('recovery', array(
                    'model' => $model,
                    'result' => true,
                ));

                return ;
            }
        }

        $this->render('recovery', array('model' => $model));
    }

    public function actionResetPassword( $email, $code ) {
        $this->layout = "//layouts/auth";

        Yii::app()->db->createCommand()->delete('tbl_recovery', 'lifetime < now()');

        $user = User::model()->findByAttributes(array('username' => $email));

        if ($user and $user->id) {
            foreach (Recovery::model()->findAllByAttributes(array('user_id' => $user->id)) as $r) {
                if ($r->generateCode() === $code) {
                    $recovery = $r;
                    break;
                }
            }
        }

        if ($recovery and $recovery->id) {

            $model = new ResetPasswordForm;
            $model->recovery = $recovery;

            if (isset($_POST['ResetPasswordForm'])) {
                $model->attributes = $_POST['ResetPasswordForm'];
                
                if ($model->validate() && $model->save()) {
                    $this->redirect(Yii::app()->urlManager->createUrl('main/user/profile'));
                    Yii::app()->end();
                }
            }

        }

        $this->render('reset-password', array('model' => $model));
    }

    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            $this->title = 'Ошибка ' . $error["code"];
            $this->breadcrumbs[] = 'Ошибка ' . $error["code"];
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }
    
	public function actionCrone() {
        Yii::import('application.modules.seo.components.*');
        Yii::import('application.modules.seo.models.*');
        foreach (Project::model()->findAll() as $project) {
            /*
            $d = Domain::model()->findByPk($project->domain_id);
            echo $d->domain."<br/>";
            $project->save();
            */
            foreach (explode(',', $project->keywords) as $keyword) {
                foreach ($project->regions as $lr) {
                    $project->analisisYandexPosition($keyword, $lr);
                    $project->analisisRivals($keyword, $lr);
                }
            }
        }
    }

}

?>
