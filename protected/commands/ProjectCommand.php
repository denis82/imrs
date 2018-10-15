<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class ProjectCommand extends CConsoleCommand {
	private $log_id = null;
	private $queue = null;

	private $log_n = 0;
	
	private $robots_www; // флаг для проверки адресов по умолчанию с www/без www
    	private $robots_http; // флаг для проверки адресов по умолчанию с http/https

	private function getDomain( $model ) {
		if (is_numeric($model)) {
			$model = Domain::model()->findByPk( $model );
		}

		if ($model instanceof Project) {
			$model = Domain::model()->findByPk( $model->domain_id );
		}

		if ($model instanceof Domain) {
			return $model;
		}

		return false;
	}

	private function getProject( $model ) {
		if (is_numeric($model)) {
			$model = Project::model()->findByPk( $model );
		}

		if ($model instanceof Project) {
			return $model;
		}

		return false;
	}

	private function log( $text ) {
		if (!$this->log_id) {
			$this->log_id = date('YmdHis');
		}

		if ($text == 'start') {
			return ;
		}

		if ($text != 'end' and $this->log_n == 0) {
			$f = fopen( Yii::getPathOfAlias('root') . '/../cronlog/project_' . date('Y-m-d') . '.txt' , 'a');
			fputs($f, $this->log_id . " " .  date('H:i:s') . "\t" . 'start' . "\n");
			fclose($f);
		}

		if ($text == 'end' and $this->log_n == 0) {
			$text = 'nothing to do';
		}

		$this->log_n++;

		$f = fopen( Yii::getPathOfAlias('root') . '/../cronlog/project_' . date('Y-m-d') . '.txt' , 'a');
		fputs($f, $this->log_id . " " .  date('H:i:s') . "\t" . $text . "\n");
		fclose($f);
	}

    public function actionCheckQueue( $project_id = 0 ) {

    	$this->log('start');        
    	$query = array(
    		'status' => 0
    	);

    	if ($project_id) {
    		$query['project_id'] = $project_id;
    	}

    	$prev_param = '';

    	foreach (Queue::model()->findAllByAttributes($query, array('order' => 'updated_date asc, stage asc')) as $q) {
    		$this->queue = $q;

    		if ($q->isAllowed() and ($prev_param == '' or $prev_param == $q->object_type . ' ' . $q->object_id) ) {

    			$prev_param = $q->object_type . ' ' . $q->object_id;
                        
		    	$this->log('try ' . $q->id . ' ' . $q->object_type . ' ' . $q->object_id . ' ' . $q->stage);

    			$q->ping();

    			$params = $q->actionParams();
 
    			$action = 'action' . $params['action'];
    			$model = $params['model'];
                        var_dump($action);
    			if ($this->$action( $model )) {
    				$this->log('success');
    				$q->success();
    			}
    			else {
    				$this->log('fail');
    				$q->fail();
    			}

		    	$this->log('break');

    		}
    	}

    	$this->log('end');
    }

    public function actionProjectFullcheck( $model ) {
    	if (is_numeric($model)) {
    		$model = Project::model()->findByPk( $model );
    	}

    	if (!$model or !($model instanceof Project)) {
    		return false;
    	}

    	$model->fullCheck();

		return true;
    }

    public function actionDomainMainpage( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	if (!DomainsMainpage::download( $model )) {
    		return false;
    	}

    	return true;
    }

    public function actionProjectFavicon( $model ) {
    	if (is_numeric($model)) {
    		$model = Project::model()->findByPk( $model );
    	}

    	if (!$model or !($model instanceof Project)) {
    		return false;
    	}

    	if (!ProjectsFavicon::download($model)) {
    		$this->log('no favicon found');
    		return true;
    	}

		return true;
    }

    public function actionProjectScreenshot( $model ) {
    	if (is_numeric($model)) {
    		$model = Project::model()->findByPk( $model );
    	}

    	if (!$model or !($model instanceof Project)) {
    		return false;
    	}

    	ProjectsScreenshot::download( $model );

		return true;
    }

    public function actionDomainWhois( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

		DomainsWhois::model()->download( $model );

		return true;
    }

    public function actionDomainWayback( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

        DomainsWayback::model()->download( $model );

		return true;
    }

    public function actionDomainWhoip( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	DomainsIp::model()->download( $model );

		return true;
    }

    public function actionDomainHttpHeader( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	$domainsHeadersModel =  DomainsHeaders::model()->download( $model );
    	$this->robots_www = $domainsHeadersModel->both_www;
    	$this->robots_http = $domainsHeadersModel->both_http;

		return true;
    }

    public function actionDomainRobots( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}
	//$robotsModel = Robots::model();
	//$robotsModel->both_www = $this->robots_www;
	//$robotsModel->both_http = $this->robots_http;
    	Robots::model()->download( $model );

		return true;
    }

    public function actionDomainSitemap( $model ) {
/*     	if (!($model = $this->getDomain( $model ))) {
     		return false;
     	}
     	
 
         $robots = Robots::model()->last( $model );
 
         $transaction = Yii::app()->db->beginTransaction();
 
         if ($robots) {
             $sitemaps = $robots->sitemaps();
             $default_sitemap = $model->url() . '/sitemap.xml';
 
             if (count($sitemaps) and in_array($default_sitemap, $sitemaps)) {
                 $in_robots = true;
             }
 
             if (!in_array($default_sitemap, $sitemaps)) {
             	$sitemaps[] = $default_sitemap;
             }
 
             $context = stream_context_create(
                 array(
                     'http' => array(
                         'follow_location' => false
                     )
                 )
             );
 
             for ($j = 0; $j < count($sitemaps); $j++) {
             	$sitemap_url = $sitemaps[$j];
             	
             	if ($this->queue) {
             		$this->queue->ping();
             	}
 
                 $text = @file_get_contents($sitemap_url, false, $context);
 
                 if ($text) {
 					if (strpos($text, '<?xml') === false) {
 						$ungz = @gzdecode($text);
 
 						if ($ungz) {
 							$text = $ungz;
 						}
 					}
 
                     $data = new DomainsSitemap;
                     $data->domain_id = $model->id;
                     $data->url = $sitemap_url;
                     $data->robots = intval($in_robots or $sitemap_url != $default_sitemap);
                     $data->text = $text;
                     $data->save();
                 }
             }
 	    
             $sm = new SitemapDownloader( $sitemaps );
             if ($load_result = $sm->load()) {
                 $sm->save( $model->id );
             }
         }
 
 		$transaction->commit();
*/
		return true;
    }

    public function actionDomainSitemapStructure( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}
        
    	
        $crawler = Sitemap::model()->findAllByAttributes(
        	array('domain_id' => $model->id, 'status' => 0), 
        	array('order' => 'url asc')
        );
        //$crawler = array(); // !!!!!!!!!!!!!!!
        if (count($crawler)) {
            foreach ($crawler as $s) {
            	$this->log( '#' . $s->id . ' ' . $s->url );

            	if ($this->queue) {
            		$this->queue->ping();
            	}

                $s->checkStatus();

            	$this->log( '#' . $s->id . ' checked' );

                /* pause 1 second */

                sleep(1);
            }
        }

		return true;
    }
    

    public function actionDomainCms( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

		return true;
    }

    public function actionDomainCounters( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

        $c = new CountersCheck( $model->url() );
        $r = $c->checkAll();

        $data = array();

        foreach ($r as $j => $i) {
            if (!is_array($i)) {
                $i = array($i);
            }

            foreach ($i as $l) {
                $d = new Counter;
                $d->domain_id = $model->id;
                $d->name = $j;
                $d->value = $l;
                $d->save();

                $data[] = $d;
            }
        }

		return true;
    }

    public function actionDomainUniq( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

        $data = Sitemap::model()->findAllByAttributes(array('domain_id' => $model->id, 'status' => 200), array('order' => 'id asc'));

        foreach ($data as $sm) {

        	if ($this->queue) {
        		$this->queue->ping();
        	}

            $page = $sm->page;

        	$this->log('check uniq for sm#' . $sm->id. ' page#' . $page->id);

            if ($page->uniq < 0) {
                $checker = new PagesChecker($page);

                if (!$checker->hasShingles()) {
                    $checker->saveShingles();
                    $checker->checkShingles();
                }
                else {
                    $checker->checkShingles();
                }
            }
        }

		return true;
    }

    public function actionDomainMention( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

        $check = new MentionCheck;
        $check->domain_id = $model->id;
        $check->progress = 1;
        $check->save();

        while ($check->progress == 1) {
        	if ($this->queue) {
        		$this->queue->ping();
        	}

            $result = $check->saveNextPage();
        }

        return true;
    }

    public function actionDomainSsl( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	$browser = new Client([
    		'base_url' => 'https://' . $model->host() ,
    	]);

		try {
			$response = $browser->get('/');
		} catch (RequestException $e) {
		    if ($e->hasResponse()) {
		    	$response = $e->getResponse();
		    }
		}

		$r = new DomainsResult;
		$r->domain_id = $model->id;
		$r->name = 'ssl';

		if ($response and intval($response->getStatusCode()) == 200) {
			$r->value = 'yes';
		}
		else {
			$r->value = 'no';
		}

		$r->save();

		return true;
    }

    public function actionDomainPagespeed( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	$params = array(
    		'key' => Yii::app()->params['google']['pageSpeed']['key'],
    		'url' => $model->url(),
    		'locale' => 'ru',
    		'strategy' => 'desktop',
    	);

    	$url = 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed?';

    	$result = @file_get_contents( $url . http_build_query($params) );

    	if ($result) {
			$r = new DomainsResult;
			$r->domain_id = $model->id;
			$r->name = 'pagespeed_desktop';
			$r->value = $result;
			$r->save();
    	}

    	$params['strategy'] = 'mobile';

    	$result = @file_get_contents( $url . http_build_query($params) );

    	if ($result) {
			$r = new DomainsResult;
			$r->domain_id = $model->id;
			$r->name = 'pagespeed_mobile';
			$r->value = $result;
			$r->save();
    	}

		return true;
    }

    public function actionDomainSpell( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

        $data = Sitemap::model()->findAllByAttributes(array('domain_id' => $model->id, 'status' => 200), array('order' => 'id asc'));

    	$host = 'http://speller.yandex.net';
    	$url = '/services/spellservice.json/checkText';

    	$params = array(
    		'text' => '',
    		'options' => 47,
    		'format' => 'plain',
    	);

    	$this->log( 'Total to DomainSpell check: ' . count($data) );

        foreach ($data as $sm) {
        	if ($this->queue) {
        		$this->queue->ping();
        	}

            $page = $sm->page;

	    	if (!$page) {
		    	$this->log( 'spellcheck not found page for sm#' . $sm->id );
	    		continue;
	    	}

	    	$this->log( 'spellcheck for page#' . $page->id . ' start' );

            $params['text'] = $page->getText();

	    	$browser = new Client([
	    		'base_url' => $host,
	    	]);

			try {
				$response = $browser->post($url, array('body' => $params));
			} catch (RequestException $e) {
			    if ($e->hasResponse()) {
			    	$response = $e->getResponse();
			    }
			}

	    	$this->log( 'spellcheck for page#' . $page->id . ' response=' .intval($response->getStatusCode()) );

			if ($response and intval($response->getStatusCode()) == 200) {
				$body = $response->getBody();

				$spell = new PagesSpell;
				$spell->page_id = $page->id;
				$spell->text = $body;
				$spell->save();
			}
        }

    	$this->log( 'End DomainSpell check' );

		return true;
    }

    public function actionDomainResponseTime( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	$desc = array('time_namelookup', 'time_connect', 'time_pretransfer', 'time_starttransfer', 'time_total');

    	$result = exec("curl -s -w '%{time_namelookup};%{time_connect};%{time_pretransfer};%{time_starttransfer};%{time_total}' -o /dev/null \"".$model->url()."\"");

    	if ($result) {
    		$timer = explode(';', $result);

    		foreach ($timer as $j => $i) {
				$r = new DomainsResult;
				$r->domain_id = $model->id;
				$r->name = $desc[$j];
				$r->value = $i;
				$r->save();
    		}
    	}

		return true;
    }

    public function actionDomainTic( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	$url = 'http://bar-navig.yandex.ru/u?ver=2&url=' . $model->url() . '&show=1' ;

        $opts = array('http' =>
            array(
                'method'  		=> 'GET',
                'user_agent' 	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                'timeout' 		=> 60,
            )
        );
                                
        $context  = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);

        $xml = new SimpleXMLElement($result);

        if ($xml->tcy) {
        	$tcy = $xml->tcy;
        	$tic = 0;

        	foreach ($tcy->attributes() as $j => $i) {
        		if ($j == 'value') {
        			$tic = $i;
        		}
        	}

			$r = new DomainsResult;
			$r->domain_id = $model->id;
			$r->name = 'yandex_tic';
			$r->value = $tic;
			$r->save();
        }

        return true;
    }

    public function actionDomainPhrase( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

    	$domain_phrase = array();
        $grammar = array();

        $pages = $model->sitemap;

        if ($pages and is_array($pages)) {

            PagesPhrase::model()->deleteAllByAttributes(array('domain_id' => $model->id));

            $sql = "insert into tbl_pages_phrase (domain_id, page_id, phrase, gr, qty) values ";

            foreach ($pages as $sm) {
            	if ($this->queue) {
            		$this->queue->ping();
            	}

                $page = $sm->page;

                if ($page) {

                    $text = $page->getText( false );

                    $text = str_replace(array(',', '+', ':', ';'), ' ', $text);

                    $mystem = new Mystem( $text );
                    $mystem->check();

                    $result = array();
                    $words = array();
                    $words_g = array();

                    foreach ($mystem->result as $chunk) {
                    	$result[ $chunk->word ]++;
                    	$grammar[ $chunk->word ] = $chunk->gr;

                        $words[] = mb_strtolower( $chunk->text, 'utf-8' );
                        $words_g[] = $chunk->gr;

                        if (count($words) > 1) {
                        	$w = implode(' ', array_slice($words, -2));

			            	$result[ $w ]++;

			            	if (!isset($grammar[ $w ])) {
			            		$grammar[ $w ] = implode('+', array_slice($words_g, -2));
			            	}
                        }

                        if (count($words) > 2) {
                        	$w = implode(' ', array_slice($words, -3));

			            	$result[ $w ]++;

			            	if (!isset($grammar[ $w ])) {
			            		$grammar[ $w ] = implode('+', array_slice($words_g, -3));
			            	}
                        }
                    }

                    /*$chunks = preg_split('/(\.|\!|\?)/', $text);

                    foreach ($chunks as $chunk) {
                        $words = explode(' ', $chunk);

                        foreach ($words as $j => $i) {
                            if (strlen($i) == 0) {
                                unset($words[$j]);
                            }
                        }

                        for ($j = 0; $j <= count($words) - 2; $j++) {
                            $w2 = implode(' ', array_slice($words, $j, 2));

                            if ($j <= count($words) - 3) {
                                $w3 = implode(' ', array_slice($words, $j, 3));
                            }

                            $result[$w2]++;
                            $result[$w3]++;
                        }
                    }*/

                    $sql_param = array(
                        'did' => $model->id,
                        'pid' => $page->id,
                    );

                    $sql_values = array();
                    $param_values = array();

                    $n = 0;

                    foreach ($result as $j => $i) {
                        $n++;

                    	if (!isset($domain_phrase[ $j ])) {
                    		$domain_phrase[ $j ] = 0;
                    	}

                    	$domain_phrase[ $j ] += $i;

                        $sql_values[] = '(:did, :pid, :phrase' . $n . ', :gr' . $n . ', :qty' . $n . ')';

                        $param_values['phrase' . $n] = $j;
                        $param_values['gr' . $n] = $grammar[ $j ];
                        $param_values['qty' . $n] = $i;

                        if ($n >= 100) {
		                    Yii::app()->db
		                    	->createCommand($sql . implode(', ', $sql_values))
		                    	->execute( array_merge($sql_param, $param_values) );

		                    $n = 0;
		                    $sql_values = array();
		                    $param_values = array();
                        }
                    }

                    if ($n > 0) {
	                    Yii::app()->db
	                    	->createCommand($sql . implode(', ', $sql_values))
	                    	->execute( array_merge($sql_param, $param_values) );
                    }

                }

            }

            if (count($domain_phrase)) {
                $sql_param = array(
                    'did' => $model->id,
                    'pid' => 0,
                );

                $sql_values = array();
                $param_values = array();

                $n = 0;

                foreach ($domain_phrase as $j => $i) {
                    $n++;

                    $sql_values[] = '(:did, :pid, :phrase' . $n . ', :gr' . $n . ', :qty' . $n . ')';

                    $param_values['phrase' . $n] = $j;
                    $param_values['gr' . $n] = $grammar[ $j ];
                    $param_values['qty' . $n] = $i;

                    if ($n >= 100) {
	                    Yii::app()->db
	                    	->createCommand($sql . implode(', ', $sql_values))
	                    	->execute( array_merge($sql_param, $param_values) );

	                    $n = 0;
	                    $sql_values = '';
	                    $param_values = array();
                    }
                }

                if ($n > 0) {
                    Yii::app()->db
                    	->createCommand($sql . implode(', ', $sql_values))
                    	->execute( array_merge($sql_param, $param_values) );
                }
            }

        }

        return true;
    }

    public function actionDomainCrawler( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
			$this->log('domain not found');
    		return false;
    	}

        /*$criteria = new CDbCriteria;
        $criteria->condition = 'url like :query';
        $criteria->params = array(
            'query' => $model->url() . "%",
        );

        foreach (CrawlerPage::model()->findAll($criteria) as $page) {
        	$page->domain_id = $model->id;
        	$page->check = 0;
        	$page->save();
        }*/

		$this->log('start domain crawler');

    	if (!$p = CrawlerPage::model()->findByAttributes(array( 'url' => $model->url() ))) {
			$p = new CrawlerPage;
			$p->domain_id = $model->id;
			$p->url = $model->url();
			$p->save();
    	}

        $opts = array('http' =>
            array(
                'follow_location' 	=> false,
                'method'  			=> 'GET',
                'user_agent' 		=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                'timeout' 			=> 60,
            )
        );

        $context = stream_context_create($opts);

		$this->log('initialized domain crawler');

    	while ($page = CrawlerPage::model()->findByAttributes(array('domain_id' => $model->id, 'check' => 0), array('order' => 'id asc'))) {
			$this->log('page founded #' . $page->id . ' ' . $page->url);

        	if ($this->queue) {
        		$this->queue->ping();
        	}

    		/*print "page " . $page->id . ", " . $page->url . "\n";*/

	        $page->check = 2;
	        $page->save();
		if ( $this->checkUrl($model->id, $page->url) ) {
		    //$page->check = 1;
		    //$page->save();
		    //continue;
		}
    		sleep( 1 );

	        $text = @file_get_contents($page->url, false, $context);
	        $status = 0;
	        $headers = array();

	        foreach ($http_response_header as $h) {
	            $m = array();

	            if (!$status and preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $h, $m )) {
	                $status = intval($m[1]);
	            }
	            else {
	            	$m = explode(':', $h, 2);

	            	$headers[ $m[0] ] = trim($m[1]);
	            }
	        }

	        list($content_type, $content_type_params) = explode(';', $headers['Content-Type'], 2);

	        $page->code = (int) $status;
	        $page->page_crc32 = crc32($text);

	        /*print "status ".$status."\n";*/

		$this->log('crawler page ' . $page->id . ' status ' . $status . ' ' . $page->url);

	        if (trim($content_type) != 'text/html') {
				$this->log('crawler page ' . $page->id . ' delete: content_type ' . $content_type);

				Yii::app()->db->createCommand('DELETE 
					FROM tbl_crawler_links
					WHERE page_id = ' . $page->id . ' 
						or link_id = ' . $page->id
				)->execute();

	        	$page->delete();

	        	$page = null;
	        }
	        elseif ($status !== 200) {
	        	if ($headers['Location']) {

		            $href = $this->normalizeUrl($headers['Location'], $page->url);

		            if ($href === false) {
		            	/*print "NO URL -> next\n";*/
		            	continue;
		            }

		            $out_id = $this->crawlerGetPageId( $href, $model );
	            	$this->crawlerLinkSave( $page->id, $out_id, $anchor );
	        	}
	        }

	        elseif ($text) {

	        	$base_href = $page->url;

		        if (preg_match_all('/<base(.+?)>/si', $text, $matches)) {
		            foreach ($matches[0] as $j) {
			            if (preg_match('/href="(.*?)"/si', $j, $m)) {
			                $base_href = $m[1];
			            }
			            elseif (preg_match('/href=\'(.*?)\'/si', $j, $m)) {
			                $base_href = $m[1];
			            }
			            elseif (preg_match('/href=([^ \f\n\r\t\v>]+)/si', $j, $m)) {
			                $base_href = $m[1];

			                if ($base_href[0] == '"' or $base_href[0] == "'") {
			                    $base_href = substr($base_href, 1, -1);
			                }
			            }
		            }
		        }

		        preg_match('/<body(.+?)>(.*)<\/body>/si', $text, $m);

		        $text = $m[2];

		        $remove = array(
		            '/<script(.+?)<\/script>/si',
		            '/<style(.+?)<\/style>/si',
		            '/<!--(.+?)-->/si',
		        );

		        foreach ($remove as $regex) {
		            $text = preg_replace($regex, '', $text);
		        }

		        $matches = array();

		        preg_match_all('/<a (.+?)<\/a>/si', $text, $matches);

		        foreach ($matches[0] as $a) {
		            $anchor = $href = '';

		            if (preg_match('/href="(.*?)"/si', $a, $m)) {
		                $href = $m[1];
		            }
		            elseif (preg_match('/href=\'(.*?)\'/si', $a, $m)) {
		                $href = $m[1];
		            }
		            elseif (preg_match('/href=([^ \f\n\r\t\v>]+)/si', $a, $m)) {
		                $href = $m[1];

		                if ($href[0] == '"' or $href[0] == "'") {
		                    $href = substr($href, 1, -1);
		                }
		            }

		            if (preg_match('/>(.*)<\/a>/si', $a, $m)) {
		                $anchor = trim($m[1]);
		            }

		            $href = $this->normalizeUrl($href, $base_href);

		            if ($href === false) {
		            	/*print "NO URL -> next\n";*/
		            	continue;
		            }

		            $out_id = $this->crawlerGetPageId( $href, $model );
	            	$this->crawlerLinkSave( $page->id, $out_id, $anchor );
		        }

	        }

	        if ($page) {
		        $page->check = 1;
		        $page->save();
	        }

    		/*print "page end\n";*/

    		sleep( 1 );
    	}

		$this->log('end of domain crawler');

        return true;
    }

    private function crawlerGetPageId( $url, $model ) {
        $out = CrawlerPage::model()->findByUrl($url);

        if ($out) {
        	return $out->id;
        }

        $sql_page = "INSERT IGNORE INTO tbl_crawler_page 
        	(`domain_id`, `url`, `url_hash`, `check`, `created_date`, `updated_date`) 
        	VALUES 
        	(:did, :url, :hash, :chk, NOW(), NOW())
        ";

    	$is_child = $this->isChildDomain($url, $model->url());

        $params = array(
            'did' => $is_child ? $model->id : 0,
            'url' => $url,
            'hash' => CrawlerPage::urlHash( $url ),
            'chk' => $is_child ? 0 : 1,
        );

        $r = Yii::app()->db->createCommand($sql_page)->execute($params);

    	/*print 'inserted ' . $r . ' rows for ' . $url . PHP_EOL;*/

        if ($r and Yii::app()->db->lastInsertID) {
        	return Yii::app()->db->lastInsertID;
        }

    	/*print 'NOT FOUND !!! ' . $url . PHP_EOL;*/
    	sleep(1);

        $out = CrawlerPage::model()->findByUrl($url);

        if ($out) {
        	return $out->id;
        }

        $this->log( 'DIE !!! DIE !!!' . $url );

    	print 'DIE !!! DIE !!!' . $url . PHP_EOL;
    	die();

    	return 0;
    }

    private function crawlerLinkSave( $page_id, $link_id, $anchor ) {
    	if (!$page_id or !$link_id) {
    		return false;
    	}

        $sql_page = "INSERT IGNORE INTO tbl_crawler_links 
        	(`page_id`, `link_id`, `anchor`) 
        	VALUES 
        	(:pid, :lid, :anchor)
        ";

        $params = array(
            'pid' => $page_id,
            'lid' => $link_id,
            'anchor' => $anchor,
        );

        $r = Yii::app()->db->createCommand($sql_page)->execute($params);

    	/*print 'inserted ' . $r . ' link for ' . $page_id . ' / ' . $link_id . PHP_EOL;*/
    }

    private function isChildDomain( $url, $base ) {
    	$parts = parse_url($url);
    	$parent = parse_url($base);

    	if (!$parts['host']) {
    		return true;
    	}

    	if (substr($parts['host'], -strlen($parent['host'])) == $parent['host']) {
    		return true;
    	}

    	return false;
    }

    private function normalizeUrl( $url, $base ) {
    	$url = trim($url);

    	$except = array('javascript:', 'mailto:', 'tel:', '#');

    	foreach ($except as $j) {
			if (substr($url, 0, strlen( $j ) ) == $j) {
				return false;
			}
    	}

        $base_url  = parse_url($base);
        $base_path = pathinfo($base_url['path'] . ((substr($base_url['path'], -1) == '/') ? '.' : '') );
        $base_host = $base_url['scheme'] . '://' . $base_url['host'];
        $base_dir  = $base_path['dirname'] . ( (substr($base_path['dirname'], -1) == '/') ? '' : '/' );

        if (substr($url, 0, 2) == '//') {
            $url = $base_url['scheme'] . ':' . $url;
        }

        $p = parse_url($url);

		$b = explode('/', $p['path']);
		$c = array();
		foreach ($b as $j => $i) {
			if ($i == '..') {
				$c = array_slice($c, 0, -1);
			}
			else {
				$c[] = $i;
			}
		}

		$p['path'] = implode('/', $c);

		if (substr($p['path'], 0, 1) != '/') {
			$p['path'] = '/' . $p['path'];
		}

        if ($p['host']) {
            $url = ($p['scheme'] ? $p['scheme'] : $base_url['scheme']) . '://' . 
            	$p['host'] . ( ($p['path'][0] == '/') ? $p['path'] : '/' . $p['path']);
        }
        else {
            $url = $base_host . ( ($p['path'][0] == '/') ? $p['path'] : $base_dir . $p['path']);
        }

        if ($p['query']) {
        	$url.= '?' . str_replace('&amp;', '&', $p['query']);
        }

        if ($p['fragment']) {
        	$url.= '#' . $p['fragment'];
        }

        return $url;
    }

    public function actionDomainPagerank( $model ) {
    	if (!($model = $this->getDomain( $model ))) {
    		return false;
    	}

		Yii::app()->db->createCommand('DELETE r 
			FROM tbl_crawler_page_rank as r
				INNER JOIN  tbl_crawler_page as p using (id)
			WHERE p.domain_id = ' . $model->id 
		)->execute();

		Yii::app()->db->createCommand('INSERT INTO tbl_crawler_page_rank
			(id, rank1, rank2, rank3)
			SELECT id, 1, 1, 1 FROM tbl_crawler_page WHERE domain_id = ' . $model->id
		)->execute();

    	foreach ( CrawlerPage::model()->findAllByAttributes(array('domain_id' => $model->id), array('order' => 'id asc')) as $page) {
        	if ($this->queue) {
        		$this->queue->ping();
        	}

    		/*print $page->id . " = ";*/

			$in = Yii::app()->db->createCommand()
			    ->select('count(*)')
			    ->from('tbl_crawler_links')
			    ->where('link_id = ' . $page->id)
			    ->queryScalar();

			$out = Yii::app()->db->createCommand()
			    ->select('count(*)')
			    ->from('tbl_crawler_links')
			    ->where('page_id = ' . $page->id)
			    ->queryScalar();

			Yii::app()->db->createCommand('UPDATE tbl_crawler_page_rank 
				SET `in` = ' . $in . ', `out` = ' . $out . ', `rank1` = ' . (1 + ($in/10)) . '  
				WHERE `id` = ' . $page->id
			)->execute();

			$this->log('check pagerank for ' . $page->id . ' (in ' . $in . ', out ' . $out . ')');

			/*$this->updatePRLevel( $model->id, $page->id );*/
    	}

        return true;
    }

    private function updatePRLevel( $domain_id, $page_id, $rank = 1, $level = 0.1) {
		foreach (CrawlerLinks::model()->findAllByAttributes(array('page_id' => $page_id)) as $link) {
        	if ($this->queue) {
        		$this->queue->ping();
        	}

			$p = CrawlerPage::model()->findByPk( $link->link_id );

			if ($p->domain_id == $domain_id) {
				Yii::app()->db->createCommand('UPDATE tbl_crawler_page_rank 
					SET `rank' . $rank . '` = `rank' . $rank . '` + ' . $level . '
					WHERE `id` = ' . $p->id
				)->execute();
			}

			if ($rank < 3) {
				$this->updatePRLevel( $domain_id, $p->id, $rank+1, $level/10);
			}
		}
    }
    
    private function checkUrl($domain_id, $url) {
	$validateUrl = new ValidateUrl();
	$result = $validateUrl->check( $domain_id, $url);
	
    }
    

    public function actionProjectYastruct( $model ) {
    	if (!($model = $this->getProject( $model ))) {
    		return false;
    	}

        $host = $model->domain->host();

        $YAXML = new YandexXML();

        $redis = new Redis();
        $redis->connect('127.0.0.1');

        $YAXML->setRedis($redis);

        $YAXML->addProxy(YandexProxy::create(
            Yii::app()->params['yandexXML']['proxy_address'],
            Yii::app()->params['yandexXML']['proxy_auth'],
            Yii::app()->params['yandexXML']['user'],
            Yii::app()->params['yandexXML']['key']
        ));

        $YAXML->switchProxy();

        $word = 'url:' . $host . '*';

        $check = new YandexStructureCheck;
        $check->project_id = $model->id;
        $check->domain_id = $model->domain_id;
        $check->save();

        $REGION_ID = (int) $model->regions[0];
        
        $arrayUrl = array();

        for ($page = 0; $page < 100000000; $page++) {
	        $xml = $YAXML->getXML($word, $REGION_ID, 100, $page);

	        $page_total = 0;

            if ($results = YandexXMLResult::parse($xml)) {
                foreach($results->list as $doc){
                	$page_total++;

                    if ($doc->domain == $host) {
	                    $total++;

	                    $ypage = new YandexStructure;
	                    $ypage->project_id = $model->id;
	                    $ypage->domain_id = $model->domain_id;
	                    $ypage->check_id = $check->id;
	                    $ypage->title = $doc->title;
	                    $ypage->url = $doc->url;
	                    $ypage->save();
	                    
	                    //$yasitemappage = new Sitemap;
	                    //$yasitemappage = Sitemap::model()->findAllByAttributes( array('domain_id' => $model->domain_id));
	                    //$yasitemappage->project_id = $model->id;
	                    //$yasitemappage->domain_id = $model->domain_id;
	                    //$yasitemappage->url = $doc->url;
	                    $arrayUrl[] = $doc->url;
	                    //$yasitemappage->hash = md5( $model->domain_id . $doc->url );
	                    //$yasitemappage->check_id = $check->id;
	                    //$yasitemappage->title = $doc->title;
	                    //$yasitemappage->save();
	                }
                }
            }

            if ($page_total == 0) {
            	break;  // !!!!!!!!! сделать флаг чтоб начал собирать из сайтмапа
            }
        }

         $sql = "insert ignore into tbl_sitemap (domain_id, url, hash) values ";

        $parameters = array(
            'did' => $model->domain_id,
        );
        
        if ( empty($arrayUrl) ) {
	    return true;
        }
        
        foreach ($arrayUrl as $i => $j) {
            if ($i > 0) $sql .= ', ';

            $sql.= '(:did, :url' . $i . ', :hash' . $i . ')';

            $parameters['url' . $i] = $j;
            $parameters['hash' . $i] = md5( $model->domain_id . $j );
        }
	
        Yii::app()->db->createCommand($sql)->execute($parameters);
        
        return true;
    }
    
    public function actionDomainYandexmap( $model ) {
	
	if (!($model = $this->getDomain( $model ))) {
	    return false;
    	}
    	$crawler = YandexStructure::model()->findAllByAttributes(
        	//array('domain_id' => $model->id, 'status' => 0), 
        	array('domain_id' => $model->id, 'status' => 0), 
        	array('order' => 'url asc')
        );
    	//var_dump('count = ' . count($crawler));
        if (count($crawler)) {
            foreach ($crawler as $s) {
            	$this->log( '#' . $s->id . ' ' . $s->url );

            	if ($this->queue) {
            		$this->queue->ping();
            	}
                $s->checkStatus();
            	$this->log( '#' . $s->id . ' checked' );

                /* pause 1 second */

                sleep(1);
            }
        }
		return true;
    }

    
    public function actionSlashTest( ) {
	
	$domainsHeadersModel =  DomainsHeaders::model()->getStatusSlash("https://seo-experts.com/service/");
	
// 	if (!($model = $this->getDomain( $model ))) {
// 	    return false;
//     	}
//     	$crawler = YandexStructure::model()->findAllByAttributes(
//         	//array('domain_id' => $model->id, 'status' => 0), 
//         	array('domain_id' => $model->id, 'status' => 0), 
//         	array('order' => 'url asc')
//         );
//     	//var_dump('count = ' . count($crawler));
//         if (count($crawler)) {
//             foreach ($crawler as $s) {
//             	$this->log( '#' . $s->id . ' ' . $s->url );
// 
//             	if ($this->queue) {
//             		$this->queue->ping();
//             	}
//                 $s->checkStatus();
//             	$this->log( '#' . $s->id . ' checked' );
// 
//                 /* pause 1 second */
// 
//                 sleep(1);
//             }
//         }
	    var_dump($domainsHeadersModel);
		return true;
    }

}


