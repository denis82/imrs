<?php

class SecurityDirectory extends DataDomain {
	const VAR_NAME = 'directory_index';

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

	private function testUrl( $url ) {

		$text = @file_get_contents($url);

		if ($text) {

            if (preg_match("/<title>(.*)<\/title>/siU", $text, $title_matches)) {
                $title = preg_replace('/\s+/', ' ', $title_matches[0]);

                if (preg_match('/index of/si', $title)) {
                	return true;
                }
            }

		}

		return false;
	}

	public function check() {
		$model = Domain::model()->findByPk( $this->_domain_id );

		if ($model) {

			$criteria = new CDbCriteria;
			$criteria->condition = 'domain_id = :id and type = :type and url like :url';
			$criteria->order = 'rand()';
			$criteria->params = array(
				'id' => $model->id,
				'type' => DomainsResource::T_SCRIPT,
				'url' => $model->url() . '%',
			);

			$r = DomainsResource::model()->find($criteria);
			$url = substr($r->url, 0, strrpos($r->url, '/')) . '/';

			$test = $this->testUrl( $url );

			if (!$test) {
				$criteria->params['type'] = DomainsResource::T_CSS;

				$r = DomainsResource::model()->find($criteria);
				$url = substr($r->url, 0, strrpos($r->url, '/')) . '/';

				$test = $this->testUrl( $url );
			}

			$result = new DomainsResult;
			$result->domain_id = $model->id;
			$result->name = self::VAR_NAME;
			$result->value = ($test ? 'yes' : 'no');
			$result->save();
		}

		return $result ? $result : false;
	}


}