<?php

class ReportController extends CSiteController
{

    public $name = 'Отчеты';
    public $title = 'Отчеты';
    public $description = '';
    const DONE = 0;
    const WAIT = 1;
    const PROCESS = 2;

	private function genBreadcrumbs( ) {
        $this->breadcrumbs[$this->project->name] = Yii::app()->createUrl($this->module->id . '/index/view', array('id' => $this->project->id));

        if ($this->description) {
	        $this->breadcrumbs[$this->title] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index', array('id' => $this->project->id));
	        $this->breadcrumbs[] = $this->description;
        }
        else {
	        $this->breadcrumbs[] = $this->title;
        }

	}

    public function actionIndex($id){
        $this->project = $model = Project::model()->findByPk($id);
    	$this->genBreadcrumbs();
    	
    	$domainModel = Project::model()->findAll();
    	//var_dump(phpinfo());
        
        $this->render('project.report.index', array(
        	"model" => $model,
        	"res" => $domainModel,
        	"domain" => $this->project,
        	"done" => self::DONE,
        	"wait" => self::WAIT,
        	"process" => self::PROCESS,
        ));
    }

    
    public function actionAddindex($id) {

    $projectModel = Project::model()->findByPk($id);
    $httpRequest = new CHttpRequest();
    $dir = Yii::app()->params['report']['path'] . '/';
    return $httpRequest->sendFile( $projectModel->fileName . '.docx', file_get_contents( $dir . $projectModel->fileName . '.docx'));
        
    }
    
    
    public function actionTest() {

    //$tr = DomainsHeaders::model()->findByAttributes(array('domain_id' => 60106));
    $tr = new ValidateUrl();
   // $tr->current_www = 'www'; // если редирект настроен
   // $tr->current_https = 'https'; // если редирект настроен 
    //$tr->save();
    
    var_dump ( $tr->http() );
    //var_dump ( $tr->protocol );
	//print_r ( Robots::model()->test ); 
    }
    
        public function status($url) {
        
	  $out = false;
          if( $curl = curl_init() ) {
	      curl_setopt($curl,CURLOPT_URL,$url);
	      curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	      curl_setopt($curl,CURLOPT_NOBODY,true);
	      curl_setopt($curl,CURLOPT_HEADER,true);
	      $out = curl_exec($curl);
	      curl_close($curl);
	  }

	  if ($out) {
		  $m = array();

		  if (preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $out, $m )) {
		      $res = intval($m[1]);
		  }
	  } 
	
	return $res;
        
    }
    
    
    public function actionDocx($id){

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

		/* 2.3 Проверка орфографии */
		/* -> 2.7 */

		/* 2.4 Контактные данные на сайте */

        $xml_data['company'] = new StdClass;
        $xml_data['company']->inn = array();
        $xml_data['company']->ogrn = array();

        foreach ($model->domain->sitemap as $sm) {
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
    	}

        $xml_data['company']->tel = $tel_total;
        $xml_data['company']->mail = $mails;

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

	            if (is_array($meta))
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

	            $files = $p->linkFiles();
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
				}

				if ($p->uniq >= 0) {
					$uniq_sum += $p->uniq;
					$uniq_total++;
				}
            }
        }

        $xml_data['uniq'] = $uniq_total ? round($uniq_sum / $uniq_total) : -1;

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

		/* 3.9 Наличие  JavaScript */

		$xml_data['js'] = DomainsResource::model()->findAllByAttributes(array(
			'domain_id' => $model->domain_id, 
			'type' => DomainsResource::T_SCRIPT
		));

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

        /**

		Вывод документа

        **/

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
