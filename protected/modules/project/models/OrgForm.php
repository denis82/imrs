<?php

/**
 * Модель формы информации об организации
 */

class OrgForm extends CFormModel {
    public $country;
    public $region;
    public $city;
    public $district;

    public $name;
    public $legal;
    public $english;

    public $address;

    public $phone_country;
    public $phone_code;
    public $phone_number;
    public $phone_extra;
    public $phone_name;

    public $site;
    public $social;
    public $email;

    public $worktime_days;
    public $worktime_time1;
    public $worktime_time2;

    public $project;

    public function rules() {
        return array(
            array('name', 'length', 'max' => 250),
            array('country, region, city, district, name, legal, english, address, site, social, email', 'safe'),
            array('phone_country, phone_code, phone_number, phone_extra, phone_name', 'safe'),
            array('worktime_days, worktime_time1, worktime_time2', 'safe'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'country' => 'Страна',
            'region' => 'Область',
            'city' => 'Город',
            'district' => 'Район',
            'name' => 'Название организации',
            'legal' => 'Юридическое название',
            'english' => 'Название на английском',
            'address' => 'Адрес',
            'email' => 'Эл. почта организации',

            'phone' => 'Телефоны',
            'site' => 'Сайт',
            'social' => 'Ссылки на страницы в соцсетях',
            'worktime' => 'Часы работы',
        );
    }

    public function save() {

    	$org = ProjectsOrg::model()->findByAttributes(array('project_id' => $this->project->id));

    	if (!$org or !$org->id) {
    		$org = new ProjectsOrg;
    		$org->project_id = $this->project->id;
    	}

    	$org->country 	= $this->country;
	    $org->region 	= $this->region;
	    $org->city 		= $this->city;
	    $org->district 	= $this->district;

	    $org->name 		= $this->name;
	    $org->legal 	= $this->legal;
	    $org->english 	= $this->english;

	    $org->address 	= $this->address;
	    $org->email 	= $this->email;

	    $org->phone 	= array(
	    	'country' => $this->phone_country,
	    	'code' => $this->phone_code,
	    	'number' => $this->phone_number,
	    	'extra' => $this->phone_extra,
	    	'name' => $this->phone_name
	    );
	    $org->site 		= $this->site;
	    $org->social 	= $this->social;
	    $org->worktime 	= array(
	    	'days' => $this->worktime_days,
	    	'time1' => $this->worktime_time1,
	    	'time2' => $this->worktime_time2,
	    );

        if ($org->save()) {
            return true;
        }

        $this->addError('name', 'Непредвиденная ошибка. Попробуйте ещё раз.');

        return false;
    }

}


