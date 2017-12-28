<?php

/**
 * This is the model class for table "{{mailevent}}".
 *
 * The followings are the available columns in table '{{mailevent}}':
 * @property integer $id
 * @property integer $active
 * @property string $name
 * @property string $code
 * @property string $to
 * @property string $from
 * @property string $subject
 * @property string $body
 */
class MailEvent extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return VMailEvent the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{mailevent}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, code, to, from, subject', 'required'),
            array('active', 'numerical', 'integerOnly' => true),
            array('name, code, to, from, subject', 'length', 'max' => 250),
            array('body', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, active, name, code, to, from, subject, body', 'safe', 'on' => 'search'),
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
            'active' => 'Активный',
            'name' => 'Название',
            'code' => 'Код',
            'to' => 'Получатель',
            'from' => 'Отправитель',
            'subject' => 'Тема сообщения',
            'body' => 'Шаблон письма',
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
        $criteria->compare('active', $this->active);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('to', $this->to, true);
        $criteria->compare('from', $this->from, true);
        $criteria->compare('subject', $this->subject, true);
        $criteria->compare('body', $this->body, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function beforeValidate() {
        if ($this->code == '') {
            $this->code = Utils::Translit($this->name);
        }
        return parent::beforeValidate();
    }

    public function send($code, $attributes) {
        $event = self::model()->findByAttributes(array('code' => $code));
        if (!isset($event))
            return false;
        $module = Yii::app()->getModule('admin');
        include_once(Yii::app()->basePath . '/modules/main/components/mail/class.phpmailer.php');
        $mail = new PHPMailer();

        if ($module->smtp['active']) {
            $mail->Host = $module->smtp['host'];
            $mail->SMTPDebug = $module->smtp['debug'];
            $mail->SMTPAuth = $module->smtp['auth'];
            $mail->Port = $module->smtp['port'];
            $mail->Username = $module->smtp['username'];
            $mail->Password = $module->smtp['password'];
        }

        $mail->CharSet = 'utf-8';
        $mail->IsHTML(true);
        $mail->from = $event->from;
        $mail->AddAddress($event->to);
        $from = $event->from;
        foreach ($attributes as $key => $value) {
            $from = preg_replace("!#" . $key . "#!", $value, $from);
        }
        $mail->from = $from;
        $to = $event->to;
        foreach ($attributes as $key => $value) {
            $to = preg_replace("!#" . $key . "#!", $value, $to);
        }
        $mail->AddAddress($to);
        $subject = $event->subject;
        foreach ($attributes as $key => $value) {
            $subject = preg_replace("!#" . $key . "#!", $value, $subject);
        }
        $mail->Subject = $subject;
        $message = $event->body;
        foreach ($attributes as $key => $value) {
            $message = preg_replace("!#" . $key . "#!", $value, $message);
        }
        $mail->Body = $message;
        return $mail->Send();
    }

    public function elementsForm() {
        return array(
            'main' => array('label' => 'Основные настройки',
                'attributes' => array(
                    'active' => array('type' => 'checkbox'),
                    'name' => array('type' => 'text', 'htmlOptions' => array('class' => 'span8')),
                    'code' => array('type' => 'text', 'htmlOptions' => array('class' => 'span8')),
                    'to' => array('type' => 'text', 'htmlOptions' => array('class' => 'span8')),
                    'from' => array('type' => 'text', 'htmlOptions' => array('class' => 'span8')),
                    'subject' => array('type' => 'text', 'htmlOptions' => array('class' => 'span8')),
                    'body' => array('type' => 'html'),
                )
            )
        );
    }
    
    public function getLabel()            
    {
        return $this->name;
    }

}