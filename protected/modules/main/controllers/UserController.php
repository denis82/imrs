<?php

class UserController extends CListController {

    public $name = "Пользователи";
    public $description = "Управление пользователями";
    public $type = "User";

    public function getColumns($columns = array()) {
        return parent::getColumns(array(
			'name',
			'username',
			'email',
        ));
    }

    public function getForm($element) {
        return array(
            'rows' => array(
                'Авторизация',
                array('username' => array('type' => 'text', 'grid' => '6', 'icon' => 'user')),
                array('password_new' => array('type' => 'password', 'grid' => '6')),
                array('password_confirm' => array('type' => 'password', 'grid' => '6')),
                array('role' => array('type' => 'dropdownlist', 'items' => CHtml::listData(Yii::app()->authManager->roles, "name", "description"), 'grid' => '6', 'htmlOptions' => array('disabled' => $element->default ? 'disabled' : ''))),
                'Профиль',
                !$element->default ? array('active' => array('type' => 'toggle')) : array(),
                array(
                    'avatar' => array('type' => 'picture', 'grid' => '4', 'picture'=>'photo'),
                    'name' => array('type' => 'text', 'grid' => '8'),
                    'email' => array('type' => 'text', 'grid' => '8', 'icon' => 'envelope'),
                ),
            ),
        );
    }

    public function actionProfile() {
        $id = Yii::app()->user->id;

        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $type = $this->type;
        $model = User::model()->findByPk($id);

        $this->breadcrumbs[] = strip_tags($model->name);
        $this->title = strip_tags($model->name);
        $this->description = 'Изменить элемент';

        $password_form = new PasswordForm;
        $password_form->email = $model->email;

        if (isset($_POST['PasswordForm'])) {
            $password_form->attributes = $_POST['PasswordForm'];
            if ($password_form->validate() and $password_form->save()) {
                $password_form->attributes = array(
                    'old_password' => '',
                    'new1_password' => '',
                    'new2_password' => '',
                );
            }
        }

        $profile_form = new ProfileForm;
        $profile_form->attributes = array(
            'name' => $model->name, 
        );

        if (isset($_POST['ProfileForm'])) {
            $profile_form->attributes = $_POST['ProfileForm'];

            if ($profile_form->save()) {
                $this->redirect(Yii::app()->createUrl('main/user/profile'));
            }
        }

        /*if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            if ($model->save()) {
                if (!isset($_POST["apply"])) {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index'));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }
            }
        }*/

        $this->render('user/form', array(
            'password_form' => $password_form, 
            'profile_form' => $profile_form, 
            'model' => $model, 
            'formElements' => $this->getForm($model)
        ));
    }

    public function actionEmail( $id ) {
        $email = $id;

        $id = Yii::app()->user->id;

        $type = $this->type;
        $model = User::model()->findByPk($id);

        if ($model->email == $email and $model->confirmation('email') == $_GET['code']) {
            $model->email_confirmed = 1;
            $model->save();

            $this->redirect(Yii::app()->createUrl('main/user/profile'));
        }

        $this->breadcrumbs[] = strip_tags($model->name);
        $this->title = strip_tags($model->name);
        $this->description = 'Подтверждене e-mail';

        $this->render('user/confirm_email', array('model' => $model));
    }

    public function actionRemove( $id ) {
        $email = $id;

        $id = Yii::app()->user->id;

        $type = $this->type;
        $model = User::model()->findByPk($id);

        $this->breadcrumbs[] = strip_tags($model->name);
        $this->title = strip_tags($model->name);
        $this->description = 'Удаление аккаунта';

        if ($model->email == $email and $model->confirmation('remove') == $_GET['code']) {

            if ($_POST['User'] and $_POST['User']['email'] == $model->email) {
                Yii::app()->user->logout();
                $model->delete();

                $this->render('user/remove_done', array('model' => $model));
            }
            else {
                $this->render('user/remove_form', array('model' => $model));
            }

        }
        else {
            $this->render('user/remove_error', array('model' => $model));
        }
    }

}

?>
