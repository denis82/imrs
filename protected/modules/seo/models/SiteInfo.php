<?php

/**
 * This is the model class for table "{{siteinfo}}".
 *
 * The followings are the available columns in table '{{siteinfo}}':
 * @property integer $id
 * @property string $checkdate
 * @property string $created
 * @property string $paid
 * @property string $registrar
 * @property string $nsservers
 * @property string $ip
 * @property string $hoster
 * @property double $time
 * @property integer $error404
 * @property integer $robots
 * @property string $robots_txt
 * @property string $sitemap
 * @property string $cms
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property integer $h1h6
 * @property integer $alts
 * @property integer $tic
 * @property integer $pr
 * @property integer $yac
 * @property integer $yam
 * @property integer $yaw
 * @property integer $ga
 * @property integer $gw
 * @property integer $limp
 * @property integer $limv
 * @property integer $lidp
 * @property integer $lidv
 * @property integer $index_count
 * @property string $index_date
 * @property integer $mr_sites
 * @property integer $ip_sites
 * @property integer $hin
 * @property integer $hin_l1
 * @property integer $hin_l2
 * @property integer $hin_l3
 * @property integer $hin_l4
 * @property integer $din
 * @property integer $din_l1
 * @property integer $din_l2
 * @property integer $din_l3
 * @property integer $din_l4
 * @property integer $hout
 * @property integer $hout_l1
 * @property integer $hout_l2
 * @property integer $hout_l3
 * @property integer $hout_l4
 * @property integer $dout
 * @property integer $anchors
 * @property integer $anchors_out
 * @property string $igood
 * @property double $starttransfer_time
 * @property integer $top50
 * @property integer $citation
 * @property integer $trust
 * @property integer $domain_id
 * @property integer $last_modified
 * @property integer $optimized
 * @property string $codepage
 */
class SiteInfo extends CActiveRecord
{
	const REGEXP_IP4 = '~(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})~is';
	const REGEXP_IP4_CHECK = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';
	
	public $bindHosting = array(
		'1&1 Internet AG' => 'www.1und1.de',
		'RU-HOSTING' => 'nic.ru',
		'GLOBALLAYER' => 'global-layer.com'
	);
	
	protected $header = "";
	protected $body = "";
	protected $body_codepage = 'utf-8';
	protected $domain;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{siteinfo}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('checkdate, domain_id', 'required'),
			array('error404, robots, h1h6, alts, tic, pr, yac, yam, yaw, ga, gw, limp, limv, lidp, lidv, index_count, mr_sites, ip_sites, hin, hin_l1, hin_l2, hin_l3, hin_l4, din, din_l1, din_l2, din_l3, din_l4, hout, hout_l1, hout_l2, hout_l3, hout_l4, dout, anchors, anchors_out, top50, citation, trust, domain_id', 'numerical', 'integerOnly' => true),
			array('time, starttransfer_time', 'numerical'),
			array('registrar, ip, hoster, sitemap, cms, igood', 'length', 'max' => 250),
			array('created, paid, nsservers, robots_txt, title, description, keywords, index_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, checkdate, created, paid, registrar, nsservers, ip, hoster, time, error404, robots, robots_txt, sitemap, cms, title, description, keywords, h1h6, alts, tic, pr, yac, yam, yaw, ga, gw, limp, limv, lidp, lidv, index_count, index_date, mr_sites, ip_sites, hin, hin_l1, hin_l2, hin_l3, hin_l4, din, din_l1, din_l2, din_l3, din_l4, hout, hout_l1, hout_l2, hout_l3, hout_l4, dout, anchors, anchors_out, igood, starttransfer_time, top50, citation, trust, domain_id, last_modified, optimized', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'checkdate' => 'Checkdate',
			'created' => 'Created',
			'paid' => 'Paid',
			'registrar' => 'Registrar',
			'nsservers' => 'Nsservers',
			'ip' => 'Ip',
			'hoster' => 'Hoster',
			'time' => 'Time',
			'error404' => 'Error404',
			'robots' => 'Robots',
			'robots_txt' => 'Robots Txt',
			'sitemap' => 'Sitemap',
			'cms' => 'Cms',
			'title' => 'Title',
			'description' => 'Description',
			'keywords' => 'Keywords',
			'h1h6' => 'H1h6',
			'alts' => 'Alts',
			'tic' => 'Tic',
			'pr' => 'Pr',
			'yac' => 'Yac',
			'yam' => 'Yam',
			'yaw' => 'Yaw',
			'ga' => 'Ga',
			'gw' => 'Gw',
			'limp' => 'Limp',
			'limv' => 'Limv',
			'lidp' => 'Lidp',
			'lidv' => 'Lidv',
			'index_count' => 'Index Count',
			'index_date' => 'Index Date',
			'mr_sites' => 'Mr Sites',
			'ip_sites' => 'Ip Sites',
			'hin' => 'Hin',
			'hin_l1' => 'Hin L1',
			'hin_l2' => 'Hin L2',
			'hin_l3' => 'Hin L3',
			'hin_l4' => 'Hin L4',
			'din' => 'Din',
			'din_l1' => 'Din L1',
			'din_l2' => 'Din L2',
			'din_l3' => 'Din L3',
			'din_l4' => 'Din L4',
			'hout' => 'Hout',
			'hout_l1' => 'Hout L1',
			'hout_l2' => 'Hout L2',
			'hout_l3' => 'Hout L3',
			'hout_l4' => 'Hout L4',
			'dout' => 'Dout',
			'anchors' => 'Anchors',
			'anchors_out' => 'Anchors Out',
			'igood' => 'Igood',
			'starttransfer_time' => 'Starttransfer Time',
			'top50' => 'Top50',
			'citation' => 'Citation',
			'trust' => 'Trust',
			'domain_id' => 'Domain',
			'last_modified' => 'Last modified',
			'optimized' => 'Сайт уже продвигался %',
			'codepage' => 'Кодировка сайта'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('checkdate', $this->checkdate, true);
		$criteria->compare('created', $this->created, true);
		$criteria->compare('paid', $this->paid, true);
		$criteria->compare('registrar', $this->registrar, true);
		$criteria->compare('nsservers', $this->nsservers, true);
		$criteria->compare('ip', $this->ip, true);
		$criteria->compare('hoster', $this->hoster, true);
		$criteria->compare('time', $this->time);
		$criteria->compare('error404', $this->error404);
		$criteria->compare('robots', $this->robots);
		$criteria->compare('robots_txt', $this->robots_txt, true);
		$criteria->compare('sitemap', $this->sitemap, true);
		$criteria->compare('cms', $this->cms, true);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('keywords', $this->keywords, true);
		$criteria->compare('h1h6', $this->h1h6);
		$criteria->compare('alts', $this->alts);
		$criteria->compare('tic', $this->tic);
		$criteria->compare('pr', $this->pr);
		$criteria->compare('yac', $this->yac);
		$criteria->compare('yam', $this->yam);
		$criteria->compare('yaw', $this->yaw);
		$criteria->compare('ga', $this->ga);
		$criteria->compare('gw', $this->gw);
		$criteria->compare('limp', $this->limp);
		$criteria->compare('limv', $this->limv);
		$criteria->compare('lidp', $this->lidp);
		$criteria->compare('lidv', $this->lidv);
		$criteria->compare('index_count', $this->index_count);
		$criteria->compare('index_date', $this->index_date, true);
		$criteria->compare('mr_sites', $this->mr_sites);
		$criteria->compare('ip_sites', $this->ip_sites);
		$criteria->compare('hin', $this->hin);
		$criteria->compare('hin_l1', $this->hin_l1);
		$criteria->compare('hin_l2', $this->hin_l2);
		$criteria->compare('hin_l3', $this->hin_l3);
		$criteria->compare('hin_l4', $this->hin_l4);
		$criteria->compare('din', $this->din);
		$criteria->compare('din_l1', $this->din_l1);
		$criteria->compare('din_l2', $this->din_l2);
		$criteria->compare('din_l3', $this->din_l3);
		$criteria->compare('din_l4', $this->din_l4);
		$criteria->compare('hout', $this->hout);
		$criteria->compare('hout_l1', $this->hout_l1);
		$criteria->compare('hout_l2', $this->hout_l2);
		$criteria->compare('hout_l3', $this->hout_l3);
		$criteria->compare('hout_l4', $this->hout_l4);
		$criteria->compare('dout', $this->dout);
		$criteria->compare('anchors', $this->anchors);
		$criteria->compare('anchors_out', $this->anchors_out);
		$criteria->compare('igood', $this->igood, true);
		$criteria->compare('starttransfer_time', $this->starttransfer_time);
		$criteria->compare('top50', $this->top50);
		$criteria->compare('citation', $this->citation);
		$criteria->compare('trust', $this->trust);
		$criteria->compare('domain_id', $this->domain_id);
		$criteria->compare('last_modified', $this->last_modified);
		$criteria->compare('optimized', $this->optimized);
		$criteria->compare('codepage', $this->codepage);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SiteInfo the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}


	public function getHost()
	{
		$domain = Domain::model()->findByPk($this->domain_id);
		//return $domain->ru_domain;
		return $domain->domain;
	}

	public function getSource($codepage = 'utf-8')
	{
		if($codepage) $codepage = strtolower($codepage);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->host . '/' . ltrim( $this->domain->page, '/'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.0 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		$source = curl_exec($curl);
		$info = curl_getinfo($curl);
		$this->starttransfer_time = $info['starttransfer_time'];
		curl_close($curl);
		
		//if ($codepage) $source = mb_convert_encoding($source, 'HTML-ENTITIES', $codepage);
		if ($codepage && $codepage != '' && $codepage != 'utf-8') $source = mb_convert_encoding($source, 'utf-8', $codepage);
		//if ($codepage && $codepage != 'utf-8' ) $source = iconv($codepage, 'utf-8', $source); 
		
		$source = str_replace("\r", "", $source);
		$source = str_replace("\n", "", $source);
		return $source;
	}

	public function checkBase()
	{
		//Получить заголовок сайта
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_URL, $this->host . '/' . ltrim( $this->domain->page, '/'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_NOBODY, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		$header = curl_exec($curl);
		foreach (explode("\r\n", $header) as $row) {
			$rp = explode(":", $row);
			if (count($rp) == 2) {
				$this->header[$rp[0]] = $rp[1];
			}
		}
		curl_close($curl);
		
		$codepage = '';
		
		if(isset($this->header['Content-Type'])){
		
			$cp = explode('=', $this->header['Content-Type']);
			
			if(isset($cp[1]))
				$codepage = $cp[1];
				
		}
		
		$this->codepage = strtolower($codepage);
		
		//Получить содержание
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$tstart = $mtime[1] + $mtime[0];
		$this->body = $this->getSource($codepage);
		
		//определение кодировки из контента страницы charset=windows-1251"
		if(!$this->codepage || $this->codepage == ''){
			if(preg_match('/charset=\s*([^\"\'\s\/]+)/', $this->body, $body_code_match)){
				$this->codepage = $body_code_match[1];
				$this->body = iconv($this->codepage, 'utf-8', $this->body);
			}
		}
		
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->time = ($mtime - $tstart);
		
		$this->ip = gethostbyname($this->host);
		
		if(!preg_match(self::REGEXP_IP4_CHECK, $this->ip)){
			$real_ip = $this->getRealIP($this->ip);
			if($real_ip) $this->ip = $real_ip;
		}
		
		$this->yam = is_int(strpos($this->body, "Ya.Metrika")) ? 1 : 0;
		//$this->ga = is_int(strpos($this->body, "var _gaq = _gaq || [];")) ? 1 : 0;
		
		$this->ga = preg_match('~google-analytics.com~is', $this->body)? 1 : 0;
		
		//is_int(strpos($this->body, "yandex-verification")) ? 1 : 0;
		
		$this->yaw = preg_match('/<meta([^>])yandex\-verification/i',$this->body)? 1 : 0;
		
		$this->gw = preg_match('/<meta([^>])google\-site\-verification/i',$this->body)? 1 : 0;
		
		$this->last_modified = (isset($this->header['Last-Modified']))? strtotime($this->header['Last-Modified']) : 0;

	}
	
	public function getRealIP($domain){
		$domain = trim($domain);
		$domain = str_replace('http://', '', $domain);
		$domain = substr($domain, -1)=='/'?substr($domain, 0, -1):$domain;

		if (!$domain)
			return false;
		
		$domain = preg_replace('/[^a-z0-9\-\.]+/i', '', $domain);
		
		$ping_result = shell_exec('ping -c 1 '.$domain);

		if (!preg_match('~(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})~is', $ping_result, $match) || !SeoUtils::isValidIP($match[1]))
			return false;

		return $match[1];
	}

	public function checkRobots()
	{

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_URL, $this->host . '/robots.txt');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$source = curl_exec($curl);
		$header = curl_getinfo($curl);
		if ($header["http_code"] == 404 || !(strpos($header['content_type'], "text/plain") === 0)) {
			$this->robots = 0;
		} else {
			$this->robots = 1;
			$this->robots_txt = $source;
		}
		curl_close($curl);
	}

	public function checkError404()
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_URL, $this->host . "/" . uniqid() . "/" . uniqid() . "/");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		$source = curl_exec($curl);
		$header = curl_getinfo($curl);
		if ($header["http_code"] == 404) {
			$this->error404 = 1;
		} else {
			$this->error404 = 0;
		}
		curl_close($curl);
	}

	public function checkSiteMap()
	{

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_URL, $this->host . "/sitemap.xml");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$source = curl_exec($curl);
		$header = curl_getinfo($curl);
		if ($header["http_code"] == 404 || !((strpos($header['content_type'], "application/xml") === 0 || strpos($header['content_type'], "text/xml") === 0))) {
			$this->sitemap = "";
		} else {
			$this->sitemap = $this->host . "/sitemap.xml";
		}
		curl_close($curl);
		if ($this->sitemap == "") {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_URL, $this->host . "/sitemap_index.xml");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$source = curl_exec($curl);
			$header = curl_getinfo($curl);
			if ($header["http_code"] == 404 || !((strpos($header['content_type'], "application/xml") === 0 || strpos($header['content_type'], "text/xml") === 0))) {
				$this->sitemap = "";
			} else {
				$this->sitemap = $this->host . "/sitemap_index.xml";
			}
			curl_close($curl);
		}
	}

	public function checkCMS()
	{
		$this->cms = 'Не определено';
		//Drupal http://drupal.org/
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_URL, $this->host . '/' . ltrim( $this->domain->page, '/'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		$source = curl_exec($curl);
		curl_close($curl);
		if (is_int(strpos($source, "Expires: Sun, 19 Nov 1978 05:00:00 GMT"))) {
			$this->cms = '<a href="http://drupal.org/">Drupal</a>';
			return;
		}
		//DataLife Engine http://dle-news.ru/
		if(is_int(strpos($source, 'var dle_spam_agree ='))){
			$this->cms = '<a href="http://dle-news.ru/">DataLife Engine</a>';
			return;
		}
		//WordPress http://wordpress.org/
		if (is_int(stripos($source, "wp-content"))) {
			$this->cms = '<a href="http://wordpress.org/">WordPress</a>';
			return;
		}
		
		if(is_int(stripos($source, 'Shop powered by PrestaShop'))){
			$this->cms = '<a href="https://www.prestashop.com/">PrestaShop</a>';
			return;
		}

		if(is_int(stripos($source, 'content="Joomla!')) || is_int(stripos($source, 'Joomla! - Open Source Content Management'))){
			$this->cms = '<a href="http://www.joomla.org/">Joomla</a>';
			return;
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->host . '/admin/');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		$source = curl_exec($curl);
		curl_close($curl);

		if(strpos($source, 'hostcms6') !== false){
			$this->cms = '<a href="http://www.hostcms.ru/">HostCMS v.6</a>';
			return;
		}

		if(strpos($source, 'HostCMS v. 5') !== false){
			$this->cms = '<a href="http://www.hostcms.ru/">HostCMS v.5</a>';
			return;
		}

		if(strpos($source, 'hostcms.ru') !== false){
			$this->cms = '<a href="http://www.hostcms.ru/">HostCMS</a>';
			return;
		}

		//1C:Битрикс http://www.1c-bitrix.ru/
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->host . "/bitrix/tools/help.css");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		$source = curl_exec($curl);
		curl_close($curl);
		if (is_int(strpos($source, "/*Help*/"))) {
			$this->cms = '<a href="http://www.1c-bitrix.ru/">1C:Битрикс</a>';
			return;
		}
		
		//Joomla http://www.joomla.org/
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->host . '/configuration.php-dist');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		$source = curl_exec($curl);
		curl_close($curl);
		if (is_int(strpos($source, 'Joomla'))) {
			$this->cms = '<a href="http://www.joomla.org/">Joomla</a>';
			return;
		}
		
		//MODx http://modx.com/
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->host . '/manager');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
		$source = curl_exec($curl);
		curl_close($curl);
		if (is_int(strpos($source, 'MODx.load'))) {
			$this->cms = '<a href="http://modx.com/">MODx</a>';
			return;
		}
		/*
		  MODx	 признаков использования не найдено
		  Textpattern	 признаков использования не найдено
		  OSCommerce	 признаков использования не найдено
		  e107	 признаков использования не найдено
		  Danneo	 признаков использования не найдено
		  NetCat	 признаков использования не найдено
		  TYPO3	 признаков использования не найдено
		  Plone	 признаков использования не найдено
		  CMS Made Simple	 признаков использования не найдено
		  Movable Type	 признаков использования не найдено
		  InstantCMS	 признаков использования не найдено
		  MaxSite CMS	 признаков использования не найдено
		  UMI.CMS	 признаков использования не найдено
		  Amiro CMS	 признаков использования не найдено
		  Magento	 признаков использования не найдено
		  S.Builder	 признаков использования не найдено
		  ABO.CMS	 признаков использования не найдено
		  Twilight CMS	 признаков использования не найдено
		  PHP-Fusion	 признаков использования не найдено
		  Melbis	 признаков использования не найдено
		  Miva Merchant	 признаков использования не найдено
		  phpwcms	 признаков использования не найдено
		  N2 CMS	 признаков использования не найдено
		  Explay CMS	 признаков использования не найдено
		  ExpressionEngine	 признаков использования не найдено
		  Klarnet CMS	 признаков использования не найдено
		  SEQUNDA	 признаков использования не найдено
		  SiteDNK	 признаков использования не найдено
		  CM5	 признаков использования не найдено
		  Site Sapiens	 признаков использования не найдено
		  Cetera CMS	 признаков использования не найдено
		  Hitmaster	 признаков использования не найдено
		  DSite	 признаков использования не найдено
		  SiteEdit	 признаков использования не найдено
		  TrinetCMS	 признаков использования не найдено
		  Adlabs.CMS	 признаков использования не найдено
		  Introweb-CMS	 признаков использования не найдено
		  iNTERNET.cms	 признаков использования не найдено
		  Kentico CMS	 признаков использования не найдено
		  LiveStreet	 признаков использования не найдено
		  vBulletin	 признаков использования не найдено
		  phpBB	 признаков использования не найдено
		  Invision Power Board	 признаков использования не найдено
		  Cmsimple	 признаков использования не найдено
		  OpenCMS	 признаков использования не найдено
		  slaed	 признаков использования не найдено
		  PHP-Nuke	 признаков использования не найдено
		  RUNCMS	 признаков использования не найдено
		  eZ publish	 признаков использования не найдено
		  Koobi	 признаков использования не найдено
		  Simple Machines Forum (SMF)	 признаков использования не найдено
		  MediaWiki	 признаков использования не найдено */
	}

	public function checkMeta()
	{

		$source = $this->body;

		$dom = new DOMDocument();
		$dom->strictErrorChecking = false;
		
		if($this->codepage && $this->codepage != '') $dom->encoding = $this->codepage; // insert proper
		
		@$dom->loadHTML('<?xml encoding="'.$this->codepage.'">' . $source);
		
		$xpath = new DOMXPath($dom);
		/*
		foreach ($xpath->query("//title") as $title) {
			$this->title = $title->nodeValue;
		}
		*/
		if(preg_match('/<title>(.*)<\/title>/i',$source,$matches)){
			$exploded = explode('</title>',$matches[1]);
			$this->title = preg_replace( '/(<|>)/', '', $exploded[0] );
		}else{
			$this->title = '';
		}
		
		//<meta name='yandex-verification' content='515819e285d77adc' />
		/*
		foreach ($xpath->query('//meta') as $title) {
			$length = $title->attributes->length;
			for ($i = 0; $i < $length; ++$i) {
				$name = $title->attributes->item($i)->name;
				if ($name == "name" && $title->attributes->item($i)->nodeValue == "description") {
					$d = true;
				}
				if ($name == "name" && $title->attributes->item($i)->nodeValue == "keywords") {
					$k = true;
				}
				if ($name == "name" && $title->attributes->item($i)->nodeValue == "yandex-verification") {
					$this->yaw = 1;
				}
				if ($name == "name" && $title->attributes->item($i)->nodeValue == "google-site-verification") {
					$this->gw = 1;
				}
				if ($name == "content") {
					if ($d) {
						$this->description = $title->attributes->item($i)->nodeValue;
						$d = false;
					}
					if ($k) {
						$this->keywords = $title->attributes->item($i)->nodeValue;
						$k = false;
					}
				}
			}
		}
		*/
		
		$this->description = '';
		$this->keywords = '';
		
		//ver 2.0
		preg_match_all('/<meta([^>]+)>/i',$source,$matches);
		$meta_array = array();
		foreach($matches[1] as $stringMeta){
			if(preg_match('/name="([^"]+)"/i', $stringMeta, $nameMatch) or preg_match('/name=\'([^\']+)\'/i', $stringMeta, $nameMatch)){
				$name = strtolower($nameMatch[1]);
				$nameMatch = NULL;
				if(preg_match('/content="([^"]+)"/i', $stringMeta, $contentMatch) or preg_match('/content=\'([^\']+)\'/i', $stringMeta, $contentMatch)){
					$meta_array[$name] = $contentMatch[1];
				}
			}
			$name = NULL; // clear memmory
			$stringMeta = NULL; // clear memmory
		}
		
		if( array_key_exists('keywords', $meta_array) ) $this->keywords = $meta_array['keywords'];
		if( array_key_exists('description', $meta_array) ) $this->description = $meta_array['description'];
		if( array_key_exists('yandex-verification', $meta_array) ) $this->yaw = 1;
		if( array_key_exists('google-site-verification', $meta_array) ) $this->gw = 1;
		
		$this->h1h6 = ($xpath->query('//h1')->length > 0 || $xpath->query('//h2')->length > 0 || $xpath->query('//h3')->length > 0 || $xpath->query('//h4')->length > 0 || $xpath->query('//h5')->length > 0 || $xpath->query('//h6')->length > 0) ? 1 : 0;

		
		foreach ($xpath->query('//img') as $img) {
			$length = $img->attributes->length;
			$title = $alt = 0;
			for ($i = 0; $i < $length; ++$i) {
				$name = $img->attributes->item($i)->name;
				if ($name == "alt") {
					$alt = 1;
				}
				if ($name == "title") {
					$title = 1;
				}
				$name = NULL;
			}
			$this->alts = ($alt || $title) ? 1 : 0;
			$img = NULL;
		}
		
	}

	public function checkHosting()
	{
		$this->hoster = $this->getHosting($this->ip);
		
		/*$this->hoster = '';
		$info = @file_get_contents('http://ipinfodb.com/ip_locator.php?ip=' . $this->ip);
		if($info){
			$info = str_replace("\r", "", $info);
			$info = str_replace("\n", "", $info);
			$info = str_replace("\t", "", $info);
			
			if(preg_match_all('/<li>Hostname : (.*?)<\/li>/', $info, $matches)){
				if(isset($matches[1][0])) $this->hoster = $matches[1][0];
			}
		}*/
	}
	
	public function getHosting($domain){
		$domain = trim($domain);

		if (!$domain) return false;

		$componentsPath = Yii::getpathOfAlias('application.modules.seo.components');

		include_once($componentsPath . '/Whois/Info.php');
		include_once($componentsPath . '/Whois/Info/Element.php');
		include_once($componentsPath . '/Whois/Info/Element/Date.php');
		include_once($componentsPath . '/Whois/Info/Element/NServer.php');
		include_once($componentsPath . '/Whois/Info/Element/State.php');
		include_once($componentsPath . '/Whois/Service.php');
		include_once($componentsPath . '/Whois/Services/RIPN.php');

		$whois = new Whois\Info($domain);

		if($whois->get()){
			if($whois->netname){
				return $whois->descr? $whois->netname->getValue() . ', ' . $whois->descr->getValue() : $whois->netname->getValue();
			}elseif(isset($whois->data['~netname'])){
				return isset($whois->data['~desc'])? $whois->data['~netname']->getValue() . ', ' . $whois->data['~desc']->getValue() : $whois->data['~netname']->getValue();
			}
		}

		return false;
		/*
		
		$content = file_get_contents(sprintf('https://nic.ru/whois?query=%s', $domain));

		if (preg_match('~netname:([^\n\r<]+)\s+descr:([^\n\r<]+)~is', $content, $match)) {
			$hoster_name = trim($match[1]);
			$hoster_descr = trim($match[2]);
			return $hoster_name.($hoster_descr?', '.$hoster_descr:'');
		}
		
		$content = file_get_contents(sprintf('https://nic.ru/whois/?query=%s', $domain), false, stream_context_create(array(
			'http'=>array(
				'header'=>array(sprintf("Referer: %s\r\n", 'https://nic.ru/whois'))
			)
		)));
		
		$content = iconv('windows-1251', 'utf-8', $content);
		
		if (!preg_match('~netname:([^\n\r<]+)~is', $content, $match))
			return false;

		$hoster_name = trim(str_replace('&nbsp;', '', $match[1]));
		$hoster_descr = '';

		if (preg_match('~descr:([^\n\r<]+)~is', $content, $match))
			$hoster_descr = trim(str_replace('&nbsp;', '', $match[1]));

		return $hoster_name.($hoster_descr?', '.$hoster_descr:'');
		*/
	}

	public static function check($domain_id, $date = null, $force = false)
	{
		$date = $date ? $date : date('Y-m-d');

		$domain_id = (int)$domain_id;

		if ($force == false && $model = SiteInfo::model()->findByAttributes(['domain_id' => $domain_id, 'checkdate' => $date], ['order'=>'id DESC'])){
			return $model;
		}

		$model = new SiteInfo();
		
		$model->domain = Domain::model()->findByPk($domain_id);
		
		$model->domain_id = $domain_id;

		$model->checkBase();
		$model->checkError404();
		$model->checkRobots();
		$model->checkSiteMap();
		$model->checkCMS();
		$model->checkMeta();
		$model->checkHosting();

		//LiveInternet
		if($li = LiveInternet::getStat($model->domain->domain)){
			$model->limp = $li[0];
			$model->limv = $li[1];
			$model->lidp = $li[4];
			$model->lidv = $li[5];
		}

		//WhoIs
		if($whois = Whois::check($domain_id, $date, $force)){
			$model->created = isset($whois->created)? $whois->created : '';
			$model->paid = isset($whois->expiration)? $whois->expiration : '';
			$model->nsservers = isset($whois->ns)? $whois->ns : '';
			$model->registrar = isset($whois->registrar)? $whois->registrar : '';
		}

		//Solomono
		if($solomono = Solomono::check($domain_id, $date, $force)){ /** @var Solomono $solomono */
			foreach ($solomono->attributes as $key => $value){
				if($key == 'id') continue;
				$model->{$key} = $solomono->{$key};
			}
		}
		
		//if($model->index_count == 0){
		$domain = str_replace('www.','',$model->domain->domain);
		$wwwdomain = 'www.'.$domain;
		
		$content = XMLYandex::getResult('host:'.$domain.' | host:'.$wwwdomain);
		
		//$model->index_count = 0;
		
		if(preg_match('/<found priority="all">([0-9]+)<\/found>/',$content,$foundMatch)){
			$model->index_count = (int)$foundMatch[1];
		}
		//}
		
		
		//CitationAndTrast
		if($ct = CitationTrust::check($domain_id, $date)){
			$model->citation = $ct->citation;
			$model->trust = $ct->trust;
		}

		//YandexAndGoogle
		if($ya = Yandex::check($domain_id, $date, $force)){
			$model->yac = $ya->yac;
			$model->pr = $ya->pr;
			$model->tic = $ya->tic;
		}
		
		/*
			Файл инструкций для поисковых систем robots.txt : 5%
			Карта сайта (sitemap.xml) : 5%
			Обработка ошибки 404 : 5%
			Ответ сервера на запрос даты последней модификации документа : 20%
			Заголовок страницы (Title) : 5%
			Мета-тег description : 5%
			Мета-тег keywords : 5%
			Заголовки текста H1-H6  на главной странице: 5%
			Атрибуты alt и title иллюстраций  на главной странице: 5%
			Яндекс-каталог : 20%
			Яндекс-метрика : 5%
			Яндекс-вебмастер : 5%
			Google-analytics : 5%
			Google-webmaster : 5%
		*/
		
		$model->optimized = 0;
		
		if($model->robots) $model->optimized += 5;
		if($model->sitemap) $model->optimized += 5;
		if($model->error404) $model->optimized += 5;
		if($model->last_modified) $model->optimized += 20;
		if($model->title && $model->title != '') $model->optimized += 5;
		if($model->description && $model->description != '') $model->optimized += 5;
		if($model->keywords && $model->keywords != '') $model->optimized += 5;
		if($model->h1h6) $model->optimized += 5;
		if($model->alts) $model->optimized += 5;
		if($model->yac) $model->optimized += 20;
		if($model->yam) $model->optimized += 5;
		if($model->yaw) $model->optimized += 5;
		if($model->ga) $model->optimized += 5;
		if($model->gw) $model->optimized += 5;


		$model->checkdate = $date;

		if (!$model->save()) {
			CUtils::traceValidationErrors($model);
		}
		return $model;
	}


}
