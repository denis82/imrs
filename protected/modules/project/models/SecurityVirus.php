<?php

use Yandex\SafeBrowsing\SafeBrowsingClient;

class SecurityVirus {
	const VAR_NAME = 'safebrowsing';

	private $_domain_id = 0;

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
		return new self( $project );
	}

	public function data() {
		$result = DomainsResult::model()->findByAttributes(
			array('domain_id' => $this->_domain_id, 'name' => self::VAR_NAME), 
			array('order' => '`date` desc')
		);

		if (!$result) {
			$result = $this->check();
		}

		return $result;
	}

	public function check() {
		$model = Domain::model()->findByPk( $this->_domain_id );

		if ($model) {
			$safeBrowsing = new SafeBrowsingClient("430bb131-af37-42fc-b150-c1e46cafeab7");
			$data = $safeBrowsing->searchUrl( $model->url() );

			$result = new DomainsResult;
			$result->domain_id = $model->id;
			$result->name = self::VAR_NAME;
			$result->value = (intval( $data ) ? 'no' : 'yes');
			$result->save();
		}

		return $result ? $result : false;
	}


}