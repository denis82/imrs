<?php

/**
 * This is the model class for table "{{history}}".
 *
 * The followings are the available columns in table '{{history}}':
 * @property integer $id
 * @property string $timestamp
 * @property string $host
 * @property string $created
 * @property string $paid
 * @property string $registrar
 * @property string $nsservers
 * @property string $ip
 * @property string $hoster
 * @property double $starttransfer_time
 * @property double $time *
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
 * @property integer $top50
 * @property integer $citation
 * @property integer $trust
 */
class History extends CActiveRecord
{

    protected $header = "";
    protected $body = "";
    public $checkkeywords = array();

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return History the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{history}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('timestamp', 'required'),
            array('error404, robots, h1h6, alts, tic, pr, yac, yam, yaw, ga, gw, limp, limv, lidp, lidv, index_count, mr_sites, ip_sites, hin, hin_l1, hin_l2, hin_l3, hin_l4, din, din_l1, din_l2, din_l3, din_l4, hout, hout_l1, hout_l2, hout_l3, hout_l4, dout, anchors, anchors_out, top50, citation, trust', 'numerical', 'integerOnly' => true),
            array('time, starttransfer_time', 'numerical'),
            array('host, registrar, ip, hoster, sitemap, cms, igood', 'length', 'max' => 250),
            array('created, paid, nsservers, robots_txt, index_date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, timestamp, starttransfer_time, host, created, paid, registrar, nsservers, ip, hoster, time, error404, robots, robots_txt, sitemap, cms, title, description, keywords, h1h6, alts, tic, pr, yac, yam, yaw, ga, gw, limp, limv, lidp, lidv, index_count, index_date, mr_sites, ip_sites, hin, hin_l1, hin_l2, hin_l3, hin_l4, din, din_l1, din_l2, din_l3, din_l4, hout, hout_l1, hout_l2, hout_l3, hout_l4, dout, anchors, anchors_out, igood, top50, citation, trust', 'safe', 'on' => 'search'),
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
            'timestamp' => 'Timestamp',
            'host' => 'Host',
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
            'top50' => 'Top 50',
            'starttransfer_time' => 'starttransfer_time'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('timestamp', $this->timestamp, true);
        $criteria->compare('host', $this->host, true);
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
        $criteria->compare('starttransfer_time', $this->starttransfer_time);

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
        $criteria->compare('top50', $this->top50);
        $criteria->compare('citation', $this->citation);
        $criteria->compare('trust', $this->trust);


        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function check($host)
    {
        $host = basename($host);
        $convert = new idna_convert();
        $host = $convert->encode((string)$host);
        $this->host = $host;

        $li = LiveInternet::getStat($this->host);
        $this->limp = $li[0];
        $this->limv = $li[1];
        $this->lidp = $li[4];
        $this->lidv = $li[5];


        //Получить заголовок сайта
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_URL, $this->host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
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
        $cp = explode("=", $this->header["Content-Type"]);
        $codepage = $cp[1];

        //Получить содержание
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $tstart = $mtime[1] + $mtime[0];
        $this->body = $this->getSource($codepage);
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->time = ($mtime - $tstart);
        $this->ip = gethostbyname($this->host);


        $this->getHosting();
        $this->checkWhois();
        $this->checkError404();
        $this->checkRobots();
        $this->checkSiteMap();
        $this->checkCMS();
        $this->getMeta();

       // $xml = new SimpleXMLElement(file_get_contents("http://xml.solomono.ru/?url=" . $this->host));
        
        $xml_string = file_get_contents('http://xml.linkpad.ru/?url=' . $this->host);
        $xml_string = stripslashes($xml_string);
        
        $xml = new SimpleXMLElement($xml_string);

        $this->mr_sites = (string)$xml->mr;

        $this->ip_sites = (string)$xml->ip;


        $this->index_count = intval($xml->index);
        $iattr = $xml->index->attributes();
        $this->index_date = date("Y-m-d", strtotime($iattr["date"]));
        $this->pr = GooglePageRankChecker::getRank($this->host);
        $dattr = $xml->din->attributes();
        $this->din = (string)$xml->din;
        $this->din_l1 = (string)$dattr["l1"];
        $this->din_l2 = (string)$dattr["l2"];
        $this->din_l3 = (string)$dattr["l3"];
        $this->din_l4 = (string)$dattr["l4"];
        $hattr = $xml->hin->attributes();
        $this->hin = (string)$xml->hin;
        $this->hin_l1 = (string)$hattr["l1"];
        $this->hin_l2 = (string)$hattr["l2"];
        $this->hin_l3 = (string)$hattr["l3"];
        $this->hin_l4 = (string)$hattr["l4"];
        $hattr = $xml->hout->attributes();
        $this->hout = (string)$xml->hout;
        $this->hout_l1 = (string)$hattr["l1"];
        $this->hout_l2 = (string)$hattr["l2"];
        $this->hout_l3 = (string)$hattr["l3"];
        $this->hout_l4 = (string)$hattr["l4"];
        $this->dout = (string)$xml->dout;
        $this->anchors = (string)$xml->anchors;
        $this->anchors_out = (string)$xml->anchors_out;
        $this->igood = (string)$xml->igood;
        $this->top50 = SeoUtils::getTop50($this->host);

        list($citation, $trust) = SeoUtils::getCitationTrustFlow($this->host);
        $this->citation = $citation;
        $this->trust = $trust;

        $this->tic = SeoUtils::getTIC($this->host);
        $this->checkYaca();

        $this->yam = is_int(strpos($this->body, "Ya.Metrika")) ? 1 : 0;
        $this->ga = is_int(strpos($this->body, "var _gaq = _gaq || [];")) ? 1 : 0;

        /*
        foreach (explode(",", Yii::app()->request->getParam("keywords", "")) as $keyword) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://xmlsearch.yandex.ru/xmlsearch?user=kirshinas&key=03.129836852:68279dadbefb75e39fe238831575d40a&query=$keyword&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D10.docs-in-group%3D3&maxpassages=3&page=1");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.0 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
            $source = curl_exec($curl);
            curl_close($curl);
            $this->checkkeywords[$keyword] = $source;
        }
        */
    }

    public function getHosting()
    {

        $info = file_get_contents("http://ipinfodb.com/ip_locator.php?ip=" . $this->ip);
        $info = str_replace("\r", "", $info);
        $info = str_replace("\n", "", $info);
        $info = str_replace("\t", "", $info);
        preg_match_all('/<li>Hostname : (.*?)<\/li>/', $info, $matches);
        $hoster = explode(".", $matches[1][0]);
        $hoster = array_reverse($hoster);
        $this->hoster = $hoster[1] . "." . $hoster[0];
    }

    function checkYaca()
    {
        $this->yac = 0;
        $codepage = 'utf-8';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://yaca.yandex.ru/yca?text=http%3A%2F%2F" . $this->host . "&yaca=1");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/html; charset=$codepage"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.0 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        $source = curl_exec($curl);
        curl_close($curl);
        $source = mb_convert_encoding($source, 'HTML-ENTITIES', $codepage);
        $source = str_replace("\r", "", $source);
        $source = str_replace("\n", "", $source);
        if ($source) {
            preg_match('/<div class=\"z\-counter\">.*?(\d{1,5})<\/div>/', $source, $out);
            if (trim($out[1]) > 0)
                $this->yac = 1;
        }
    }

    public function checkWhois()
    {
        $whois = SeoUtils::getWhois($this->host);
        $this->created = $whois["creation"];
        $this->paid = $whois["expiration"];
        $this->nsservers = $whois["ns"];
        $this->registrar = $whois["registrar"];
    }

    public function checkRobots()
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_URL, $this->host . "/robots.txt");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $sourse = curl_exec($curl);
        $header = curl_getinfo($curl);
        if ($header["http_code"] == 404 || !(strpos($header['content_type'], "text/plain") === 0)) {
            $this->robots = 0;
        } else {
            $this->robots = 1;
            $this->robots_txt = $sourse;
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
        $sourse = curl_exec($curl);
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
        $sourse = curl_exec($curl);
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
            $sourse = curl_exec($curl);
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
        curl_setopt($curl, CURLOPT_URL, $this->host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        $sourse = curl_exec($curl);
        curl_close($curl);
        if (is_int(strpos($sourse, "Expires: Sun, 19 Nov 1978 05:00:00 GMT"))) {
            $this->cms = '<a href="http://drupal.org/">Drupal</a>';
            return;
        }
        //WordPress http://wordpress.org/
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        $sourse = curl_exec($curl);
        curl_close($curl);
        if (is_int(strpos($sourse, "wp-content"))) {
            $this->cms = '<a href="http://wordpress.org/">WordPress</a>';
            return;
        }
        //1C:Битрикс http://www.1c-bitrix.ru/
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->host . "/bitrix/tools/help.css");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        $sourse = curl_exec($curl);
        curl_close($curl);
        if (is_int(strpos($sourse, "/*Help*/"))) {
            $this->cms = '<a href="http://www.1c-bitrix.ru/">1C:Битрикс</a>';
            return;
        }
        /*
          DLE	 признаков использования не найдено
          Joomla	 признаков использования не найдено
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
          HostCMS	 признаков использования не найдено
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

    public function getMeta()
    {

        $source = $this->body;

        $dom = new DOMDocument();
        $dom->strictErrorChecking = false;
        @$dom->loadHTML($source);
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query("//title") as $title) {
            $this->title = $title->nodeValue;
        }
        //<meta name='yandex-verification' content='515819e285d77adc' />
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
        $this->h1h6 = ($xpath->query('//h1')->length > 0 || $xpath->query('//h2')->length > 0 || $xpath->query('//h3')->length > 0 || $xpath->query('//h4')->length > 0 || $xpath->query('//h5')->length > 0 || $xpath->query('//h6')->length > 0) ? 1 : 0;
        foreach ($xpath->query('//img') as $img) {
            $length = $img->attributes->length;
            for ($i = 0; $i < $length; ++$i) {
                $name = $img->attributes->item($i)->name;
                if ($name == "alt") {
                    $alt = 1;
                }
                if ($name == "title") {
                    $title = 1;
                }
            }
            $this->alts = ($alt || $title) ? 1 : 0;
        }
    }

    public function getSource($codepage = 'utf-8')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.0 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        $source = curl_exec($curl);
        $info = curl_getinfo($curl);
        $this->starttransfer_time = $info["starttransfer_time"];
        curl_close($curl);
        if ($codepage)
            $source = mb_convert_encoding($source, 'HTML-ENTITIES', $codepage);
        $source = str_replace("\r", "", $source);
        $source = str_replace("\n", "", $source);
        return $source;
    }

    public function afterFind()
    {
        $this->created = date("d.m.Y", strtotime($this->created));
        $this->paid = date("d.m.Y", strtotime($this->paid));
        return parent::beforeSave();
    }

    public function beforeSave()
    {
        $this->created = date("Y-m-d", strtotime($this->created));
        $this->paid = date("Y-m-d", strtotime($this->paid));
        return parent::beforeSave();
    }

}