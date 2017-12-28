<?php

/**
 * Модель формы регистрации
 * 
 */
class PasswordForm extends CFormModel {

    public $email;
    public $old_password;
    public $new1_password;
    public $new2_password;

    public $saved = false;

    private $user;

    public function rules() {
        return array(
            array('email, old_password, new1_password, new2_password', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'email' => 'Ваш email',
            'old_password' => 'Старый пароль',
            'new1_password' => 'Новый пароль',
            'new2_password' => 'Повторите пароль',
        );
    }

	public function beforeValidate() {
		if (parent::beforeValidate()){
            $this->user = User::model()->findByPk( Yii::app()->user->id );

            if (!$this->user or !$this->user->id) {
                $this->addError('email', 'Пользователь не найден.');
            }

            if (!$this->user->validatePassword( $this->old_password )) {
                $this->addError('old_password', 'Вы не правильно ввели старый пароль.');
            }

            if ($this->new1_password != $this->new2_password) {
                $this->addError('new2_password', 'Пароли не совпадают.');
            }

            if (!$this->hasErrors()) {
                $this->user->password_new = $this->new1_password;
                $this->user->password_confirm = $this->new2_password;
                
                if ($this->user->save()) {
                    $this->saved = true;
                    return true;
                }
            }
        }

        return false;
	}

    public function save() {
        if ($this->user) {
            $this->user->password_new = $this->new1_password;
            $this->user->password_confirm = $this->new2_password;
            
            if ($this->user->save()) {
                $this->saved = true;
                return true;
            }
        }

        $this->addError('username', 'Непредвиденная ошибка. Попробуйте ещё раз.');

        return false;
    }

}


