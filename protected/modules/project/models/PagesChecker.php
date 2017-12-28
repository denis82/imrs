<?php

class PagesChecker {
    const SHINGLE_SIZE = 10; /* количество слов в шингле */
    const CHECK_SIZE = 10;   /* соотношение проверки шинглов 1 из N */

    private $page;
    private $shingles;

    public function __construct( $page ) {
        if ($page instanceof Page) {
            $this->page = $page;
        }
    }

    public function model( $page = null ) {
        return new self($page);
    }

    public function hasShingles() {
        return (Shingle::model()->countByAttributes(array('page_id' => $this->page->id)) > 0);
    }

    public function getShingles() {
        if (!$this->page) {
            return false;
        }

        $text = $this->page->getText( false );

        $words = explode(' ', $text);

        $shingles = array();

        for ($j = 0; $j < ceil(count($words) / self::SHINGLE_SIZE); $j++) {
            $shingles[] = implode(' ', array_slice($words, $j * self::SHINGLE_SIZE, self::SHINGLE_SIZE));
        }

        $shingles_check = array();

        for ($j = 0; $j < ceil(count($shingles) / self::CHECK_SIZE); $j++) {
            $part = array_slice($shingles, $j * self::CHECK_SIZE, self::CHECK_SIZE);
            $shingles_check[] = $part[ rand(0, count($part)-1) ];
        }

        return $shingles_check;
    }

    public function saveShingles( $shingles = null )  {
        if (!$shingles) {
            $shingles = $this->getShingles();
        }

        $result = array();

        if (is_array($shingles)) {
            $this->shingles = array();

            foreach ($shingles as $i) {
                $s = new Shingle;
                $s->page_id = $this->page->id;
                $s->text = $i;
                $s->save();

                $this->shingles[] = $s;
            }
        }

        return $this->shingles;
    }

    public function checkShingles() {
        $total = $score = 0;

        foreach (Shingle::model()->findAllByAttributes(array('page_id' => $this->page->id)) as $s) {
            if (!$s->checked) {
                if (!$s->oncheck) {

                    $s->oncheck = 1;
                    $s->save();

                    $YAXML = new YandexXML();

                    $redis = new Redis();
                    $redis->connect('127.0.0.1');

                    $YAXML->setRedis($redis);

                    $YAXML->addProxy(YandexProxy::create(
                        '127.0.0.1:3128',
                        'paul:zawert',
                        Yii::app()->params['yandexXML']['user'],
                        Yii::app()->params['yandexXML']['key']
                    ));


                    $YAXML->switchProxy();

                    $REGION_ID = (int) 225;

                    $xml = $YAXML->getXML($s->text, $REGION_ID, 10);

                    $POSITION = false;

                    $host = $SITE_DOMAIN = $this->page->domain->host();

                    $uniq = 0;

                    if ($results = YandexXMLResult::parse($xml)) {
                        $foundPhrase = (int)$results->foundDocsPhrase;
                        $foundAll = (int)$results->foundDocsAll;
                        $foundStrict = (int)$results->foundDocsStrict;

                        $foundedPosition = false;

                        $w2 = $w3 = array();
                        $words = explode(' ', $s->text);

                        for ($j = 0; $j <= count($words) - 2; $j++) {
                            $w2[] = implode(' ', array_slice($words, $j, 2));

                            if ($j <= count($words) - 3) {
                                $w3[] = implode(' ', array_slice($words, $j, 3));
                            }
                        }

                        foreach($results->list as $doc){

                            if ($doc->domain == $host) {
                                continue;
                            }

                            $this_uniq = 0;
                            $text = $doc->title . PHP_EOL . implode(PHP_EOL, $doc->passages);

                            foreach ($w3 as $w) {
                                if (strpos($text, $w) !== false) {
                                    $this_uniq = 100;
                                }
                            }

                            if (!$this_uniq) {
                                foreach ($w2 as $w) {
                                    if (strpos($text, $w) !== false) {
                                        $this_uniq += 12.5;
                                    }
                                }
                            }

                            $uniq = max($uniq, $this_uniq);

                            if($doc->position <= 10){
                                $r = new ShinglesResult;
                                $r->shingle_id = $s->id;
                                $r->url = $doc->url;
                                $r->title = $doc->title;
                                $r->text = implode("\n", $doc->passages);
                                $r->uniq = abs(round(100 - $this_uniq));
                                $r->save();
                            }
                        }

                    }

                    $s->checked = 1;
                    $s->oncheck = 0;
                    $s->result = abs(round(100 - $uniq));
                    $s->save();
                }
            }

            $total++;
            $score += $s->result;
        }

        $this->page->uniq = $total ? round($score / $total) : 0;
        $this->page->save();
    }

}
