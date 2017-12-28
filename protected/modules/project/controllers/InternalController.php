<?php

class InternalController extends CSiteController
{

    public $name = 'Внутренняя оптимизация';
    public $title = 'Внутренняя оптимизация';
    public $description = '';

    private function getRegions()
    {
        $content = file_get_contents(dirname(__FILE__) . '/../files/regions.txt');
        $rows = explode("\r\n", $content);
        $result = array();
        foreach ($rows as $row) {
            $r = explode("\t", $row);
            $result[$r[0]] = $r[1];
        }
        return $result;
    }
	
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

        $this->render('project.internal.index');
    }

    public function actionDomain($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Домен';
    	$this->genBreadcrumbs();

        $scr = $model->screenshot();

        if (!$scr or !$scr->id) {
        	$scr = ProjectsScreenshot::model()->findByAttributes(
        		array('domain_id' => $model->domain_id),
        		array('order' => 'id desc')
        	);
        }

        $this->render('project.internal.domain', array(
        	"model" => $model,
        	"screenshot" => $scr,
        ));
    }

    public function actionHosting($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Хостинг';
    	$this->genBreadcrumbs();

        $this->render('project.internal.hosting', array(
        	"model" => $model,
        ));
    }

    public function actionServer($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Сервер';
    	$this->genBreadcrumbs();

        $this->render('project.internal.server', array(
        	"model" => $model,
        ));
    }

    public function actionCms($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Движок (CMS) сайта';
    	$this->genBreadcrumbs();

    	$last_update = Queue::model()->findStageForProject( $model, 11 );

        $this->render('project.internal.cms', array(
        	"last_update" => $last_update ? $last_update->updated_date : null,
        	"model" => $model,
        ));
    }

    public function actionStructure($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Структура сайта';
    	$this->genBreadcrumbs();

        $this->render('project.internal.structure', array(
        	"model" => $model,
        ));
    }

    public function actionTraffic($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Посещаемость сайта';
    	$this->genBreadcrumbs();

        $this->render('project.internal.traffic', array(
        	"model" => $model,
        ));
    }

    public function actionValidator($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Валидатор разметки';
    	$this->genBreadcrumbs();

        $this->render('project.internal.validator', array(
        	"model" => $model,
        ));
    }

    public function actionSpeed($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Скорость загрузки сайта';
    	$this->genBreadcrumbs();

    	$last_update = Queue::model()->findStageForProject( $model, 18 );

    	$params = array(
        	"last_update" => $last_update ? $last_update->updated_date : null,
    		"model" => $model,
    		"timer" => array(
				'namelookup' => false, 
				'connect' => false,
				'pretransfer' => false,
				'starttransfer' => false,
				'total' => false
			)
    	);

    	foreach ($params['timer'] as $j => $i) {
    		$params['timer'][$j] = DomainsResult::model()->findByAttributes(
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

			foreach ($data->formattedResults->ruleResults as $j => $r) {
				$r->name = $j;
				$rules[] = $r;
			}

			usort($rules, function($a, $b){
				if ($a->ruleImpact == $b->ruleImpact) return 0;
				return ($a->ruleImpact < $b->ruleImpact) ? 1 : -1;
			});

			$data->rules = $rules;

			$params['desktop'] = $data;
		}

		$result = DomainsResult::model()->findByAttributes(
			array(
				'domain_id' => $model->domain_id, 
				'name' => 'pagespeed_mobile'
			), array('order' => 'id desc')
		);

		if ($result) {
			$data = json_decode($result->value);

			$data->score = 100;
			foreach ($data->ruleGroups as $j) {
				$data->score = min($data->score, $j->score);
			}

			$rules = array();

			foreach ($data->formattedResults->ruleResults as $j => $r) {
				$r->name = $j;
				$rules[] = $r;
			}

			usort($rules, function($a, $b){
				if ($a->ruleImpact == $b->ruleImpact) return 0;
				return ($a->ruleImpact < $b->ruleImpact) ? 1 : -1;
			});

			$data->rules = $rules;

			$params['mobile'] = $data;
		}

        $this->render('project.internal.speed', $params); 
    }

    public function actionWeight($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Структура и вес страниц';
    	$this->genBreadcrumbs();

    	$res = array();

    	foreach ( CrawlerPage::model()->findAllByAttributes(array('domain_id' => $model->domain_id), array('order' => 'id asc')) as $page) {
    		$pagerank = CrawlerPageRank::model()->findByPk( $page->id );

    		$res[] = array(
    			'url' => $page->url,
    			'in' => $pagerank->in,//CrawlerLinks::model()->countByAttributes(array('link_id' => $page->id)),
    			'out' => $pagerank->out,//CrawlerLinks::model()->countByAttributes(array('page_id' => $page->id)),
    			'rank1' => $pagerank->rank1,
    			'rank2' => $pagerank->rank2,
    			'rank3' => $pagerank->rank3,
    		);
    	}

    	$last_update = Queue::model()->findStageForProject( $model, 9001 );

        $this->render('project.internal.weight', array(
        	'last_update' => $last_update ? $last_update->updated_date : '',
        	'pages' => $res
        )); 
    }

    public function actionYastruct($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Структура по Яндексу';
    	$this->genBreadcrumbs();

    	$check = YandexStructureCheck::model()->findByAttributes(array('domain_id' => $model->domain_id), array('order' => 'id desc'));

    	$pages = YandexStructure::model()->findAllByAttributes(array('check_id' => $check->id), array('order' => 'url asc'));

        $this->render('project.internal.yastruct', array(
        	"model" => $model,
        	"check" => $check,
        	"pages" => $pages,
        ));
    }

    public function actionStructCompare($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Структура сайта';
    	$this->genBreadcrumbs();

    	$struct = array(
    		'sitemap' => array(),
    		'crawler' => array(),
    		'yandex' => array(),
    	);

    	foreach (Sitemap::model()->findAllByAttributes(array('domain_id' => $model->domain->id), array('order' => 'url asc')) as $el) {
    		$struct['sitemap'][] = $el->url;
    	}

    	foreach (CrawlerPage::model()->findAllByAttributes(array('domain_id' => $model->domain_id), array('order' => 'url asc')) as $el) {
    		$struct['crawler'][] = $el->url;
    	}

    	$check = YandexStructureCheck::model()->findByAttributes(array('domain_id' => $model->domain_id), array('order' => 'id desc'));
    	foreach (YandexStructure::model()->findAllByAttributes(array('check_id' => $check->id), array('order' => 'url asc')) as $el) {
    		$struct['yandex'][] = $el->url;
    	}

        $this->render('project.internal.structcompare', array(
        	"model" => $model,
        	"struct" => $struct,
        ));
    }

    public function actionError404($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Ошибки 404';
    	$this->genBreadcrumbs();

    	$pages = array();

    	foreach (Sitemap::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'status' => 404)) as $p) {
    		$pages[] = $p->url;
    	}

    	foreach (CrawlerPage::model()->findAllByAttributes(array('domain_id' => $model->domain_id, 'code' => 404)) as $p) {
    		$pages[] = $p->url;
    	}

    	sort($pages);

    	$last_update = Queue::model()->findStageForProject( $model, 10 );

        $this->render('project.internal.error404', array(
        	"last_update" => $last_update ? $last_update->updated_date : null,
        	"model" => $model,
        	"pages" => $pages,
        ));
    }

    public function actionRedirect($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Редиректы на сайте';
    	$this->genBreadcrumbs();

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

    	$last_update = Queue::model()->findStageForProject( $model, 10 );

        $this->render('project.internal.redirect', array(
        	"last_update" => $last_update ? $last_update->updated_date : null,
        	"model" => $model,
        	"pages" => $pages,
        ));
    }

    public function actionMeta($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Мета-теги';
    	$this->genBreadcrumbs();

    	$last_update = Queue::model()->findStageForProject( $model, 11 );

        $this->render('project.internal.meta', array(
        	"last_update" => $last_update ? $last_update->updated_date : null,
        	"model" => $model,
        	"pages" => $model->domain->sitemap,
        ));
    }

    public function actionLoad( $id, $method )
    {
    	$error_code = 423;

    	if (Yii::app()->request->isAjaxRequest) {
    		$model = Project::model()->findByPk( intval($id) );

    		if ($model and $model->id) {
				if ($model->user_id == Yii::app()->user->id) {
					$action = 'load' . $method;
					if (method_exists($this, $action)) {
						return $this->$action( $model );
					}
					else {
						$error_code = 404;
					}
				}
				else {
					$error_code = 401;
				}
    		}
    		else {
    			$error_code = 404;
    		}
    	}

    	$this->renderPartial('//error/load/' . $error_code);

    	Yii::app()->end();
    }

    private function loadWhois( $project ) {
    	$whois = $project->domain->whois_history[0];
        $params = array();

    	if ($whois) {
	        foreach ($whois->params as $p) {
	            $params[$p->name] = $p;
	        }
    	}

        $data = array(
            'last_update' => TxtHelper::DateTimeFormat( $whois->date ),
            'html' => $this->renderPartial(
                'domain/whois', 
                array(
                    'domain' => $project->domain,
                    'data' => $whois->items, 
                    'params' => $params
                ),
                true
            ),
        );

        echo CJavaScript::jsonEncode($data);
    	Yii::app()->end();
    }

    private function loadScreenshotDate( $project ) {
    	$scr = $project->screenshot;

        if (!$scr or !$scr->id) {
        	$scr = ProjectsScreenshot::model()->findByAttributes(
        		array('domain_id' => $project->domain_id),
        		array('order' => 'id desc')
        	);
        }


        echo CJavaScript::jsonEncode(array(
            'last_update' => TxtHelper::DateTimeFormat($scr->date),
        ));

        Yii::app()->end();
    }

    private function loadWayback( $project ) {
        $data = $project->domain->wayback;

        $this->renderPartial('domain/wayback', array('data' => $data));

        Yii::app()->end();
    }

    private function loadHostingIp( $project ) {
    	$data = $project->domain->ip;

    	if (is_array($data)) {
    		$data = $data[0];
    	}

    	if ($data) {
	    	$whois = IpWhois::model()->findByAttributes(array('ip' => $data->ip), array('order' => 'id desc'));

	    	if (!$whois or !$whois->id) {
	    		$whois = IpWhois::download( $data->ip );
	    	}
    	}

        echo CJavaScript::jsonEncode(array(
            'last_update' => TxtHelper::DateTimeFormat($data->date),
            'html' => $this->renderPartial('hosting/ip', array(
                'data' => $data,
                'whois' => $whois,
            ), true)
        ));

    	Yii::app()->end();
    }

    private function loadServerHeaders( $project ) {
    	$data = $project->domain->headers;

    	if (is_array($data)) {
    		$data = $data[0];
    	}

        echo CJavaScript::jsonEncode(array(
            'last_update' => TxtHelper::DateTimeFormat($data->date),
        	'html' => $this->renderPartial('server/headers', array('data' => $data), true)
        ));

    	Yii::app()->end();
    }

    private function loadRobots( $project ) {
    	$data = Robots::model()->findByAttributes(array('domain_id' => $project->domain->id));

        echo CJavaScript::jsonEncode(array(
            'last_update' => TxtHelper::DateTimeFormat($data->date),
            'html' => $this->renderPartial('server/robots', array('data' => $data), true)
        ));

    	Yii::app()->end();
    }

    private function loadSitemapInfo( $project ) {
        $data = DomainsSitemap::model()->findByAttributes(array('domain_id' => $project->domain->id), array('order' => 'id desc'));

        echo CJavaScript::jsonEncode(array(
            'last_update' => TxtHelper::DateTimeFormat( $data ? $data->date : time() ),
            'html' => $this->renderPartial(
                'server/sitemapinfo', 
                array(
                    'data' => $data, 
                ), 
                true
            )
        ));

        Yii::app()->end();
    }

    private function loadSitemap( $project ) {
        /*$crawler = Sitemap::model()->findAllByAttributes(array('domain_id' => $project->domain->id, 'status' => 0), array('order' => 'url asc'));

        if (count($crawler)) {
            $n = 0;
            $timer = time();

            foreach ($crawler as $s) {
                $s->checkStatus();
                $n++;

                if (time() - $timer > 5) {
                    echo CJavaScript::jsonEncode(array(
                        'retry' => 2,
                        'html' => $this->renderPartial('server/sitemap-progress', array('total' => (count($crawler) - $n)), true),
                    ));

                    Yii::app()->end();

                    return;
                }
            }
        }*/

        $data = Sitemap::model()->findAllByAttributes(array('domain_id' => $project->domain->id), array('order' => 'url asc'));

        /*if (count($data) == 0) {
            $robots = Robots::model()->last( $project->domain );

            if ($robots) {

                $sitemaps = $robots->sitemaps();

                if (count($sitemaps) == 0) {
                    $sitemaps[] = $project->domain->url() . '/sitemap.xml';
                }

                $sm = new SitemapDownloader( $sitemaps );
                if ($load_result = $sm->load()) {
                    $sm->save( $project->domain->id );

                    $data = Sitemap::model()->findAllByAttributes(array('domain_id' => $project->domain->id), array('order' => 'url asc'));

                    if (count($data)) {
                        echo CJavaScript::jsonEncode(array(
                            'retry' => 2,
                        ));
                        Yii::app()->end();
                    }
                }
                
            }
        }*/

        echo CJavaScript::jsonEncode(array(
            'last_update' => ($data[0] or isset($load_result)) ? TxtHelper::DateTimeFormat( $data[0] ? $data[0]->date : time() ) : '',
            'html' => $this->renderPartial(
                'server/sitemap', 
                array(
                    'data' => $data, 
                    'load_result' => $load_result,
                    'error_code' => $sm ? $sm->error_code : null,
                ), 
                true
            )
        ));

        Yii::app()->end();
    }

    private function loadCrawlerStruct( $project ) {
    	$pages = CrawlerPage::model()->findAllByAttributes(array('domain_id' => $project->domain_id), array('order' => 'url asc'));

        $this->renderPartial('structure/crawler', array(
        	"model" => $model,
        	"pages" => $pages,
        ));

        Yii::app()->end();
    }

    private function loadYaStruct( $project ) {
    	$check = YandexStructureCheck::model()->findByAttributes(array('domain_id' => $project->domain_id), array('order' => 'id desc'));

    	$pages = YandexStructure::model()->findAllByAttributes(array('check_id' => $check->id), array('order' => 'url asc'));

        $this->renderPartial('yandex/structure', array(
        	"model" => $model,
        	"check" => $check,
        	"pages" => $pages,
        ));

        Yii::app()->end();
    }

    private function loadCMS( $project ) {
        $cms = new CMSCheck( $project->domain->url() );
        $data = $cms->check( $_GET['cms'] );

        $this->renderPartial('cms/result', array('data' => $data));

        Yii::app()->end();
    }

    private function loadCounters( $project ) {
    	$data = array();
    	$date = 0;

        foreach (Counter::model()->findAllByAttributes(array('domain_id' => $project->domain->id), array('order' => '`date` desc')) as $c) {
        	if (!$data[ $c->name ]) {
        		$data[ $c->name ] = $c;
        	}

        	if ($date < $c->date) {
        		$date = $c->date;
        	}
        }

        echo CJavaScript::jsonEncode(array(
            'last_update' => TxtHelper::DateTimeFormat($date),
            'html' => $this->renderPartial('traffic/counters', array('data' => $data), true)
        ));

        Yii::app()->end();
    }

}
