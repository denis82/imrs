<?php

class CompetitorForm extends CFormModel {
    public $project;

    public $site;

    public function rules() {
        return array(
            array('site', 'safe'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'site' => 'Адрес сайта',
        );
    }

    public function save() {

		$this->site = SeoUtils::normalizeHost( $this->site );

        $host_level = count(explode('.', parse_url($this->site, PHP_URL_HOST)));
        if ($host_level > 3) {
            $this->addError('site', 'Введен домен более чем третьего уровня, система не может его принять по техническим причинам.');
            return false;
        }

        $domain = Domain::check(  $this->site );
        $domain_id = $domain->id;
	
        $code = SeoUtils::testUrl( $domain->url() );

        if ($code != 200) {
            $this->addError('site', 'Сайт недоступен. ' . ($code ? 'Сервер вернул ошибку ' . $code : '') );
            return false;
        }

        $c = new ProjectsCompetitor;
        $c->project_id = $this->project->id;
        $c->domain_id = $domain->id;
        $c->save();

        return true;
    }

}


