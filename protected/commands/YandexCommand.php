<?php

class YandexCommand extends CConsoleCommand {

    public function actionPositions( $project_id = 0 ) {

        $total = 0;
        $found = 0;
        $top10 = 0;

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

        if ($project_id) {
            $words = Semantic::model()->findAllByAttributes(array( 'project_id' => $project_id ));
        }
        else {
            $words = Semantic::model()->findAll();
        }

        foreach ($words as $word) {
            $total++;

            print $word->phrase . ' - ' ;

            if (!$word->project or !$word->project->id) {
                print 'NO HOST' . PHP_EOL;
                continue;
            }

            $REGION_ID = (int) $word->project->regions[0];
            $POSITION = false;
            $host = $SITE_DOMAIN = $word->project->domain->host();

            print $host . ' - ';

            $xml = $YAXML->getXML($word->phrase, $REGION_ID, 100);

            if ($results = YandexXMLResult::parse($xml)) {
                $foundPhrase = (int)$results->foundDocsPhrase;
                $foundAll = (int)$results->foundDocsAll;
                $foundStrict = (int)$results->foundDocsStrict;

                $foundedPosition = false;

                foreach($results->list as $doc){
                    if ($doc->domain == $host) {
                        $found++;

                        if ($doc->position <= 10) {
                            $top10++;
                        }

                        $ypos = new YandexPosition;
                        $ypos->semantic_id = $word->id;
                        $ypos->position = $doc->position;
                        $ypos->title = $doc->title;
                        $ypos->url = $doc->url;
                        $ypos->save();

                        print $doc->position;

                        break;
                    }
                }
            }

            print PHP_EOL;
        }

        print 'total: ' . $total . ', found: ' . $found . ', top10: ' . $top10 . PHP_EOL;
    }

    public function actionQuery() {
        die();
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");
        $maxpage = 10;
        foreach (Project::model()->findAll() as $project) {
            if (date('Y-m-d', strtotime($project->lastcheck)) != date('Y-m-d')) {
                $transaction = Yii::app()->db->beginTransaction();

                $hi = new History();
                $hi->check(basename($project->host));
                $hl = History::model()->findAllByAttributes(array('host' => $hi->host));
                $hi->timestamp = date("Y-m-d", time());
                if (!$hi->save()) {
                    var_dump($hi->errors);
                }

                foreach (explode(',', $project->keywords) as $keyword) {
                    foreach ($project->regions as $lr) {
                        $pos = 1;
                        for ($page = 0; $page < $maxpage; $page++) {
                            $query = "http://xmlsearch.yandex.ru/xmlsearch?user=xsite&key=03.37624:68dcfd904d9cac84ac2e25bf79b104af&lr=$lr&page=$page&query=" . urlencode($keyword);
                            $response = file_get_contents($query);
                            $exist = false;
                            $dom = new DOMDocument();
                            $dom->loadXML($response);
                            foreach ($dom->getElementsByTagName("error") as $error) {
                                $error = simplexml_import_dom($error);
                                mail("kirshin.as@gmail.com", "Ошибка поиска по позициям", (string) $error);
                                echo $error . "\n";
                            }
                            foreach ($dom->getElementsByTagName('group') as $groupDom) {
                                $group = simplexml_import_dom($groupDom);
                                $q = Query::model()->findByAttributes(array('url' => strtolower($group->doc->url), 'date' => date('Y-m-d'), 'keyword' => $keyword, 'region' => $lr));
                                if (!isset($q)) {
                                    $query = new Query();
                                    $query->date = date('Y-m-d');
                                    $query->created = date('Y-m-d H:i:s');
                                    $query->keyword = $keyword;
                                    $query->domain = str_replace("www.", "", basename(strtolower($group->doc->domain)));
                                    $query->url = strtolower($group->doc->url);
                                    $query->title = $group->doc->title;
                                    $query->description = $group->doc->headline;
                                    $query->position = $pos;
                                    $query->region = $lr;
                                    $query->xml = $dom->saveXML($groupDom);
                                    if (!$query->save()) {
                                        var_dump($query->errors);
                                    }
                                    if ($query->domain == $project->domain) {
                                        $exist = true;
                                    }
                                }
                                $pos++;
                            }
                            if ($exist) {
                                break;
                            }
                        }
                    }
                }
                $project->lastcheck = date('Y-m-d');
                $project->save();
                $transaction->commit();
            }
        }
        echo "Запрос выполнен.\r\n";
    }

    public function actionDomainsCheck() {
        die();
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");
        $criteria = new CDbCriteria();
        $criteria->select = "domain";
        $criteria->condition = "position<=10";
        $criteria->distinct = true;
        foreach (Query::model()->findAll($criteria) as $q) {
            echo $q->domain . ":";
            if ($q->domain == "probirka.kz")
                continue;
            $hcriteria = new CDbCriteria();
            $hcriteria->condition = "host=:host";
            $hcriteria->params = array("host" => $q->domain);
            $h = History::model()->find($hcriteria);
            if (!isset($h)) {
                $h = new History();
                $h->check(basename($q->domain));
                $h->timestamp = date("Y-m-d", time());
                try {
                    if (!$h->save()) {
                        var_dump($h->errors);
                    }
                } catch (Exception $ex) {
                    
                }
                sleep(5);
            }
            echo $h->tic . "\n";
        }
        echo "Запрос выполнен.\r\n";
    }

    public function actionWordstat() {
        die();
        Yii::import("root.admin.protected.modules.seo.components.*");
        Yii::import("root.admin.protected.modules.seo.models.*");
        foreach (Project::model()->findAll() as $project) {
            $regions = array();
            foreach ($project->regions as $regionId) {
                $regions[$regionId] = Region::getByPk($regionId);
            }
            foreach (explode(',', $project->keywords) as $keyword) {
                foreach ($project->regions as $lr) {
                    $wordstat = Wordstat::model()->findByAttributes(array("date" => date("Y-m-d"), "keyword" => $keyword, "regions" => $lr));
                    if (!isset($wordstat)) {
                        $qregions = array($lr => $regions[$lr]);
                        $result = SeoUtils::getWordStat($keyword, $qregions);
                        $wordstat = new Wordstat();
                        $wordstat->date = date("Y-m-d");
                        $wordstat->keyword = $keyword;
                        $wordstat->regions = implode(",", array_keys($qregions));
                        $wordstat->result = $result;
                        $wordstat->save();
                        sleep(10);
                    }
                }
            }
        }
        echo "Запрос выполнен.\r\n";
    }
    
    public function actionDocx( $project_id = 0 ) {

    	$project = Project::model()->findByPk($project_id);

    	if (!$project or !$project->id) {
    		print 'Project not set.';
    		return false;
    	}

		$phpWord = new \PhpOffice\PhpWord\PhpWord();

		$phpWord->addFontStyle(
		    'cssH1',
		    array('name' => 'Tahoma', 'size' => 22, 'color' => '2e74b5', 'bold' => false)
		);

		$phpWord->addFontStyle(
		    'cssH2',
		    array('name' => 'Tahoma', 'size' => 18, 'color' => '2e74b5', 'bold' => false)
		);

		$phpWord->addFontStyle(
		    'cssH3',
		    array('name' => 'Tahoma', 'size' => 14, 'color' => '2e74b5', 'bold' => false)
		);

		$phpWord->addFontStyle(
		    'cssP',
		    array('name' => 'Tahoma', 'size' => 11, 'color' => '000000', 'bold' => false)
		);

		$phpWord->addFontStyle(
		    'cssPB',
		    array('name' => 'Tahoma', 'size' => 11, 'color' => '000000', 'bold' => true)
		);

		$phpWord->addFontStyle(
		    'smallP',
		    array('name' => 'Tahoma', 'size' => 9, 'color' => '000000', 'bold' => false)
		);

		$phpWord->addFontStyle(
		    'smallPB',
		    array('name' => 'Tahoma', 'size' => 9, 'color' => '000000', 'bold' => true)
		);

		$section = $phpWord->addSection();

		$section->addText(
		    $project->name . ' ' . $project->domain->host(),
		    'cssH1'
		);

		$section->addText(
		    'Внутренняя оптимизация',
		    'cssH2'
		);

		$section->addText(
		    'Домен',
		    'cssH3'
		);

		$cc = new CCronController('docx');

    	$whois = $project->domain->whois_history[0];
        $params = array();

    	if ($whois) {
	        foreach ($whois->params as $p) {
	            $params[$p->name] = $p;
	        }
    	}

		$text = $cc->renderInternal(__DIR__ . '/../views/report/domain.php', array(
            'domain' => $project->domain,
            'data' => $whois->items, 
            'params' => $params
		), true);

		$section->addText($text, 'cssP');

		if ($whois->items and count($whois->items)) {
			$table = $section->addTable();
			foreach ($whois->items as $j => $i) {
			    $table->addRow();
		        $table->addCell(3500)->addText( $i->name, 'smallP' );
		        $table->addCell(3500)->addText( $i->value, 'smallP' );
			}
		}

    	$data = $project->domain->ip;

    	if ($data) {
			$section->addText(
			    'Хостинг',
			    'cssH3'
			);

			if (is_array($data)) {
	    		$data = $data[0];
			}

	    	$whois = IpWhois::model()->findByAttributes(array('ip' => $data->ip), array('order' => 'id desc'));

			$section->addText($data->ip, 'cssPB');

	    	if ($whois) {
	    		foreach (explode("\n", $whois->text) as $line) {
					$section->addText(trim($line), 'smallP');
	    		}
	    	}
    	}

		$section->addText(
		    'HTTP заголовки',
		    'cssH3'
		);

    	$data = $project->domain->headers;

    	if ($data and is_array($data)) {
    		$data = $data[0];
    	}

    	if ($data) {
			$table = $section->addTable();

			$rows = explode("\n", $data->text);

			foreach ($rows as $line) {
				$j = trim($line);
				$txt = '';

	            if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $j)) {
	            	$txt = 'Статус';
	            }
	            else {
	            	list($i, $j) = explode(':', $j, 2);

	            	$i = trim($i);

	            	switch ($i) {
	            		case 'Server':
	            			$txt = 'Веб-сервер'; break;
	            		case 'Date':
	            			$txt = 'Дата обращения к серверу'; break;
	            		case 'Content-Type':
	            			$txt = 'Тип контента, кодировка'; break;
	            		case 'Content-Length':
	            			$txt = 'Размер страницы, байт'; break;
	            		case 'X-Powered-By':
	            			$txt = 'Обработчик'; break;
	            		case 'ETag':
	            			$txt = 'Идентификатор ETag'; break;
	            		case 'Expires':
	            			$txt = 'Дата экспирации контента'; break;
	            		case 'Location':
	            			$txt = 'Переадресация'; break;
	            		case 'Last-Modified':
	            			$txt = 'Дата последнего обновления'; break;
	        			case 'Set-Cookie':
	        				$txt = 'Параметры cookie'; break;
	        			case 'Cache-Control':
	        				$txt = 'Параметры кэширования'; break;
	            		case 'X-Bitrix-Composite':
	            			$txt = 'Код битрикса X-Bitrix-Composite'; break;
	        			case 'X-Powered-CMS':
	        				$txt = 'Используемая CMS'; break;
	            		case 'X-XSS-Protection':
	            			$txt = 'Защита от XSS-атак'; break;

	            		default: 
	            			$txt = $i; break;
	            	}
	            }

			    $table->addRow();
		        $table->addCell(4000)->addText( $txt, 'smallPB' );
		        $table->addCell(4000)->addText( trim($j), 'smallP' );
	        }

		    $table->addRow();
	        $table->addCell(4000)->addText( 'Кэширование на стороне клиента If-Modified-Since', 'smallPB' );
	        $table->addCell(4000)->addText( $data->if_modified_since ? 'используется' : 'не используется', 'smallP' );
    	}

		$section->addText(
		    'Robots',
		    'cssH3'
		);

    	$data = Robots::model()->findByAttributes(array('domain_id' => $project->domain->id));

    	if ($data) {
    		foreach (explode("\n", $data->text) as $line) {
				$section->addText(trim($line), 'smallP');
    		}
    	}

		$section->addText(
		    'Sitemap',
		    'cssH3'
		);

        $data = DomainsSitemap::model()->findByAttributes(array('domain_id' => $project->domain->id), array('order' => 'id desc'));

        if ($data) {
			$section->addText('Файл sitemap найден'. ($data->robots ? ' в robots.txt' : '') . ', расположен по адресу ' . $data->url, 'cssP');

    		foreach (explode("\n", $data->text) as $line) {
				$section->addText(trim(htmlspecialchars($line)), 'smallP');
    		}
        }

		$section->addText(
		    'Структура сайта',
		    'cssH3'
		);

		$section->addText(
		    'Контент',
		    'cssH2'
		);

		$section->addText(
		    'Юзабилити',
		    'cssH2'
		);

		$section->addText(
		    'Адаптивность сайта',
		    'cssH3'
		);

        if ($page = $project->domain->sitemap[0]->page) {
            if ($viewport = $page->param('meta-viewport')) {
                $parsed = new Viewport($viewport->value);
            }
        }

        if ($parsed) {
        	$section->addText( 'Viewport найден.', 'cssP' );

			$table = $section->addTable();

            foreach ($parsed->textValues() as $j => $i) {
			    $table->addRow();
		        $table->addCell(4000)->addText( $i['title'] ? $i['title'] : $i['name'], 'smallPB' );
		        $table->addCell(4000)->addText( $i['text'] ? $i['text'] : $i['value'], 'smallP' );
            }
        }
        else {
        	$section->addText( 'Viewport не найден.', 'cssP' );
        }

		$section->addText(
		    'Мобильная версия',
		    'cssH3'
		);

        $useragent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25';

        $context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false,
                    'header' => "User-Agent: " . $useragent . "\r\n"
                )
            )
        );

        $data = @file_get_contents($project->domain->url(), false, $context);

        foreach ($http_response_header as $line) {
            list($a, $b) = explode(':', $line, 2);

            if (trim($a) == 'Location') {
                $mobile = trim($b);
            }
        }

        if ($mobile) {
        	$section->addText('Мобильная версия сайта найдена по адресу ' . $mobile, 'cssP');
        }
        else {
        	$section->addText('Мобильная версия сайта не найдена.', 'cssP');
        }

		/**



		**/

		$section->addText(
		    'Внешние ссылки',
		    'cssH2'
		);

		$section->addText(
		    'Упоминания домена',
		    'cssH3'
		);


        $check = MentionCheck::model()->findByAttributes(array('domain_id' => $project->domain->id), array('order' => 'id desc'));

        $domain = $project->domain;
        $data = $check->result;

        if ($data) {
	        $section->addText('Домен ' . $domain->host() .'  упоминается '. count($data) .' раз, на следующих сайтах', 'cssP');

			foreach ($data as $m) {
		        $section->addText( $m->title, 'smallPB' );
		        $section->addText( $m->url, 'smallP' );
		        $section->addText( $m->text, 'smallP' );

			}

        }
        else {
	        $section->addText('Домен ' . $domain->host() . ' не упоминается.');
	    }

		/**


		**/

		$section->addText(
		    'Анализ позиций',
		    'cssH2'
		);

		$section->addText(
		    'Семантическое ядро сайта',
		    'cssH3'
		);

		$table = $section->addTable();

	    $table->addRow();
        $table->addCell(4000)->addText( 'поисковая фраза', 'smallPB' );
        $table->addCell(1500)->addText( 'частотность', 'smallPB' );

		foreach (Semantic::model()->findAllByAttributes(array('project_id' => $project->id), array('order' => 'phrase asc')) as $word) {
			$stat = Wordstat::model()->findByAttributes(array(
				'word' => $word->phrase,
				'region_id' => $project->regions[0]
			), array('order' => 'id desc'));

		    $table->addRow();
	        $table->addCell(4000)->addText( $word->phrase, 'smallP' );
	        $table->addCell(1500)->addText( $stat ? $stat->stat : '', 'smallP' );
		}

		/**


		**/

		$section->addText(
		    'Анализ позиций',
		    'cssH3'
		);

		$table = $section->addTable();

        $today = $start = new DateTime();
        $start->sub( new DateInterval('P30D') );

        $word = Semantic::model()->findByAttributes(array('project_id' => $project->id), array('order' => 'created_date asc'));

        if ($word) {
            $min_date = new DateTime( $word->created_date );

            if ($start < $min_date) {
                $start = $min_date;
            }
        }

        $criteria = new CDbCriteria;
        $criteria->select = 'p.*';
        $criteria->alias = 'p';
        $criteria->join = 'left join `tbl_semantic` `s` on (p.semantic_id = s.id)';
        $criteria->condition = 's.project_id = :pid and p.date >= :d';
        $criteria->params = array(
            'pid' => $project->id,
            'd' => $start->format('Y-m-d ') . '00:00:00'
        );

        $positions = $positions_data = array();

        foreach (YandexPosition::model()->findAll($criteria) as $p) {
            $positions[ $p->semantic_id ][ substr($p->date, 0, 10) ] = $p->position;
            $positions_data[ $p->semantic_id ][ substr($p->date, 0, 10) ] = array(
                'url' => $p->url,
                'title' => $p->title
            );
        }

	    $table->addRow();
        $table->addCell(4000)->addText( 'поисковая фраза', 'smallPB' );
        $table->addCell(1500)->addText( 'сегодня', 'smallPB' );

        $date = new DateTime();
        $interval = $date->diff( $min_date );

        $table->addCell(1500)->addText( $interval->format('%a дней назад'), 'smallPB' );

        $words = Semantic::model()->findAllByAttributes(array('project_id' => $project->id), array('order' => 'phrase asc'));

        if ($words) {
            foreach ($words as $n => $word) {
                $stat  = $word->stat();

			    $table->addRow();
		        $table->addCell(4000)->addText( $word->phrase, 'smallP' );

                $name = $date->format('Y-m-d');
		        $table->addCell(1500)->addText( isset($positions[ $word->id ][ $name ]) ? $positions[ $word->id ][ $name ] : '' , 'smallP' );

                $name = $min_date->format('Y-m-d');
		        $table->addCell(1500)->addText( isset($positions[ $word->id ][ $name ]) ? $positions[ $word->id ][ $name ] : '' , 'smallP' );
            }
        } 

		// Saving the document as OOXML file...
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save('report_' . $project->id . '_' . date('YmdHis') . '.docx');
    }
      

}


