<?php

class DataDomain {
	protected $_domain_id = 0;

	public function __construct( $project ) {
		if (is_numeric($project)) {
			if ($model = Project::model()->findByPk( $project )) {
				$this->_domain_id = $model->domain_id;
			}
		}
		elseif ($project instanceof Project) {
			$this->_domain_id = $project->domain_id;
		}
		elseif ($project instanceof Domain) {
			$this->_domain_id = $project->id;
		}
	}

	public static function model( $project ) {
		return new static( $project );
	}

}