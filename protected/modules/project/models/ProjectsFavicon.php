<?php

class ProjectsFavicon extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{projects_favicon}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('project_id', 'required'),
            array('project_id', 'numerical', 'integerOnly' => true),
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

    public static function download( $project ) {
        if (is_numeric($project)) {
            $project = Project::model()->findByPk( $project );
        }

        if ($project and $project instanceof Project) {
        	$mainpage = $project->domain->mainpage;
        	$url = $project->domain->url();

            if ($mainpage and $page = $mainpage->text) {

                $matches = array();

                $regexp = array(
                    array(
                        'exp' => '/<link.*?rel=("|\').*?shortcut(.*?)("|\').*?href=("|\')(.*?)("|\').*?>/i',
                        'el' => 5,
                    ),
                    array(
                        'exp' => '/<link.*?href=("|\')(.*?)("|\').*?rel=("|\').*?shortcut(.*?)("|\').*?>/i',
                        'el' => 2,
                    ),
                    
                    array(
                        'exp' => '/<link.*?rel=("|\').*?icon(.*?)("|\').*?href=("|\')(.*?)("|\').*?>/i',
                        'el' => 5,
                    ),
                    array(
                        'exp' => '/<link.*?href=("|\')(.*?)("|\').*?rel=("|\').*?icon(.*?)("|\').*?>/i',
                        'el' => 2,
                    ),
                );

                foreach ($regexp as $e) {
                    preg_match_all($e['exp'], $page, $matches);

                    if (count($matches[0]) != 0) {
                        $href = $matches[ $e['el'] ][0];
                        break;
                    }
                }

                if (!$href) {
                    $href = $url . '/favicon.ico';
                }

                $a = parse_url($url);
                $b = parse_url($href);
                $c = pathinfo($b['path']);
                $ext = strtolower($c['extension']);

                if (!$b['scheme']) $b['scheme'] = $a['scheme'];
                if (!$b['host']) $b['host'] = $a['host'];
                if (substr($b['path'], 0, 1) != '/') $b['path'] = '/' . $b['path'];

                $href = ($b['scheme'] ? $b['scheme'] : 'http'). '://' . $b['host'] . $b['path'];

                if (in_array($ext, array('ico', 'png', 'jpg', 'jpeg', 'gif')) and $favicon = @file_get_contents($href)) {

                    $name = '/upload/favicon/' . $a['host'].date('-YmdHis') . '.' . $ext;
                    file_put_contents(Yii::app()->basePath . '/..' . $name, $favicon);

                    $obj = new self;
                    $obj->project_id = $project->id;
                    $obj->image = $name;
                    $obj->save();

                    return $obj;
                }
            }
        }

        return false;
    }

}
