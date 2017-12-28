<?php

/**
 * Модель формы авторизации
 * 
 * @author Alexandr Kirshin <kirshin.as@gmail.com>
 */
class LoginForm extends CFormModel {

    public $username;
    public $password;
    public $rememberMe = false;
    private $user;
	private $daysRemember = 7;

    public function rules() {
        return array(
            array('username, password', 'required'),
			array('rememberMe', 'boolean',),
            array('password', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
			'rememberMe' => 'Запомнить меня',
        );
    }

	public function beforeValidate(){

		if(parent::beforeValidate()){

			if ($this->user === null) {
				$this->user = new CAdminUserIdentity($this->username, $this->password);
				$this->user->authenticate();
			}

			return true;

		}else{

			return false;
		}

	}

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params) {
        $this->user = new CAdminUserIdentity($this->username, $this->password);

		if (!$this->user->authenticate()) {
            $this->addError('password', 'Неправильное имя пользователя или пароль.');
            $this->password = '';

			return false;
        }

		return true;
    }

    /**
     * Logs in the user using the given password in the model.
     * @return boolean whether login is successful
     */
    public function login(){
/*
		if ($this->user){
			return $this->user->authenticate();
		}else{
			$this->user = new CAdminUserIdentity($this->username, $this->password);
			$this->user->authenticate();
		}

		$valid = $this->validate();

		if ($valid && $this->user->errorCode === CAdminUserIdentity::ERROR_NONE){
			//return \Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0);
			$duration = $this->rememberMe ? 3600 * 24 * $this->daysRemember : 0; // 5 days from property
			return Yii::app()->user->login($this->user, $duration);
		} else {
			return false;
		}
*/
        if ($this->user === null) {
			$this->user = new CAdminUserIdentity($this->username, $this->password);
            //$this->user = new CAdminUserIdentity($this->username, $this->password);
            $this->user->authenticate();
        }

        if ($this->user->errorCode === CAdminUserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? 3600 * 24 * $this->daysRemember : 3600 * 24 * 1; // 5 days from property
            Yii::app()->user->login($this->user, $duration);
            return true;
        }

        else{
			var_dump($this->_identity->errorCode);
			die();
            return false;
		}

    }

}


