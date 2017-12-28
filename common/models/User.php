<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property bool $active
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $email
 * @property string $photo
 */
class User extends CActiveRecord {
    const defaultAvatar = '/html/assets/images/placeholder.jpg';

    public $avatar;
    public $password_new;
    public $password_confirm;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{users}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, username, role, email', 'required'),
            array('name, username, password, salt, email, role, service', 'length', 'max' => 250),
            //array('email', 'unique', 'message' => 'Пользователь с таким email уже существует'),
            array('username', 'unique', 'message' => 'Пользователь с таким логином уже существует'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, username, password, salt, email, role, service, is_openid, subscribe', 'safe', 'on' => 'search'),
            array('avatar', 'file', 'types' => 'jpg, png, jpeg, gif',
                'allowEmpty' => true, 'on' => 'insert,update'),
            array('password, salt, password_new, password_confirm, photo, active', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'active' => 'Доступ',
            'name' => 'Имя',
            'username' => 'Логин',
            'password' => 'Пароль',
            'salt' => 'Salt',
            'email' => 'E-mail',
            'default' => 'Пользователь по умолчанию',
            'is_openid' => 'Авторизация через OpenId',
            'service' => 'Сервис авторизации OpenId',
            'role' => 'Роль',
            'password_new' => 'Пароль',
            'password_confirm' => 'Пароль еще раз',
            'avatar' => 'Аватар',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('active', $this->active, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('salt', $this->salt, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('default', $this->default, true);
        $criteria->compare('photo', $this->photo, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Checks if the given password is correct.
     * @param string the password to be validated
     * @return boolean whether the password is valid
     */
    public function validatePassword($password) {
        return $this->hashPassword($password, $this->salt) === $this->password;
    }

    /**
     * Generates the password hash.
     * @param string $password
     * @param string $salt
     * @return string hash
     */
    public function hashPassword($password, $salt) {
        return md5($salt . $password);
    }

    /**
     * Generates a salt that can be used to generate a password hash.
     * @return string the salt
     */
    protected function generateSalt() {
        return uniqid('', true);
    }

    public function beforeSave() {
        if ($this->password_new || $this->password_confirm) {
            if ($this->password_new == $this->password_confirm) {
                $this->salt = $this->generateSalt();
                $this->password = md5($this->salt . $this->password_new);
            } else {
                $this->addError('password_new', 'Пароли не совпадают.');
                return false;
            }
        }
        return parent::beforeSave();
    }

    public function getSmallAvatar() {
        if ($this->photo)
            return CImageHelper::crop($this->photo, 60, 60);
    }

    public function behaviors() {
        return array(
            'PictureBehavior' => array(
                'class' => 'CPictureBehavior',
                'fields' => array('avatar' => 'photo'),
            )
        );
    }

    public function confirmation( $action = 'undefined' ) {
        return hash('sha256', $ths->email . $this->id . $this->password . $action);
    }

}