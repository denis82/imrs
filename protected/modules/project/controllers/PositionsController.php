<?php

class PositionsController extends CProjectController
{

    public $name = 'Анализ позиций';
    public $title = 'Анализ позиций';
    public $description = '';

    public function actionIndex($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->checkProject();
    	$this->genBreadcrumbs();

        $this->render('project.position.index');
    }

    public function actionWords($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Анализ слов и фраз сайта';
        $this->checkProject();
        $this->genBreadcrumbs();

        $pages = Sitemap::model()->findAllByAttributes(array(
            'domain_id' => $model->domain->id,
            'status' => 200
        ));

        $this->render('project.positions.words', array(
            "model" => $model,
            "pages" => $pages,
        ));
    }

    public function actionSemantic($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Семантическое ядро сайта';
        $this->checkProject();
        $this->genBreadcrumbs();

        $form = new SemanticForm;
        if (isset($_POST['SemanticForm'])) {
            $form->attributes = $_POST['SemanticForm'];
            $form->project = $model;

            if ($form->save()) {
                $this->redirect(Yii::app()->urlManager->createUrl('project/positions/semantic', array('id' => $model->id)));
                Yii::app()->end();
            }
        }

        $this->render('project.positions.semantic', array(
            "model" => $model,
            "form" => $form,
            "words" => Semantic::model()->findAllByAttributes(array('project_id' => $model->id), array('order' => 'phrase asc')),
        ));
    }

    public function actionAnalyze($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Анализ позиций';
        $this->checkProject();
        $this->genBreadcrumbs();

        $today = $start = new DateTime();
        $start->sub( new DateInterval('P30D') );

        $word = Semantic::model()->findByAttributes(array('project_id' => $model->id), array('order' => 'created_date asc'));

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
            'pid' => $model->id,
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

        $this->render('project.positions.analyze', array(
            "model" => $model,
            "words" => Semantic::model()->findAllByAttributes(array('project_id' => $model->id), array('order' => 'phrase asc')),
            "min_date" => $start,
            "positions" => $positions,
            "data" => $positions_data,
        ));
    }

    public function actionYametrika( $id ) {
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Яндекс.Метрика';
        $this->checkProject();
        $this->genBreadcrumbs();

        $token = 'AQAAAAAAAJL4AAAtCs7K1CrOPE6InROBmmX4FJc';

        $query = 'https://api-metrika.yandex.ru/management/v1/counters';

		$opts = array('http' =>
		    array(
		        'method'  => 'GET',
		        'header'  => "Authorization: OAuth " . $token . "\r\n"
		    )
		);

		$context  = stream_context_create($opts);

		// Traffic source
		$result = file_get_contents($query, false, $context);
		$r_data = json_decode($result);

		$counters = array();

		if ($r_data and is_array($r_data->counters)) {
			foreach ($r_data->counters as $counter) {
				if ($counter->site == $model->domain->host()) {
					$counters[] = $counter;
				}
			}
		}

		$counter_id = $counters[0]->id;

		if ($counter_id) {
			$curdate = date('Y-m-d');

			$date = new DateTime( $curdate );
			$date2 = $date->format('Y-m-d');

			$date->sub( new DateInterval('P1M') );
			$date1 = $date->format('Y-m-d');

			/*'&metrics=ym:s:users'. */

			$query = 'https://api-metrika.yandex.ru/stat/v1/data/bytime?'.
			'direct_client_logins=xsite'.
			'&ids=' . $counter_id .
			'&metrics=ym:s:visits'. 
			'&date1=' . $date1 .
			'&date2=' . $date2 .
			'&group=day'.
			'&include_undefined=true'.
			'&proposed_accuracy=true'.
			'';

			// Traffic source
			$result = @file_get_contents($query . '&dimensions=ym:s:<attribution>TrafficSource', false, $context);
			$traffic = json_decode($result);

			// UTM Source
			$result = @file_get_contents($query . '&dimensions=ym:s:UTMSource', false, $context);
			$utm = json_decode($result);

			// Result Prepare

			$stat = array();

			for ($date = new DateTime( $date1 ); $date->format('Y-m-d') != $date2; $date->add( new DateInterval("P1D") )) {
				$stat[ $date->format('Y-m-d') ] = array();
				if (count($stat) > 40) break;
			}

			$traffic_source = array();

			if ($traffic->data and is_array($traffic->data)) {
				foreach ($traffic->data as $j => $i) {
					$traffic_source[ $i->dimensions[0]->id ] = $i->dimensions[0]->name;

					foreach ($i->metrics as $jj => $ii) {
						foreach ($ii as $jjj => $iii) {
							$stat[ $traffic->time_intervals[$jjj][0] ][ $i->dimensions[0]->id ] = $iii;
						}
					}
				}
			}

			if ($utm->data and is_array($utm->data)) {
				foreach ($utm->data as $j => $i) {
					$traffic_source[ 'utm_' . $i->dimensions[0]->name ] = $i->dimensions[0]->name;

					foreach ($i->metrics as $jj => $ii) {
						foreach ($ii as $jjj => $iii) {
							$stat[ $utm->time_intervals[$jjj][0] ][ 'utm_' . $i->dimensions[0]->name ] = $iii;
						}
					}
				}
			}

		}

        $this->render('project.positions.yametrika', array(
            "model" => $model,
            "counters" => $counters,

            "stat" => $stat,
            "traffic_source" => $traffic_source,
            "utm" => $utm,
            "traffic" => $traffic,
        ));
    }

    protected function loadAddSemantic( $project ) {
        $sql = "insert ignore 
            into tbl_semantic 
                (project_id, phrase, created_date) 
            values 
                (:pid, :phrase, NOW())";

        $parameters = array(
            'pid' => $project->id,
            'phrase' => $_POST['phrase'],
        );

        Yii::app()->db->createCommand($sql)->execute($parameters);

        echo CJavaScript::jsonEncode(array(
            'content' => '<i class="icon-checkmark2"></i> добавлено'
        ));

        Yii::app()->end();
    }

    protected function loadWords( $project ) {
        $model = $project;
        $page_id = 0;

        if ($_POST['page']) {
            $sm = Sitemap::model()->findByPk( $_POST['page'] );
        	$page_id = intval( $sm->page->id );
        }

        $data_site = array(); 

        $data = Yii::app()->db->createCommand()
            ->select('phrase, sum(qty) as total')
            ->from('tbl_pages_phrase')
            ->where('domain_id=:id', array(':id' => $model->domain->id))
            ->group('phrase')
            ->queryAll();

        foreach ($data as $j => $i) {
        	$data_site[$i['phrase']] = $i['total'];
        }

        $data = Yii::app()->db->createCommand()
            ->select('phrase, gr, sum(qty) as total')
            ->from('tbl_pages_phrase')
            ->where('page_id=:pid and domain_id=:id', array(
                ':pid' => $page_id, 
                ':id' => $model->domain->id))
            ->group('phrase')
            ->order('total desc')
            ->queryAll();

        $except = array();

        $excarr = array( 'ADV', 'ADVPRO', 'ANUM', 'APRO', 'CONJ', 'INTJ', 'NUM', 'PART', 'PR', 'SPRO' );
        foreach ($excarr as $j) {
        	$except[] = $j;
        	foreach ($excarr as $i) {
        		$except[] = $j . '+' . $i;
        		foreach ($excarr as $l) {
        			$except[] = $j . '+' . $i . '+' . $l;
        		}
        	}
        }

        foreach ($data as $j => $i) {
        	if (in_array($i['gr'], $except)) {
        		unset($data[$j]);
        	}
        	else {
        		$data[$j]['site'] = $data_site[$i['phrase']];
        	}
        }


        echo CJavaScript::jsonEncode(array(
            'content' => $this->renderPartial('words', array('stat' => $data), true)
        ));

        Yii::app()->end();
    }

    protected function loadSemantic( $project ) {

        $yad = new YandexDirectWordstat( Yii::app()->params['yandexDirect']['user'] );
        $yad->setToken( Yii::app()->params['yandexDirect']['token'] );

        $report = $project->semantic_report;

        $report_list = $yad->getReportList();

        $n = count($report_list);

        if (count($report_list)) {

            foreach ($report_list as $ro) {

                $ydr = YandexDirectReport::model()->findByPk( $ro->ReportID );

                if ($ydr and $ydr->id and $ro->StatusReport == 'Done') {
                    $ydr->status = $ro->StatusReport;
                    $ydr->save();

                    $report_result = $yad->getReport( $ydr->id );

                    foreach ($report_result as $wordstat) {

                        $region_id = $wordstat->GeoID[0];
                        $keyword = mb_strtolower($wordstat->Phrase, 'UTF-8');

                        $found = false;
                        $shows = 0;
                        $strict = 0;

                        /*$strict = false;
                        
                        if (strpos($keyword, '!') !== false){
                            $strict = true;
                        }*/

                        foreach ($wordstat->SearchedWith as $stat) {
                            if (mb_strtolower($stat->Phrase, 'UTF-8') == $keyword) {
                                $shows = (int)$stat->Shows;
                                $found = true;
                            }
                        }

                        /*foreach ($wordstat->SearchedAlso as $stat) {
                            Wordstat::saveResult(array(
                                'region_id' => $region_id,
                                'word' => $stat->Phrase,
                                'stat' => (int)$stat->Shows;
                            ));
                        }*/
                        
                        if (!$found && isset($wordstat->SearchedWith)) {
                            $shows = $wordstat->SearchedWith[0]->Shows;

                            /*Wordstat::saveResult(array(
                                'region_id' => $region_id,
                                'word' => $keyword,
                                'stat' => $shows
                            ));*/
                        }

                        $r = Wordstat::saveResult(array(
                            'region_id' => $region_id,
                            'word' => $keyword,
                            'stat' => $shows
                        ));

                    }

                    if ($yad->deleteReport( $ydr->id )) {
                        Yii::app()->db->createCommand(
                            'delete from `tbl_yandex_direct_queue` where `report_id` = ' . $ydr->id
                        )->execute();

                        $n--;
                    }
                }
            }

            sleep(5);
        }

        if ($n < 5) {

            $criteria = new CDbCriteria;
            $criteria->alias = 's';
            $criteria->select = 's.*';
            $criteria->condition = 's.project_id = :pid and q.report_id = 0';
            $criteria->join = 'left join tbl_yandex_direct_queue as q on (s.id = q.semantic_id)';
            $criteria->limit = '10';
            $criteria->params = array(':pid' => $project->id);

            $semantic = array();
            $keywords = array();

            foreach (Semantic::model()->findAll( $criteria ) as $s) {
                $semantic[] = $s->id;
                $keywords[] = $s->phrase;
            }

            $regions = count($project->regions) ? $project->regions : array( 225 );

            $report_id = $yad->createReport( $keywords, $regions );

            if ($report_id > 0) {
                $ydr = new YandexDirectReport();
                $ydr->id = $report_id;
                $ydr->report_id = $report_id;
                $ydr->project_id = $project->id;
                $ydr->save();

                Yii::app()->db->createCommand(
                    'update `tbl_yandex_direct_queue` 
                        set `report_id` = ' . $ydr->id . ' 
                        where `semantic_id` in (' . implode(', ', $semantic) . ')'
                )->execute();
            }

            sleep(5);
        }

        /* Добавление слов в очередь */

        $ids = array();

        foreach ( Semantic::model()->findAllByAttributes(array('project_id' => $project->id)) as $w ) {
            $stat = $w->stat();

            if ($stat === false) {
                $ids[] = $w->id;
            }
            else {
                $result[ $w->id ] = $stat;
            }
        }

        if (count($ids)) {
            $sql = "insert ignore into tbl_yandex_direct_queue (semantic_id) values (" . implode('), (', $ids) . ")";
            Yii::app()->db->createCommand($sql)->execute();
        }

        echo CJavaScript::jsonEncode(array(
            'stat' => $result,
            'queue' => count($ids)
        ));

        Yii::app()->end();
    }

}
