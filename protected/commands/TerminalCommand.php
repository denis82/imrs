<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class TerminalCommand extends CConsoleCommand {
	private $project;

    public function actionIfModifiedCheck($url, $date) {
    	print $url . "\n" . date('r', strtotime($date)) . "\n\n";

        $opts = array('http' =>
            array(
                'method'  		=> 'GET',
                'user_agent' 	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                'header'  		=> "If-Modified-Since: " . date('r', strtotime($date)) . "\r\n",
                'timeout' 		=> 60,
            )
        );
                                
        $context  = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);

        foreach ($http_response_header as $h) {
        	print $h . "\n";
        }

        return true;
    }

    public function actionTic($url) {
    	print $url . "\n\n";

    	$url = 'http://bar-navig.yandex.ru/u?ver=2&url=' . $url . '&show=1' ;

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

        	print $tic . "\n";
        }
    }

    public function actionMystem() {

		@exec( '/var/www/audit/mystem/mystem /var/www/audit/mystem/1 -lig --format=json', $output);

		if ($output and is_array($output)) {
			foreach ($output as $line) {
				$word = json_decode($line);

				print_r($word);
				die();

				list($word, $tmp) = explode('|', $line, 2);

				$word = str_replace('?', '', $word);

				$this->result[$word]++;
			}
		}

    }

    public function actionCrawlerStart( $url ) {
    	if ($p = CrawlerPage::model()->findByAttributes(array( 'url' => $url ))) {
    		print "exists id=" . $p->id . ", check=" . $p->check . ", code=" . $p->code . "\n";
    		return ;
    	}

    	$p = new CrawlerPage;
    	$p->url = $url;

    	if ($p->save()) {
    		print "created\n";
    	}
    	else {
    		print "error on create\n";
    	}
    }

    public function actionCrawlerPageRank( $rank = 1 ) {

    	if ($rank == 1) {

			$data = Yii::app()->db->createCommand()
			    ->select('link_id, count(*) as total')
			    ->from('tbl_crawler_links')
			    ->where('link_id > 220000')
			    ->group('link_id')
			    ->order('link_id asc')
			    ->queryAll();
			    /*->where('1')*/

			foreach ($data as $j => $i) {

				$r = (1 + ($i['total'] / 10));

				Yii::app()->db->createCommand('update tbl_crawler_page_rank set `rank1` = ' . $r . ' where `id` = ' . $i['link_id'] )->execute();
					/*->update(
						'tbl_crawler_page_rank', 
						array('rank1' => ':rank1'), 
						'id = :id',
						array(
							':id' => $i['link_id'],
							':rank1' => (1 + ($i['total'] / 10)),
						)
					)
					->execute();*/

				/*if (!$pagerank = CrawlerPageRank::model()->findByPk( $i['link_id'] )) {
					$pagerank = new CrawlerPageRank;
					$pagerank->id = $i['link_id'];
				}

				$pagerank->rank1 = 1 + ($i['total'] / 10);
				$pagerank->save();*/

				if ($i['link_id'] % 1000 == 0) {
					print $i['link_id'] . ' = ' . $r . PHP_EOL;
				}
			}

			print 'done';

    		return ;
    	}


    	foreach ( CrawlerPage::model()->findAll(array('order' => 'id asc')) as $page) {
    		print $page->id . " = ";

    		if (!$pagerank = CrawlerPageRank::model()->findByPk( $page->id )) {
    			$pagerank = new CrawlerPageRank;
    			$pagerank->id = $page->id;
    			$pagerank->rank1 = 1;
    			$pagerank->save();
    		}

    		if ($rank == 1) {
    			$n = CrawlerLinks::model()->countByAttributes(array('link_id' => $page->id)) / 10;

    			$pagerank->rank1 = 1 + $n;
    			$pagerank->save();

	    		print $pagerank->rank1;
    		}

    		else {
    			$n = 0;

    			$var1 = 'rank' . ($rank - 1);
    			$var2 = 'rank' . $rank;

    			foreach (CrawlerLinks::model()->findAllByAttributes(array('link_id' => $page->id)) as $link) {
    				$pl = CrawlerPageRank::model()->findByPk( $link->page_id );
    				$n += ($pl->$var1 / 10);
    			}

    			$pagerank->$var2 = $pagerank->$var1 + $n;
    			$pagerank->save();

	    		print $pagerank->$var2;
    		}

    		print PHP_EOL;
    	}

    	print 'done';
    }

    public function actionPageRankForDomain( $domain ) {

    	$domain = intval( $domain );

		Yii::app()->db->createCommand('DELETE r 
			FROM tbl_crawler_page_rank as r
				INNER JOIN  tbl_crawler_page as p using (id)
			WHERE p.domain_id = ' . $domain 
		)->execute();

		Yii::app()->db->createCommand('INSERT INTO tbl_crawler_page_rank
			(id, rank1, rank2, rank3)
			SELECT id, 1, 1, 1 FROM tbl_crawler_page WHERE domain_id = ' . $domain
		)->execute();

    	foreach ( CrawlerPage::model()->findAllByAttributes(array('domain_id' => $domain), array('order' => 'id asc')) as $page) {
    		print $page->id . " = ";

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

			print 'in ' . $in . ', out ' . $out . PHP_EOL;

			/*$this->updatePRLevel( $domain, $page->id );*/

    		print PHP_EOL;
    	}

    	print 'done';
    }

    private function updatePRLevel( $domain_id, $page_id, $rank = 1, $level = 0.1) {

    	$n = 0;

    	$pad = str_pad('', $rank * 4 -2, " ");

    	print $pad . 'pr'  . $rank . PHP_EOL;

		foreach (CrawlerLinks::model()->findAllByAttributes(array('page_id' => $page_id)) as $link) {
			$p = CrawlerPage::model()->findByPk( $link->link_id );

			if ($p->domain_id == $domain_id) {
				Yii::app()->db->createCommand('UPDATE tbl_crawler_page_rank 
					SET `rank' . $rank . '` = `rank' . $rank . '` + ' . $level . '
					WHERE `id` = ' . $p->id
				)->execute();

				$n++;

				/*print str_pad('', $rank * 4, " ") . $p->id . " +" . $level . PHP_EOL;*/
			}


			if ($rank < 3) {
				$this->updatePRLevel( $domain_id, $p->id, $rank+1, $level/10);
			}

		}

		print $pad . 'added' . $n . PHP_EOL;
    }

    public function actionCrawlerPageRankGet() {

		/*$data = Yii::app()->db->createCommand()
		    ->select('l.link_id, count(*) as total')
		    ->from('tbl_crawler_links l')
		    ->where('1')
		    ->group('l.link_id')
		    ->order('l.link_id asc')
		    ->queryAll();*/

		$data = Yii::app()->db->createCommand()
		    ->select('l.link_id, p.url as link, p.domain_id as domain, count(*) as total')
		    ->from('tbl_crawler_links l')
		    ->leftJoin('tbl_crawler_page p', 'l.link_id = p.id')
		    ->where('1')
		    ->group('l.link_id')
		    ->order('l.link_id asc')
		    ->queryAll();
		    /*->where('1')*/

		print 'start writing' . PHP_EOL;

    	$f = fopen('/var/www/audit/www/protected/pagerank.csv', 'w');

			foreach ($data as $j => $i) {

				if ($i['domain'] == 55745) {
					fputs($f, (10 + $i['total']) . ';' . $i['link']. ';' . PHP_EOL);
				}

			}

		fclose($f);

    	print 'done' . PHP_EOL;
    }

    public function actionCrawlerPageRankGetLinks() {

    	/*

SELECT p1.url, p2.url 
FROM `tbl_crawler_links` as l 
left join tbl_crawler_page as p1 on (l.link_id = p1.id) 
left join tbl_crawler_page as p2 on (l.page_id = p2.id) 
WHERE p1.domain_id = 55745 
order by link_id asc limit 0, 10

    	*/

		$data = Yii::app()->db->createCommand()
		    ->select('p1.url as url1, p2.url as url2')
		    ->from('tbl_crawler_links l')
		    ->leftJoin('tbl_crawler_page p1', 'l.link_id = p1.id')
		    ->leftJoin('tbl_crawler_page p2', 'l.page_id = p2.id')
		    ->where('p1.domain_id = 55745')
		    ->order('l.link_id asc, l.page_id asc')
		    ->queryAll();
		    /*->where('1')*/

		print 'start writing' . PHP_EOL;

    	$f = fopen('/var/www/audit/www/protected/pageranklink.csv', 'w');

			foreach ($data as $j => $i) {
				/*$p1 = CrawlerPage::model()->findByPk( $i['id1'] );

				if ($p1->domain_id == 55745) {
					$p2 = CrawlerPage::model()->findByPk( $i['id2'] );*/

					fputs($f, $i['url1'] . ';' . $i['url2'] . ';' . PHP_EOL);
				/*}*/

			}

		fclose($f);

    	print 'done' . PHP_EOL;
    }

    public function actionCrawler( ) {

        $opts = array('http' =>
            array(
                'follow_location' 	=> false,
                'method'  			=> 'GET',
                'user_agent' 		=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                'timeout' 			=> 60,
            )
        );

        $context = stream_context_create($opts);

        @file_get_contents('http://ritual-1.ru/public/files/products/40c2f81c04ea1fe4b5a140c893aacd1b.jpg');

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

	        list($content_type, $params) = explode(';', $headers['Content-Type'], 2);

	        if (trim($content_type) == 'text/html') {
	        	print 'ok';
	        }
	        else {
	        	print $headers['Content-Type'];
	        }

	        print "\n";

        die();

    	while ($page = CrawlerPage::model()->findByAttributes(array('check' => 0), array('order' => 'id asc'))) {
    		print "page " . $page->id . ", " . $page->url . "\n";

	        $page->check = 2;
	        $page->save();

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

	        $page->code = (int) $status;
	        $page->page_crc32 = crc32($text);

	        print "status ".$status."\n";

	        if ($status !== 200) {
	        	if ($headers['Location']) {

		            $href = $this->normalizeUrl($headers['Location'], $page->url);

		            if ($href === false) {
		            	print "NO URL -> next\n";
		            	continue;
		            }


		            if ($out = CrawlerPage::model()->findByAttributes(array('url' => $href))) {
		            }
		            else {

				    	$out = new CrawlerPage;
				    	$out->url = $href;

				    	if (!$this->isChildDomain($href, $model->url())) {
				    		print $href . " NOT CHILD" . PHP_EOL;
				    		$out->check = 1;
				    	}

				    	if ($out->save()) {
		            		print "NEW = " . $out->id . " " . $href . "\n";
		            	}
		            	else {
		            		print "NEW ERROR !!! " . $href . " \n";
		            	}

		            }

		            if ($out->id) {
		            	if ($link = CrawlerLinks::model()->findByAttributes(array('page_id' => $page->id, 'link_id' => $out->id))) {
		            	}
		            	else {
		            		$link = new CrawlerLinks;
		            		$link->page_id = $page->id;
		            		$link->link_id = $out->id;
	            			$link->anchor = '';
		            	}

	            		$link->save();
		            }

	        	}
	        }

	        elseif ($text) {

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

		            $href = $this->normalizeUrl($href, $page->url);

		            if ($href === false) {
		            	print "NO URL -> next\n";
		            	continue;
		            }


		            if ($out = CrawlerPage::model()->findByAttributes(array('url' => $href))) {
		            	/*print "ID = " . $out->id . " " . $href . "\n";*/
		            }
		            else {

				    	$out = new CrawlerPage;
				    	$out->url = $href;

				    	if (!$this->isChildDomain($href, $model->url())) {
				    		print $href . " NOT CHILD" . PHP_EOL;
				    		$out->check = 1;
				    	}

				    	if ($out->save()) {
		            		print "NEW = " . $out->id . " " . $href . "\n";
		            	}
		            	else {
		            		print "NEW ERROR !!! " . $href . " \n";
		            	}

		            }

		            if ($out->id) {
		            	if ($link = CrawlerLinks::model()->findByAttributes(array('page_id' => $page->id, 'link_id' => $out->id))) {
		            		/*print "LINK = " . $link->id . "\n";*/
		            	}
		            	else {
		            		$link = new CrawlerLinks;
		            		$link->page_id = $page->id;
		            		$link->link_id = $out->id;
	            			$link->anchor = $anchor;

		            		/*print "LINK = NEW \n";*/
		            	}

	            		$link->save();
		            }

		        }

	        }

	        $page->check = 1;
	        $page->save();

    		print "page end\n";

    		sleep( 1 );
    	}

    	print "done\n";

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

    public function actionYandexStruct($url) {

    	print $url . "\n\n";

    	$domain = Domain::model()->findByAttributes(array('domain' => $url));

    	if (!$domain or !$domain->id) {
    		print 'Domain not found' . PHP_EOL;
    	}

    	$project = Project::model()->findByAttributes(array('domain_id' => $domain->id));

    	if (!$project or !$project->id) {
    		print 'Project not found' . PHP_EOL;
    	}

    	print 'Domain ' . $domain->id.' Project ' . $project->id . PHP_EOL;

    	$total = 0;
        $host = $domain->host();

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

        $word = 'url:' . $url . '*';

        print $word . PHP_EOL;

        $check = new YandexStructureCheck;
        $check->domain_id = $domain->id;
        $check->save();

        $REGION_ID = (int) $project->regions[0];

        for ($page = 0; $page < 100000000; $page++) {
	        $xml = $YAXML->getXML($word, $REGION_ID, 100, $page);

	        $page_total = 0;

            if ($results = YandexXMLResult::parse($xml)) {
                foreach($results->list as $doc){
                	$page_total++;

                    if ($doc->domain == $host) {
	                    $total++;

	                    $ypage = new YandexStructure;
	                    $ypage->domain_id = $domain->id;
	                    $ypage->check_id = $check->id;
	                    $ypage->title = $doc->title;
	                    $ypage->url = $doc->url;
	                    $ypage->save();

	                    print $total . '   ' . $doc->url . PHP_EOL;
	                }
                }
            }

            if ($page_total == 0) {
            	break;
            }
        }

        print 'total: ' . $total . PHP_EOL;
    }




    public function actionDomainSitemap( $host ) {

    	$text = file_get_contents('https://mir7ek.ru/sitemap.1477059.xml.gz');

    	if (strpos($text, '<?xml') === false) {
    		$ungz = @gzdecode($text);
    		var_dump( $ungz );
    	}

    	die();


        $sitemaps = array();
        $sitemaps[] = 'http://' . $host . '/sitemap.xml'; 
        $robots = Robots::model()->last( $model );

            $context = stream_context_create(
                array(
                    'http' => array(
                        'follow_location' => false
                    )
                )
            );

            for ($j = 0; $j < count($sitemaps); $j++) {
            	$sitemap_url = $sitemaps[$j];

                $text = @file_get_contents($sitemap_url, false, $context);

                if ($text) {

		            if ($data = $text) {
		                $exists = true;

		                $a = array();
		                $index = array();

		                $p = xml_parser_create();
		                xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
		                $xml_result = xml_parse_into_struct($p, $data, $a, $index);
		                xml_parser_free($p);

		                if ($xml_result) {
		                	if ($index['SITEMAPINDEX']) {
			                	foreach ($index['LOC'] as $i) {
			                		$sitemaps[] = $a[$i]['value'];
			                	}
		                	}
		                	else {
			                	foreach ($index['LOC'] as $i) {
		                            $names[] = $a[$i]['value'];
			                	}
		                	}
		                }


		                /*$urlset = false;

		                $n = 1;

		                foreach ($a as $i) {    
		                    switch (strtolower($i['tag'])) {
		                        case 'loc':
		                            break;
		                    }
		                }*/
		            }

		            /*print_r($names);
		            die();


                	print $text;die();

                    $data = new DomainsSitemap;
                    $data->domain_id = $model->id;
                    $data->url = $sitemap_url;
                    $data->robots = intval($in_robots or $sitemap_url != $default_sitemap);
                    $data->text = $text;
                    $data->save();*/
                }

	            print $sitemap_url . PHP_EOL;
            }

            /*$sm = new SitemapDownloader( $sitemaps );
            if ($load_result = $sm->load()) {
                $sm->save( $model->id );
            }*/

        print_r($names);

        echo PHP_EOL;

		return true;
    }

    public function actionTable( ) {
        $s = Sitemap::model()->findByPk( 135179 );

    	print '#' . $s->id . "\n";
        $s->checkStatus();

        print "done\n";
    }

    public function actionWordx( $filename ) {

    	if (!file_exists($filename)) {
    		print "file does not exists\n"; 
    		return false;
    	}

    	$text = file_get_contents($filename);

    	if ($text) {
            $text = str_replace(array(',', '+', ':', ';'), ' ', $text);

            $mystem = new Mystem( $text );
            $mystem->checkZ();

            $result = array();
            $words = array();
            $words_g = array();

            $w_prev = $w_next = array();

            $prev = '';
            $prev_word = false;
            $this_word = array();

            $prev_wform = false;
            $this_wform = array();

            $text_id = time();

            $n = 0;

            $m = count($mystem->result);

            print 'Total mystem result: ' . $m . "\n";

            foreach ($mystem->result as $chunk) {

            	print "Try " . $n . "/". $m ." " . $chunk->text . " (".$chunk->word.", ".$chunk->gr.") \n";

            	$prev_word = $this_word;
	            $this_word = array();

	            $prev_wform = $this_wform;
	            $this_wform = array();

            	if (is_array($chunk->analysis)) {
            		foreach ($chunk->analysis as $el) {
            			$word = WordxWord::model()->findByAttributes(array('word' => $el->lex));

            			if (!$word) {
            				$transaction = Yii::app()->db->beginTransaction();

            				$word = new WordxWord;
            				$word->word = $el->lex;
            				$word->save();

            				$transaction->commit();
            			}

            			$gram_all = array();
            			list($gr_a, $gr_b) = explode('=', $el->gr, 2);

            			if (substr($gr_b, 0, 1) == '(' and substr($gr_b, -1) ==')') {
            				$gr_c = explode('|', substr($gr_b, 1, -1));

            				foreach ($gr_c as $gr_d) {
            					$gram_all[] = $gr_a . '=' . $gr_d;
            				}
            			}
            			else {
            				$gram_all[] = $gr_a . '=' . $gr_b;
            			}

            			foreach ($gram_all as $q) {
	            			$gram = WordxGram::model()->findByAttributes(array('gram' => $q));

	            			if (!$gram) {
	            				$transaction = Yii::app()->db->beginTransaction();

	            				$gram = new WordxGram;
	            				$gram->gram = $q;
	            				$gram->save();

	            				$transaction->commit();
	            			}

	            			$wform = WordxForm::model()->findByAttributes(array('word_id' => $word->id, 'gram_id' => $gram->id));

	            			if (!$wform) {
	            				$transaction = Yii::app()->db->beginTransaction();

	            				$wform = new WordxForm;
	            				$wform->word_id = $word->id;
	            				$wform->gram_id = $gram->id;
	            				$wform->word = $chunk->text;
	            				$wform->save();

	            				$transaction->commit();
	            			}

	            			$this_wform[] = $wform;
            			}

            			$gram = WordxGram::model()->findByAttributes(array('gram' => $gram_all[0]));
	            		$wform = WordxForm::model()->findByAttributes(array('word_id' => $word->id, 'gram_id' => $gram->id));

            			$this_word[] = $word;
            		}
            	}

            	foreach ($prev_word as $j) {
            		foreach ($this_word as $i) {
        				$transaction = Yii::app()->db->beginTransaction();

            			if ($pos = WordxPos::model()->findByAttributes(array('first_id' => $j->id, 'second_id' => $i->id))) {
            				$pos->total++;
            				$pos->save();
            			}
            			else {
            				$pos = new WordxPos;
            				$pos->first_id = $j->id;
            				$pos->second_id = $i->id;
            				$pos->total = 1;
            				$pos->save();
            			}

        				$transaction->commit();
            		}
            	}

            	foreach ($prev_wform as $j) {
            		foreach ($this_wform as $i) {
        				$transaction = Yii::app()->db->beginTransaction();

        				$pos = new WordxChain;
        				$pos->text_id = $text_id;
        				$pos->prev_id = $n;
        				$pos->word1 = $j->id;
        				$pos->gram1 = $j->gram_id;
        				$pos->word2 = $i->id;
        				$pos->gram2 = $i->gram_id;
        				$pos->save();

        				$transaction->commit();
            		}
            	}

            	$n++;

            	$result[ $chunk->word ]++;
            	$grammar[ $chunk->word ] = $chunk->gr;

                $words[] = mb_strtolower( $chunk->word, 'utf-8' );
                $words_n[] = mb_strtolower( $chunk->text, 'utf-8' );
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

            print "done\n";

    	}


    }

    public function actionYandexXML() {

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

        $xml = $YAXML->getXML('любое слово культра', NULL, 100, 0);

        print $xml;
    }


    public function actionReport($id){
        $this->project = $project = $model = Project::model()->findByPk($id);
    	$xml_data = array();

    	$xml_data['model'] = $project;

    	$whois = $project->domain->whois_history[0];
        $params = array();

    	if ($whois) {
    		if (is_array($whois->params))
		        foreach ($whois->params as $p) {
		            $params[$p->name] = $p;
		        }
    	}

    	$xml_data['whois'] = 
            array(
                'domain' => $project->domain,
                'data' => $whois->items, 
                'params' => $params
            );


    	$scr = ProjectsScreenshot::model()->findByAttributes(
    		array('domain_id' => $model->domain_id),
    		array('order' => 'id desc')
    	);

    	$xml_data['screen'] = $scr;
    	list($w, $h) = getimagesize( Yii::app()->basePath . '/..' . $scr->image );

    	if ($w > 0 and $h > 0) {
			$xml_data['screen_size'] = array(
				'width' => round($w / $h * 4600000),
				'height' => 4600000,
			);
    	}

    	$data = $project->domain->ip;

    	if (is_array($data)) {
    		$data = $data[0];
    	}

    	$xml_data['server_ip'] = $data->ip;

    	$data = $project->domain->headers;

    	if (is_array($data)) {
    		$data = $data[0];
    	}

    	$xml_data['headers'] = $data;

    	$xml_data['robots'] = Robots::model()->findByAttributes(array('domain_id' => $project->domain->id));

        $xml_data['sitemap'] = DomainsSitemap::model()->findByAttributes(array('domain_id' => $project->domain->id), array('order' => 'id desc'));

        $cms = new CMSCheck( $project->domain->url() );
        $xml_data['cms'] = $cms->checkAll();

        $xml_data['error404'] = 0;

    	$pages404 = array();
    	foreach (Sitemap::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'status' => 404)) as $p) {
    		$pages404[] = $p->url;
    	}
    	foreach (CrawlerPage::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'code' => 404)) as $p) {
    		$pages404[] = $p->url;
    	}
    	sort($pages404);

    	$xml_data['pages404'] = array();

    	if (count($pages404)) {
    		$xml_data['error404'] = 1;
	    	$xml_data['pages404'][] = $pages404[0];

    		for ($j = 1; $j < count($pages404)-1; $j++) {
    			if ($pages404[$j] != $pages404[$j-1]) {
    				$xml_data['error404']++;
			    	$xml_data['pages404'][] = $pages404[$j];
    			}
    		}
    	}

    	$xml_data['redirect'] = 0;
    	$xml_data['pages301'] = array();

    	$pages = array();

        $criteria = new CDbCriteria;
        $criteria->params = array( 'id' => $model->domain_id );

        $criteria->condition = 'domain_id = :id and status >= 300 and status < 400';

    	foreach (Sitemap::model()->findAll($criteria) as $p) {
    		$pages[] = $p->url;
    	}

        $criteria->condition = 'domain_id = :id and code >= 300 and code < 400';

    	foreach (CrawlerPage::model()->findAll($criteria) as $p) {
    		$pages[] = $p->url;
    	}

    	sort($pages);

    	if (count($pages)) {
    		$xml_data['redirect'] = 1;
    		$xml_data['pages301'][] = $pages[0];

    		for ($j = 1; $j < count($pages)-1; $j++) {
    			if ($pages[$j] != $pages[$j-1]) {
    				$xml_data['redirect']++;
		    		$xml_data['pages301'][] = $pages[$j];
    			}
    		}
    	}

    	$xml_data['counters'] = array();
        foreach (Counter::model()->findAllByAttributes(array('domain_id' => $project->domain->id), array('order' => '`date` desc')) as $c) {
        	if (!$xml_data['counters'][ $c->name ] and $c->value) {
        		$xml_data['counters'][ $c->name ] = $c;
        	}
        }

        /**
        Page speed
        */

    	$xml_data['timer'] = array(
			'namelookup' => false, 
			'connect' => false,
			'pretransfer' => false,
			'starttransfer' => false,
			'total' => false
    	);

    	foreach ($xml_data['timer'] as $j => $i) {
    		$xml_data['timer'][$j] = DomainsResult::model()->findByAttributes(
				array(
					'domain_id' => $model->domain_id, 
					'name' => 'time_' . $j
				), array('order' => 'id desc')
			);
    	}

    	$result = DomainsResult::model()->findByAttributes(
			array(
				'domain_id' => $model->domain_id, 
				'name' => 'pagespeed_desktop'
			), array('order' => 'id desc')
		);

		if ($result) {
			$data = json_decode($result->value);
			$data->score = $data->ruleGroups->SPEED->score;

			$rules = array();

			if (is_array($data->formattedResults->ruleResults))
				foreach ($data->formattedResults->ruleResults as $j => $r) {
					$r->name = $j;
					$rules[] = $r;
				}

			usort($rules, function($a, $b){
				if ($a->ruleImpact == $b->ruleImpact) return 0;
				return ($a->ruleImpact < $b->ruleImpact) ? 1 : -1;
			});

			$data->rules = $rules;

			$xml_data['speed_desktop'] = $data;
		}

		$result = DomainsResult::model()->findByAttributes(
			array(
				'domain_id' => $model->domain_id, 
				'name' => 'pagespeed_mobile'
			), array('order' => 'id desc')
		);

		if ($result) {
			$data = json_decode($result->value);
			$data->score = $data->ruleGroups->SPEED->score;

			if (is_array($data->ruleGroups))
				foreach ($data->ruleGroups as $j) {
					$data->score = min($data->score, $j->score);
				}

			$rules = array();

			if (is_array($data->formattedResults->ruleResults))
				foreach ($data->formattedResults->ruleResults as $j => $r) {
					$r->name = $j;
					$rules[] = $r;
				}

			usort($rules, function($a, $b){
				if ($a->ruleImpact == $b->ruleImpact) return 0;
				return ($a->ruleImpact < $b->ruleImpact) ? 1 : -1;
			});

			$data->rules = $rules;

			$xml_data['speed_mobile'] = $data;
		}

		print "stage 0 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/**

		1. Внутренняя оптимизация

		**/

		/* 1.1 Анализ домена */

		/* 1.2 Анализ хостинга */

		/* 1.3 Анализ серверных настроек */

		/* 1.4 Система управления сайтом */

		/* 1.5 Структура сайта */

		/* 1.6 404-ошибки */

		/* 1.7 301-редиректы */

		/* 1.8 Посещаемость */

		/* 1.9 Скорость загрузки сайта */

		/* 1.10 Проверка сайта через  Google  Pagespeed Insights */

		/* 1.11 Мета-теги на страницах сайта */
		/* -> 2.7 */

		/**

		2. Контент

		**/

		/* 2.1 Уникальность текстов */
		/* -> 2.7 */

		/* 2.2 Уникальность картинок на сайте */

		$xml_data['images_count'] = DomainsResource::model()->countByAttributes(
			array(
            	'domain_id' => $model->domain_id,
            	'type' => DomainsResource::T_IMAGE,
        	)
        );

		$xml_data['images'] = DomainsResource::model()->findAllByAttributes(
			array(
            	'domain_id' => $model->domain_id,
            	'type' => DomainsResource::T_IMAGE,
        	)
		);

		print "stage 2.2 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 2.3 Проверка орфографии */
		/* -> 2.7 */

		/* 2.4 Контактные данные на сайте */

        $xml_data['company'] = new StdClass;
        $xml_data['company']->inn = array();
        $xml_data['company']->ogrn = array();

        /*foreach ($model->domain->sitemap as $sm) {
            $page = $sm->page;

            if ($page and is_array($page->params)) {
                foreach ($page->params as $param) {
                    if ($param->name == 'inn' or $param->name == 'ogrn') {
                        $name = $param->name;

                        if (!in_array($param->value, $xml_data['company']->{$name})) {
                            $xml_data['company']->{$name}[] = $param->value;
                        }
                    }
                }
            }
        }

        $tel_criteria = new CDbCriteria;
        $tel_criteria->alias = 't';
        $tel_criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $tel_criteria->condition = 'p.domain_id = :id and t.href like :tel';
        $tel_criteria->group = 't.href';
        $tel_criteria->params = array(
        	'id' => $model->domain_id,
        	'tel' => 'tel:%'
        );

        $phone_criteria = new CDbCriteria;
        $phone_criteria->alias = 't';
        $phone_criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $phone_criteria->condition = 'p.domain_id = :id and t.name like :name';
        $phone_criteria->group = 't.value';
        $phone_criteria->params = array(
        	'id' => $model->domain_id,
        	'name' => 'phone'
        );

        $mail_criteria = new CDbCriteria;
        $mail_criteria->alias = 't';
        $mail_criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $mail_criteria->condition = 'p.domain_id = :id and t.href like :href';
        $mail_criteria->group = 't.href';
        $mail_criteria->params = array(
        	'id' => $model->domain_id,
        	'href' => 'mailto:%'
        );

        $mails = array();
        foreach (PagesLink::model()->findAll($mail_criteria) as $link) {
        	list($j, $i) = explode('?', substr($link->href, 7), 2);

        	if (!isset( $mails[ strtolower($j) ] )) {
        		$mails[ strtolower($j) ] = $j;
        	}
        }

        $tel = PagesLink::model()->findAll( $tel_criteria );
        $phone = PagesParam::model()->findAll( $phone_criteria );

    	if (count($tel) + count($phone) > 0) {
    		$tel_total = array();

    		if (is_array($tel))
				foreach ($tel as $el) {
	    			if (!in_array($el->href, $tel)) {
	    				$tel_total[] = $el->href;
	    			}
	    		}

	    	if (is_array($phone)) {
	    		foreach ($phone as $el) {
	    			if (!in_array($el->value, $tel)) {
	    				$tel_total[] = $el->value;
	    			}
	    		}
	    	}
    	}*/

        $xml_data['company']->tel = $tel_total;
        $xml_data['company']->mail = $mails;

		print "stage 2.4 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 2.5 Проверка конфиденциальности и Cookies */

        $criteria = new CDbCriteria;
        $criteria->condition = '`domain_id` like :id and (`text` like :polit or `text` like :personal)';
        $criteria->params = array(
        	'id' => $model->domain_id,
        	'polit' => '%Политика конфиденциальности%',
        	'personal' => '%Персональных данных%',
        );
        $criteria->limit = 101;

        $xml_data['private'] = Page::model()->findAll( $criteria );

        $headers = array();
        if ($model->domain->headers[0]) {
        	$xml_data['cookies'] = $model->domain->headers[0]->getCookie();
        }

		print "stage 2.5 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 2.6 Наличие сомнительного контента */

		$page_ids = Yii::app()->db->createCommand()
		    ->select('distinct(p.page_id)')
		    ->from('tbl_pages_phrase as p left join tbl_vocab_stop as v on (p.phrase = v.word)')
		    ->where('p.domain_id = :id and !(v.word is null)', array(':id' => $model->domain_id))
		    ->queryColumn();

		$xml_data['porno'] = array();

		if (is_array($page_ids) and count($page_ids)) {
	        $criteria = new CDbCriteria;
	        $criteria->addInCondition('id', $page_ids);
	        $xml_data['porno'] = Page::model()->findAll( $criteria );
		}

		print "stage 2.6 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 2.7 Наличие видео на сайте */
		/* 2.8 Наличие файлов для скачивания */

		$xml_data['total_pages'] = 0;

        $xml_data['meta'] =
        $xml_data['spell'] = 
		$xml_data['video'] = 
		$xml_data['files'] = array();

		$uniq_sum = 0;
		$uniq_total = 0;

        foreach ($model->domain->sitemap as $sm) {
            if ($p = $sm->page) {
            	$xml_data['total_pages']++;

            	$has_meta = array();
	            $meta = $p->meta();
	            $htag = $p->hTag();

	            if ($sm->title) {
	            	$has_meta['title']++;
	            }

	            /*if (is_array($meta))
		            foreach ($meta as $i) {
		            	$has_meta[ $i->name ]++;
		            }

		        if (is_array($htag))
		            foreach ($htag as $i) {
		            	$has_meta[ $i->name ]++;
		            }

		        if (is_array($has_meta))
		            foreach ($has_meta as $j => $i) {
		            	if ($i) {
		            		$xml_data['meta'][$j]++;
		            	}
		            }

	            /*$files = $p->linkFiles();
	            if (is_array($files))
		            foreach ($files as $link) {
		            	$xml_data['files'][ md5($link->href) ] = $link->href;
		            }

	            $video = $p->params('video');
	            if (is_array($video))
		            foreach ($video as $param) {
		            	$xml_data['video'][ md5($param->value) ] = $param->value;
		            }

	        	$spell = $p->spell;
				if ($spell and $spell->hasErrors()) {
					$xml_data['spell'] = (object) array(
						'url' => $item->url,
						'page' => $item->page,
						'short' => $spell->shortText(),
					);
				}*/

				if ($p->uniq >= 0) {
					$uniq_sum += $p->uniq;
					$uniq_total++;
				}
            }

        }

        $xml_data['uniq'] = $uniq_total ? round($uniq_sum / $uniq_total) : -1;

		print "stage 2.8 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/**

		3. Внешние ссылки

		**/

		/* 3.1 Адаптивность сайта */

        if ($page = $model->domain->sitemap[0]->page) {
            if ($viewport = $page->param('meta-viewport')) {
                $parsed = new Viewport($viewport->value);
            }
        }

        $xml_data['viewport_param'] = $viewport;
        $xml_data['viewport_parsed'] = $parsed;

		/* 3.2 Мобильная версия сайта */

        $useragent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25';

        $context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false,
                    'header' => "User-Agent: " . $useragent . "\r\n"
                )
            )
        );

        $data = @file_get_contents($model->domain->url(), false, $context);

        if (is_array($http_response_header)) {
	        foreach ($http_response_header as $line) {
	            list($a, $b) = explode(':', $line, 2);

	            if (trim($a) == 'Location') {
	                $mobile = trim($b);
	            }
	        }
        }

        $xml_data['mobile'] = $mobile;

		print "stage 3.2 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 3.3 Разрешения экранов */

		/* 3.4 Проверка в популярных браузерах */

		/* 3.5 Содержание «шапки» сайта */

		/* 3.6 Содержание «подвала»  сайта */

		/* 3.7 Использование сторонних шрифтов */

        $data = new DataModel( $model );
        $result = $data->usabilityFonts();
        $xml_data['fonts'] = $result['fonts'];

		/* 3.8 Анализ каскадных файлов стилей (CSS) */

		$xml_data['css'] = DomainsResource::model()->findAllByAttributes(array(
			'domain_id' => $model->domain_id, 
			'type' => DomainsResource::T_CSS
		));

		print "stage 3.8 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 3.9 Наличие  JavaScript */

		$xml_data['js'] = DomainsResource::model()->findAllByAttributes(array(
			'domain_id' => $model->domain_id, 
			'type' => DomainsResource::T_SCRIPT
		));

		print "stage 3.9 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 3.10 Системы отзывов на сайте */

        $codes = array(
	        'cackle.me' => 'cackle.me',
	        'disqus.com' => 'disqus.com',
	        'bazaarvoice.com' => 'bazaarvoice.com',
	        'mneniya.pro' => 'mneniya.pro',
	        'shoppilot.ru' => 'shoppilot.ru',
	        'commentbook.ru' => 'commentbook.ru',

	        'roistat.com' => 'roistat.com'
        );

        $founded = array();

        foreach ($codes as $j => $i) {
	        $criteria = new CDbCriteria;
	        $criteria->condition = 'domain_id = :id and type = :type and html like :code';
	        $criteria->params = array(
	        	'id' => $model->domain_id,
	        	'type' => DomainsResource::T_SCRIPT,
	        	'code' => '%' . $i . '%'
	        );

	        $result = DomainsResource::model()->findAll( $criteria );

	        if ($result and count($result)) {
	        	$founded[$j] = $result;
	        }
        }

        $result = array();
        foreach (DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_SCRIPT)) as $res) {
        	$code = str_replace("'+'", "", $res->html);

        	if (strpos($code, 'livetex.ru') !== false) {
        		$result[] = $res;
        	}
        }

        if ($result and count($result)) {
	        $founded['livetex.ru'] = $result;
        }

        $xml_data['references'] = $founded;

		print "stage 3.10 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 3.11 Он-лайн консультанты */

		/* 3.12 Цели  Яндекс.Метрики */

        $criteria = new CDbCriteria;
        $criteria->alias = 't';
        $criteria->join = 'left join tbl_pages as p on (t.page_id = p.id)';
        $criteria->condition = 'p.domain_id = :id and t.name like :name';
        $criteria->group = 't.value';
        $criteria->params = array(
        	'id' => $model->domain_id,
        	'name' => 'yandex-goal'
        );

    	$xml_data['goals'] = PagesParam::model()->findAll( $criteria );

		/* 3.13 Формы на сайте */

		$xml_data['forms'] = DomainsResource::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'type' => DomainsResource::T_FORM));

		print "stage 3.13 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/**

		4. Внешние ссылки

		**/

		/* 4.1 Входящие ссылки */

		/* 4.2 Исходящие ссылки */

        $xml_data['outlink'] = array();
        $xml_data['outlink_total'] = 0;

        foreach ($model->domain->sitemap as $sm) {
            $page = $sm->page;

            if ($page) {
	            $links = $page->linkOut();

	            if ($links and is_array($links)) {
	                $xml_data['outlink_total'] += count( $links );

		            foreach ($links as $l) {
		            	$xml_data['outlink'][ md5( $l->href ) ] = $l->href;
		            }
	            }
            }
        }

		print "stage 4.2 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/* 4.3 Упоминания домена */

		$xml_data['mention'] = MentionCheck::model()->findByAttributes(array('domain_id' => $model->domain_id), array('order' => 'id desc'));

		/* 4.4 Социальные сети */

        $xml_data['social'] = array();
        foreach ($model->domain->sitemap as $sm) {
            $page = $sm->page;

            if ($page) {
            	if (is_array($page->linkOut()))
		            foreach ($page->linkOut() as $link) {
		            	if (substr($link->href, 0, 2) == '//') {
		            		$link->href = 'http:' . $link->href;
		            	}

		            	$a = parse_url($link->href);

		            	$host = implode('.', array_slice(explode('.', $a['host']), -2) );

		            	if (!is_array($xml_data['social'][ $host ]) or !in_array($link->href, $xml_data['social'][ $host ])) {
			            	$xml_data['social'][ $host ][] = $link->href;
		            	}
		            }
            }
        }

		/* 4.5 Формальные признаки сайта */

		$xml_data['tic'] = DomainsResult::model()->findByAttributes(
			array('domain_id' => $model->domain_id, 'name' => 'yandex_tic'), 
			array('order' => 'id desc')
		);

		print "stage 4.5 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

		/**

		5. Безопасность

		**/

		/* 5.1 HTTPS */

		$xml_data['ssl'] = DomainsResult::model()->findByAttributes(array(
        	'domain_id' => $model->domain_id,
        	'name' => 'ssl'
        ));

        /* 5.2 Virus */

        /* 5.3 Clickjacking */

        /* 5.4 Open Dir */

        /* 5.5 Ошибки */

		print "stage 5.5 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

        /**

        Приложение 1. Структура сайта

        **/

    	$xml_data['struct'] = array(
    		'sitemap' => array(),
    		'crawler' => array(),
    		'yandex' => array(),
    	);

    	foreach (Sitemap::model()->findAllByAttributes(array('domain_id' => $model->domain->id), array('order' => 'url asc')) as $el) {
    		$xml_data['struct']['sitemap'][] = $el->url;
    	}

    	foreach (CrawlerPage::model()->findAllByAttributes(array('domain_id' => $model->domain_id), array('order' => 'url asc')) as $el) {
    		$xml_data['struct']['crawler'][] = $el->url;
    	}

    	$check = YandexStructureCheck::model()->findByAttributes(array('domain_id' => $model->domain_id), array('order' => 'id desc'));
    	foreach (YandexStructure::model()->findAllByAttributes(array('check_id' => $check->id), array('order' => 'url asc')) as $el) {
    		$xml_data['struct']['yandex'][] = $el->url;
    	}

		print "stage ex1 ok" . (" " . ceil(memory_get_usage() / 1024 / 1024) . "Mb") . PHP_EOL; 

        /**

		Вывод документа

        **/

		print "Report path: " . Yii::app()->params['report']['path'] . '/' . PHP_EOL; 

		die();

        $xml = $this->renderPartial('//report/docx', $xml_data, true);
        $xml = str_replace(array("\t", "\n"), array("", " "), $xml);

        $dir = Yii::app()->params['report']['path'] . '/';

        $report_name = $model->id . '-' . date('Ymd');

        $result = @shell_exec('cp -r ' . $dir . 'default ' . $dir . $report_name );

    	if ($scr and $scr->image) {
        	$result = @shell_exec('cp ' . Yii::app()->basePath . '/..' . $scr->image . ' ' . $dir . $report_name . '/word/media/image1.png');
    	}

        file_put_contents( $dir . $report_name . '/word/document.xml', $xml);

        @shell_exec('cd ' . $dir . $report_name . ' && zip -r ../' . $report_name . '.docx *');
        @shell_exec('rm -r ' . $dir . $report_name);

		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"report-" . str_replace('.', '_', $model->domain->host()) . "-" . date('Ymd') . '.docx' . "\""); 

		readfile( $dir . $report_name . '.docx');

    	Yii::app()->end();
    }


}









