<?php

class ResetPasswordForm extends CFormModel {

    public $password;
    public $password_confirm;

    public $recovery;
    private $user;

    public function rules() {
        return array(
            array('password, password_confirm', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'password' => 'Пароль',
            'password_confirm' => 'Подтвердите пароль',
        );
    }

	public function beforeValidate() {
		if (parent::beforeValidate()){
            $user = $this->recovery->user;

            if (!$user or !$user->id) {
                $this->addError('username', 'Пользователь не найден.');
                return false;
            }

            if ($this->password !== $this->password_confirm) {
                $this->addError('password_confirm', 'Пароли не совпадают.');
                return false;
            }

            return (!$this->hasErrors());
        } else {
            return false;
        }
	}

    public function save() {
        $salt = CPasswordHelper::generateSalt();

        $user = $this->recovery->user;
        $user->password = $user->hashPassword($this->password, $salt);
        $user->salt = $salt;
        $user->save();

        if ($user and $user->id) {
            $this->user = new CAdminUserIdentity($user->username, $this->password);
            $this->user->authenticate();

            Yii::app()->user->login($this->user, 3600 * 24 * 3);

            $this->recovery->delete();

            Recovery::model()->deleteAllByAttributes(array('user_id' => $user->id));

            return true;
        }
        else {
            $e = $user->getErrors();
        }

        $this->addError('password', 'Непредвиденная ошибка. Попробуйте ещё раз.');

        return false;
    }

}


