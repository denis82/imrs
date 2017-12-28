<?php

class ProjectsScreenshot extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{projects_screenshot}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('project_id', 'required'),
            array('project_id, width', 'numerical', 'integerOnly' => true),
            array('id, project_id, image', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
			'id' => 'ID',
			'project_id' => 'Project ID',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
		$criteria->compare('project_id', $this->region_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function download( $project, $width = 1024 ) {
        if (is_numeric($project)) {
            $project = Project::model()->findByPk( $project );
        }

        if ($project and $project instanceof Project) {
            $url = 'http://mini.s-shot.ru/'.$width.'/'.$width.'/png/?' . $project->host;

            if ($screen = @file_get_contents($url)) {
                $a = parse_url($project->host);

                $name = '/upload/screenshot/' . $a['host'] . '-' . $width . date('-YmdHis') . '.png';
                file_put_contents(Yii::app()->basePath . '/..' . $name, $screen);

                $obj = new self;
                $obj->project_id = $project->id;
                $obj->width = $width;
                $obj->image = $name;
                $obj->save();

                return $obj;
            }
        }

        return false;
    }

}
