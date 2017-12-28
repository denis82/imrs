<?php

/**
 * Модель формы регистрации
 * 
 */
class RegistrationForm extends CFormModel {

    public $username;
    public $password;
    public $name;
    public $subscribe = true;
    public $terms = false;

    private $user;

    public function rules() {
        return array(
            array('username, password, name', 'required'),
			array('subscribe, terms', 'boolean',),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'username' => 'Ваш email',
            'password' => 'Пароль',
            'name' => 'Имя Фамилия',
			'subscribe' => 'Подписаться на рассылку',
			'terms' => 'Принимаю правила сайта',
        );
    }

	public function beforeValidate() {
		if (parent::beforeValidate()){
            $user = User::model()->findByAttributes(array('username' => $this->username));

            if ($user and $user->id) {
                $this->addError('username', 'Пользователь с таким email уже существует.');
            }

            if (!$this->terms) {
                $this->addError('terms', 'Прочитайте и примите правила сайта.');
                return false;
            }

            return (!$this->hasErrors());
        } else {
            return false;
        }
	}

    public function save() {
        $salt = CPasswordHelper::generateSalt();

        $user = new User;
        $user->attributes = array(
            'active' => 1,
            'username' => $this->username,
            'email' => $this->username,
            'password' => $user->hashPassword($this->password, $salt),
            'name' => $this->name,
            'role' => 'user',
            'salt' => $salt,
            'subscribe' => intval($this->subscribe),
        );
        $user->save();

        if ($user and $user->id) {
            $this->user = new CAdminUserIdentity($user->username, $this->password);
            $this->user->authenticate();

            Yii::app()->user->login($this->user, 3600 * 24 * 3);

            $message          = new YiiMailMessage;
            $message->view    = 'registration';
            $message->from    = 'seo@seo-experts.com';
            $message->subject = 'Вы успешно зарегистрировались на ' . parse_url( Yii::app()->getBaseUrl(true), PHP_URL_HOST );
            $message->setBody(
                array('user' => $user), 
                'text/html'
            );
            $message->addTo($user->email);
            Yii::app()->mail->send($message);   

            return true;
        }
        else {
            $e = $user->getErrors();
        }

        $this->addError('username', 'Непредвиденная ошибка. Попробуйте ещё раз.');

        return false;
    }

}


