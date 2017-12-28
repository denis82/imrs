<?php

/**
 * Модель формы профиля
 * 
 */
class ProfileForm extends CFormModel {
    public $name;
    public $avatar;

    private $user;

    public function rules() {
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 250),
            array('avatar', 'file', 'types' => 'jpg, png, jpeg, gif',
                'allowEmpty' => true, 'on' => 'insert,update'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'name' => 'Имя Фамилия',
            'avatar' => 'Аватар',
        );
    }

    public function save() {
        $user = User::model()->findByPk( Yii::app()->user->id );

        $user->name = $this->name;

        $_FILES['User'] = $_FILES['ProfileForm'];

        if ($user->save()) {
            return true;
        }

        $this->addError('username', 'Непредвиденная ошибка. Попробуйте ещё раз.');

        return false;
    }

}


