<?php

/**
 * This is the model class for table "{{yandex}}".
 *
 * The followings are the available columns in table '{{yandex}}':
 * @property integer $id
 * @property integer $domain_id
 * @property string $checkdate
 * @property integer $tic
 * @property integer $pr
 * @property integer $yac
 * @property integer $index
 */
class Yandex extends CActiveRecord {

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Yandex the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{yandex}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain_id, checkdate, tic, yac, index, pr', 'required'),
			array('domain_id, tic, yac, index, pr', 'numerical', 'integerOnly' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, checkdate, tic, yac, index, pr', 'safe', 'on' => 'search'),
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
			'domain_id' => 'Domain',
			'checkdate' => 'Checkdate',
			'tic' => 'Tic',
			'yac' => 'Yac',
			'index' => 'Index',
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
		$criteria->compare('domain_id', $this->domain_id);
		$criteria->compare('checkdate', $this->checkdate, true);
		$criteria->compare('tic', $this->tic);
		$criteria->compare('yac', $this->yac);
		$criteria->compare('index', $this->index);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public function getDomain() {
		$domain = Domain::model()->findByPk($this->domain_id);
		return $domain;
	}

	public static function exist($domain_id, $date) {
		$criteria = new CDbCriteria();
		$criteria->condition = "domain_id=" . $domain_id;
		$criteria->order = "checkdate desc";
		return Yandex::model()->find($criteria);
	}

	public static function check($domain_id, $date = null, $force = false) {
		$date = $date ? $date : date("Y-m-d");

		if ($force == false && $model = self::exist($domain_id, $date))
			return $model;

		$model = new Yandex();
		$model->domain_id = $domain_id;
		//Поиск ТиЦ яндекса
		$url = CUtils::normalizeDomain($model->domain->domain);

		$httpUrl = (strpos($url, 'http') === 0)? $url : sprintf('http://%s/', trim($url,'/'));

		//$ci_url = "http://bar-navig.yandex.ru/u?ver=2&show=32&url=$url";
		$ci_url = sprintf('http://bar-navig.yandex.ru/u?ver=2&show=32&url=%s', urlencode($httpUrl));

		$ci_data = implode('', file($ci_url));

		if(is_int(strpos($ci_data, 'windows-1251'))){
			$ci_data = iconv('windows-1251', 'utf-8', $ci_data);
		}
		
		$model->tic = 0;
		
		if(preg_match("/value=\"(.\d*)\"/", $ci_data, $ci)){
			if ($ci[1])
			$model->tic = $ci[1];
		}

		//Поиск в каталоге яндекса
		$model->yac = 0;

		if(preg_match('/<topic\s+title="([^"]*)"\s+url="([^"]*)"\/>/i', $ci_data)){
			$model->yac = 1;
		}


		if(!$model->yac){

			$yandexBarAnswer = file_get_contents('https://bar-navig.yandex.ru/u', false, stream_context_create([
				'http' => [
					'header' => sprintf("%s\r\n", join("\r\n", [
						'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:36.0) Gecko/20100101 Firefox/36.0',
						'Pragma: no-cache',
						'Host: bar-navig.yandex.ru',
						'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
						'Cache-Control: no-cache',
						'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
						'Accept-Encoding: gzip, deflate',
						'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
					])),
					'method' => 'POST',
					'content' => http_build_query(
						array(
							'ver' => '8.9.1',
							//'clid' => '1917055',
							//'ui' => '%7Ba296a989-e833-294e-b9c7-d11c8790f542%7D',
							'brandID' => 'yandex',
							'yasoft' => 'barff',
							'show' => '1',
							'post' => '0',
							//'r1' => 'orlprswcqeoahenkllhosjcalwkbhlreelvpfyrhlkoloaiaixexewdxwedpcqjccetldenawhenmfbqwsbdfswoymobncstcqqufa1525c526ba886501c5bfb54279ea36',
							//'title' => 'Цивилизации мира – история развития древних цивилизаций мира',
							'url' => $url,
							'httpstatus' => '200',
							'tic' => '1'
						)
					),
					'follow_location' => 1,
					'timeout' => 10,
					//'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:36.0) Gecko/20100101 Firefox/36.0'
				]
			]));

			if(isset($http_response_header)){
				$responseHeaders = join("\n", $http_response_header);
				
				if(preg_match('/HTTP\/[^\s]+\s+([0-9]+)\s/i', $responseHeaders, $headersMatch)){
					$responseStatus = (int)$headersMatch[1];

					if($responseStatus >= 200 && $responseStatus < 300){

						$yandexBarAnswer = iconv('windows-1251', 'utf-8', $yandexBarAnswer);

						if(preg_match('/<topic\s+title="([^"]*)"\s+url="([^"]*)"\/>/i', $yandexBarAnswer, $topicMatch)){

							$model->yac = 1;

						}

						if(preg_match('/<tcy[^>]+value="([0-9]+)"[^>]*>/i', $yandexBarAnswer, $tcyMatch)){

							$model->tic = (int)$tcyMatch[1];

						}

					}
				}

			}

		}

		if(!$model->yac){
			$source = CUtils::getContent("https://yaca.yandex.ru/yca?text=http%3A%2F%2F" . $model->domain->domain . "&yaca=1");
			$source = mb_convert_encoding($source, 'HTML-ENTITIES', "utf-8");
			$source = str_replace("\r", "", $source);
			$source = str_replace("\n", "", $source);

			if ($source && preg_match('/<div class=\"z\-counter\">.*?(\d{1,5})<\/div>/', $source, $out)){
				$count = (int)$out[1];

				if($count > 0)
					$model->yac = 1;
			}

		}

		if(!$model->yac){

			if($source = CUtils::getContent(sprintf('https://yaca.yandex.ru/yca/cy/ch/%s/', urlencode($model->domain->domain)))){
				if(strpos($source, 'img.yandex.net/i/arr-hilite.gif') !== false){
					$model->yac = 1;
				}
			}

		}

		//Количество проиндексированых страниц
		$content = CUtils::getContent("http://webmaster.yandex.ru/check.xml?hostname=" . $model->domain->domain);
		if (strpos($content, "Сайт является зеркалом")) {
			$content = CUtils::getContent("http://webmaster.yandex.ru/check.xml?hostname=" . CUtils::getMirrorDomain($model->domain->domain));
		}
		if(preg_match('/<div class="header g-line">.*?<\/div>/', $content, $match)){
			$model->index = (int)trim(str_replace('Страницы:', '', strip_tags($match[0])));
		}else{
			$model->index = 0;
		}
		if (strpos($content, "Сайт является зеркалом")) {
			$model->index = -1;
		}

		$model->pr = GooglePageRankChecker::getRank($model->domain->domain);

		$model->checkdate = date("Y-m-d");

		if (!$model->save()) {
			CUtils::traceValidationErrors($model);
		}
		return $model;
	}

}