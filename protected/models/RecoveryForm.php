<?php

/**
 * Модель формы восстановления пароля
 * 
 */
class RecoveryForm extends CFormModel {

    public $username;

    private $user;

    public function rules() {
        return array(
            array('username', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'username' => 'Ваш email',
        );
    }

	public function beforeValidate() {
		if (parent::beforeValidate()){
            $this->user = User::model()->findByAttributes(array('username' => $this->username));

            if (!$this->user or !$this->user->id) {
                $this->addError('username', 'Пользователь с таким e-mail не найден.');
            }

            return (!$this->hasErrors());
        } else {
            return false;
        }
	}

    public function save() {
        $salt = CPasswordHelper::generateSalt();

        $data = new Recovery;
        $data->user_id = $this->user->id;
        $data->code = $salt;
        $data->lifetime = date('Y-m-d H:i:s', time() + 24 * 3600);
        $data->save();

        if ($data and $data->id) {
            $parsed = parse_url( Yii::app()->getBaseUrl(true) );

            $host = $parsed['host'];
            $scheme = $parsed['scheme'];

            $message          = new YiiMailMessage;
            $message->view    = 'recovery';
            $message->from    = 'seo@seo-experts.com';
            $message->subject = 'Восстановление пароля на ' . parse_url( Yii::app()->getBaseUrl(true), PHP_URL_HOST );
            $message->setBody(
                array(
                    'user' => $this->user,
                    'link' => $scheme . '://' . $host . Yii::app()->urlManager->createUrl('site/resetpassword', array(
                        'email' => $this->user->username, 
                        'code' => $data->generateCode()
                    )),
                ), 
                'text/html'
            );
            $message->addTo($this->user->email);
            Yii::app()->mail->send($message);   

            return true;
        }
        else {
            $e = $data->getErrors();
        }

        $this->addError('username', 'Непредвиденная ошибка. Попробуйте ещё раз.');

        return false;
    }

}


