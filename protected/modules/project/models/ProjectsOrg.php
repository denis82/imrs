<?php

class ProjectsOrg extends CActiveRecord {

	public $phone;
	public $site;
	public $social;
	public $worktime;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{projects_org}}';
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
            array('id, project_id, country, region, city, district, name, legal, english, address, email', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'org_phone' => array(self::HAS_MANY, 'ProjectsOrgPhone', 'org_id'),
            'org_site' => array(self::HAS_MANY, 'ProjectsOrgSite', 'org_id', 'condition' => 'social=0'),
            'org_social' => array(self::HAS_MANY, 'ProjectsOrgSite', 'org_id', 'condition' => 'social=1'),
            'org_worktime' => array(self::HAS_MANY, 'ProjectsOrgWorktime', 'org_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
			'id' => 'ID',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return YSearch the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

	protected function afterSave() {
        parent::afterSave();

        /* Сохранение телефонов */

        $phones = array();

        if (is_array($this->phone['number'])) {
	        foreach ($this->phone['number'] as $j => $i) {
	        	if (strlen($i)) {
		        	$phones[] = array(
		        		'country' => $this->phone['country'][$j],
		        		'code' => $this->phone['code'][$j],
		        		'number' => $this->phone['number'][$j],
		        		'extra' => $this->phone['extra'][$j],
		        		'name' => $this->phone['name'][$j],
		        	);
	        	}
	        }
        }

        $org_phones = ProjectsOrgPhone::model()->findAllByAttributes(array('org_id' => $this->id));

        foreach ($phones as $j => $i) {
        	if (!isset($org_phones[$j])) {
        		$org_phones[$j] = new ProjectsOrgPhone;
        		$org_phones[$j]->org_id = $this->id;
        	}

        	$org_phones[$j]->country = $i['country'];
        	$org_phones[$j]->code = $i['code'];
        	$org_phones[$j]->number = $i['number'];
        	$org_phones[$j]->extra = $i['extra'];
        	$org_phones[$j]->name = $i['name'];

        	$org_phones[$j]->save();
        }

        if (count($phones) < count($org_phones)) {
        	for ($j = count($phones); $j < count($org_phones); $j++) {
        		$org_phones[$j]->delete();
        	}
        }


        /* Сохранение сайтов и соцсетей */

        $org_sites = ProjectsOrgSite::model()->findAllByAttributes(array('org_id' => $this->id));

        $n = 0;

        if (is_array($this->site)) {
	        foreach ($this->site as $j => $i) {
	        	if (strlen($i)) {
		        	if (!isset($org_sites[$j])) {
		        		$org_sites[$j] = new ProjectsOrgSite;
	        			$org_sites[$j]->org_id = $this->id;
		        	}

		        	$org_sites[$j]->url = $i;
		        	$org_sites[$j]->social = 0;

		        	$org_sites[$j]->save();
	        	}
	        	elseif (isset($org_sites[$j])) {
	        		$org_sites[$j]->delete();
	        	}
	        }

	        $n = count($this->site);
        }

        if (is_array($this->social)) {
	        foreach ($this->social as $k => $i) {
	        	$j = $n + $k;

	        	if (strlen($i)) {
		        	if (!isset($org_sites[$j])) {
		        		$org_sites[$j] = new ProjectsOrgSite;
		        		$org_sites[$j]->org_id = $this->id;
		        	}

		        	$org_sites[$j]->url = $i;
		        	$org_sites[$j]->social = 1;

		        	$org_sites[$j]->save();
	        	}
	        	elseif (isset($org_sites[$j])) {
	        		$org_sites[$j]->delete();
	        	}
	        }

	        $n += count($this->social);
        }

        if ($n < count($org_sites)) {
        	for ($j = $n; $j < count($org_sites); $j++) {
        		$org_sites[$j]->delete();
        	}
        }

        /* Сохранение режима работы */

        $org_worktime = ProjectsOrgWorktime::model()->findAllByAttributes(array('org_id' => $this->id));

        $worktimes = array();

        if (is_array($this->worktime['days'])) {
	        foreach ($this->worktime['days'] as $j => $i) {
	        	if (strlen($i)) {
		        	$worktimes[] = array(
		        		'days' => $this->worktime['days'][$j],
		        		'time1' => $this->worktime['time1'][$j],
		        		'time2' => $this->worktime['time2'][$j],
		        	);
	        	}
	        }
        }

        foreach ($worktimes as $j => $i) {
        	if (!isset($org_worktime[$j])) {
        		$org_worktime[$j] = new ProjectsOrgWorktime;
        		$org_worktime[$j]->org_id = $this->id;
        	}

        	$org_worktime[$j]->days = $i['days'];
        	$org_worktime[$j]->time1 = $i['time1'];
        	$org_worktime[$j]->time2 = $i['time2'];

        	$org_worktime[$j]->save();
        }

        if (count($worktimes) < count($org_worktime)) {
        	for ($j = count($worktimes); $j < count($org_worktime); $j++) {
        		$org_worktime[$j]->delete();
        	}
        }

    } 

}
