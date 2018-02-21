<?php

class IndexController extends CSiteController
{

    public $name = '';
    public $description = '';

	private function genBreadcrumbs( ) {
        $this->title = $this->project->name;

        $this->breadcrumbs[$this->project->name] = Yii::app()->createUrl($this->module->id . '/index/view', array('id' => $this->project->id));

        $this->breadcrumbs[] = 'Просмотр';
	}

    public function actionIndex($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->genBreadcrumbs();

        $this->render('project.internal.index', array(
        	'project' => $this->project,
        ));
    }

    public function actionNew(){
        $this->title = 'Добавить сайт';
        $this->breadcrumbs[] = $this->title;

        $type = 'Project';
        $model = new Project;

        if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            $model->user_id = Yii::app()->user->id;
            if ($model->save()) {
            	$data_exists = false;
            	foreach (Project::model()->findAllByAttributes(array('domain_id' => $model->domain_id)) as $el) {
            		if ($el->id != $model->id) {
            			$data_exists = true;
            		}
            	}

            	if (!$data_exists) {
	            	$model->fullCheck();
            	}
            	else {
					$q = new Queue;
					$q->object_type = 'Project';
					$q->object_id = $model->id;
					$q->stage = 2;
					$q->save();
            	}

                if (!isset($_POST['apply'])){
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/view', array('id' => $model->id)));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }
            }
            else {
                $e = $model->getErrors();
            }
        }

        $this->render('project.new', array(
            "model" => $model, 
            'formElements' => $this->getForm($model),
            'errors' => $e,
        ));

    }

    public function actionView($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->genBreadcrumbs();

        $criteria = new CDbCriteria;
        $criteria->select = 't.*';
        $criteria->alias = 't';
        $criteria->condition = '(t.object_type = :type_project and t.object_id = :project_id) or (t.object_type = :type_domain and t.object_id = :domain_id)';
        $criteria->order = 't.id asc';
        $criteria->params = array(
            'type_project' => 'Project',
            'type_domain' => 'Domain',
            'project_id' => $model->id,
            'domain_id' => $model->domain->id,
        );

        $queue = Queue::model()->find($criteria);

        $this->render('project.internal.index', array(
        	'project' => $this->project,
        	'model' => $queue
        ));
    }


    public function actionUpdate($id)
    {
        $this->project = $model = Project::model()->findByPk($id);
        $this->genBreadcrumbs();
        $this->description = 'Настройки';

        /*if (isset($_POST[ get_class($model) ])) {
            $model->attributes = $_POST[ get_class($model) ];
            if ($model->save()) {
                if (!isset($_POST["apply"])) {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/view', array('id' => $model->id)));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }
            }
        }*/

        $org = ProjectsOrg::model()->findByAttributes(array('project_id' => $model->id));

        $this->render('project.index.edit', array(
        	"model" => $model, 
        	"org" => $org, 
        	'formElements' => $this->getForm($model),
        	'competitors' => ProjectsCompetitor::model()->findAllByAttributes(array('project_id' => $model->id)),
        ));
    }

    public function actionOrg($id)
    {
        $this->project = $model = Project::model()->findByPk($id);
        $this->genBreadcrumbs();
        $this->description = 'Информация об организации';

        $form = new OrgForm;
        $form->project = $model;

        if (isset($_POST[ get_class($form) ])) {
            $form->attributes = $_POST[ get_class($form) ];
            if ($form->save()) {

                $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));

                /*if (!isset($_POST["apply"])) {
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }*/
            }
        }
        elseif ($org = ProjectsOrg::model()->findByAttributes(array('project_id' => $model->id))) {
        	$form->attributes = $org->attributes;

        	if ($org->org_phone and is_array($org->org_phone)) {
	        	foreach ($org->org_phone as $p) {
	        		$form->phone_country[] = $p->country;
	        		$form->phone_code[] = $p->code;
	        		$form->phone_number[] = $p->number;
	        		$form->phone_extra[] = $p->extra;
	        		$form->phone_name[] = $p->name;
	        	}
        	}

        	if ($org->org_site and is_array($org->org_site)) {
        		foreach ($org->org_site as $s) {
        			$form->site[] = $s->url;
        		}
        	}

        	if ($org->org_social and is_array($org->org_social)) {
        		foreach ($org->org_social as $s) {
        			$form->social[] = $s->url;
        		}
        	}

        	if ($org->org_worktime and is_array($org->org_worktime)) {
        		foreach ($org->org_worktime as $s) {
        			$form->worktime_days[] = $s->days;
        			$form->worktime_time1[] = $s->time1;
        			$form->worktime_time2[] = $s->time2;
        		}
        	}
        }

        $this->render('project.index.org', array(
            "model" => $model,
            "org" => $form,
        ));
    }

    public function actionCompetitor($id)
    {
        $this->project = $model = Project::model()->findByPk($id);
        $this->genBreadcrumbs();
        $this->description = 'Добавить сайт конкурента';

        $form = new CompetitorForm;
        $form->project = $model;

        if (isset($_POST[ get_class($form) ])) {
            $form->attributes = $_POST[ get_class($form) ];
            if ($form->save()) {
                $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
            }
            else {
                $e = $form->getErrors();
            }
        }

        $this->render('project.index.competitor', array(
            "model" => $model,
            "fdata" => $form,
            "errors" => $e,
        ));
    }
    
    public function actionErrors($id)
    {
        //$this->project = $model = Project::model()->findByPk($id);
        //$this->project = $model = ReportErrorsLinks::model()->findByPk($id); 
        $this->genBreadcrumbs();
	$this->description = 'Отслеживание изменений на важных страницах';
	
	

        $modelReportErrorsLinks = new ReportErrorsLinks;
        //$form->project = $model;

        if (isset($_POST[ get_class($form) ])) {
            $form->attributes = $_POST[ get_class($form) ];
            if ($form->save()) {
                $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
            }
            else {
                $e = $form->getErrors();
            }
        }

        $this->render('project.index.errors', array(
            //"model" => $model,
            "modelReportErrorsLinks" => $modelReportErrorsLinks,
            "errors" => $e,
        ));
    }
    

    public function actionRemove($id)
    {
        $this->project = $model = Project::model()->findByPk($id);
        $this->project->delete();

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(Yii::app()->createUrl('main/user/profile'));
        }

        Yii::app()->end();
    }

    public function actionDrop($id)
    {
        $this->project = $model = Project::model()->findByPk($id);

        if ($model and $model->id) {

	        $domain_id = $model->domain_id;

	        foreach (Counter::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();

	        /* Delete Crawler PageRank, Links in-out, and Pages */

			Yii::app()->db->createCommand('DELETE r 
				FROM tbl_crawler_page_rank as r
					INNER JOIN  tbl_crawler_page as p using (id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE l
				FROM tbl_crawler_links as l
					INNER JOIN  tbl_crawler_page as p on (l.page_id = p.id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE l
				FROM tbl_crawler_links as l
					INNER JOIN  tbl_crawler_page as p on (l.link_id = p.id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE p FROM tbl_crawler_page as p WHERE p.domain_id = ' . $domain_id)->execute();

			/* Delete Domain Params */

	        foreach (DomainsHeaders::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsIp::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsMainpage::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsResource::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsResult::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsSitemap::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsWayback::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsWhois::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsWhoisFull::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();
	        foreach (DomainsWhoisParam::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();

	        foreach (MentionCheck::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) {
	        	foreach (Mention::model()->findAllByAttributes(array('check_id' => $element->id)) as $m) $m->delete();
	        	$element->delete();
	        }

	        /* Delete Domain Page Params */

			Yii::app()->db->createCommand('DELETE r 
				FROM tbl_pages_images as r
					INNER JOIN  tbl_pages as p on (r.page_id = p.id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE r 
				FROM tbl_pages_links as r
					INNER JOIN  tbl_pages as p on (r.page_id = p.id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE r 
				FROM tbl_pages_params as r
					INNER JOIN  tbl_pages as p on (r.page_id = p.id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE r 
				FROM tbl_pages_resources as r
					INNER JOIN  tbl_pages as p on (r.page_id = p.id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE r 
				FROM tbl_pages_spell as r
					INNER JOIN  tbl_pages as p on (r.page_id = p.id)
				WHERE p.domain_id = ' . $domain_id
			)->execute();

			Yii::app()->db->createCommand('DELETE FROM tbl_pages WHERE domain_id = ' . $domain_id)->execute();

			Yii::app()->db->createCommand('DELETE FROM tbl_pages_phrase WHERE domain_id = ' . $domain_id)->execute();

	        foreach (Queue::model()->findAllByAttributes(array('object_type' => 'Domain', 'object_id' => $domain_id)) as $element) $element->delete();

	        foreach (Robots::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $element) $element->delete();

			Yii::app()->db->createCommand('DELETE FROM tbl_sitemap WHERE domain_id = ' . $domain_id)->execute();

	        foreach (Project::model()->findAllByAttributes(array('domain_id' => $domain_id)) as $project) {

		        foreach (ProjectsFavicon::model()->findAllByAttributes(array('project_id' => $project->id)) as $element) $element->delete();
		        foreach (ProjectsKeywords::model()->findAllByAttributes(array('project_id' => $project->id)) as $element) $element->delete();
		        foreach (ProjectsOrg::model()->findAllByAttributes(array('project_id' => $project->id)) as $element) {
		        	foreach (ProjectsOrgPhone::model()->findAllByAttributes(array('org_id' => $element->id)) as $i) $i->delete();
		        	foreach (ProjectsOrgSite::model()->findAllByAttributes(array('org_id' => $element->id)) as $i) $i->delete();
		        	foreach (ProjectsOrgWorktime::model()->findAllByAttributes(array('org_id' => $element->id)) as $i) $i->delete();

		        	$element->delete();
		        }
		        foreach (ProjectsScreenshot::model()->findAllByAttributes(array('project_id' => $project->id)) as $element) $element->delete();

		        foreach (Queue::model()->findAllByAttributes(array('object_type' => 'Project', 'object_id' => $project->id)) as $element) $element->delete();

		        foreach (Semantic::model()->findAllByAttributes(array('project_id' => $project->id)) as $element) {
		        	foreach (YandexPosition::model()->findAllByAttributes(array('semantic_id' => $element->id)) as $i) $i->delete();
		        	$element->delete();
		        }

		        foreach (YandexStructure::model()->findAllByAttributes(array('project_id' => $project->id)) as $element) $element->delete();
		        foreach (YandexStructureCheck::model()->findAllByAttributes(array('project_id' => $project->id)) as $element) $element->delete();

	        	$project->delete();

	        }

	        foreach (Domain::model()->findAllByAttributes(array('id' => $domain_id)) as $element) $element->delete();
	    }

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(Yii::app()->createUrl('main/user/profile'));
        }

        Yii::app()->end();
    }

    public function getForm($element)
    {
        return array(
            'rows' => array(
                'Параметры проекта',
                array(
                    'name' => array('type' => 'text'),
                ),
                array(
                    'host' => array('type' => 'text'),
                ),
                array(
                    'keywords' => array('type' => 'tags'),
                ),
                //array(
                //    'alltags' => array('type' => 'textarea'),
                //),
                array(
                    'regions' => array('type' => 'multiselect', 'items' => $this->getRegions()),
                ),
            ),
        );
    }

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
    
    public function actionLoad( $id, $method )
    {
    	$error_code = 423;

    	if (Yii::app()->request->isAjaxRequest) {
    		$model = Project::model()->findByPk( intval($id) );

    		if ($model and $model->id) {
				if ($model->user_id == Yii::app()->user->id) {
					$action = 'load' . $method;
					if (method_exists($this, $action)) {
						return $this->$action( $model );  //  $action = loadStatus  $model = Project
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

    private function loadStatus( $model ) {
        $criteria = new CDbCriteria;
        $criteria->select = 't.*';
        $criteria->alias = 't';
        $criteria->condition = '((t.object_type = :type_project and t.object_id = :project_id) or (t.object_type = :type_domain and t.object_id = :domain_id)) and (`status` < 1 or `updated_date` >= :date)';
        $criteria->order = 't.id asc';
        $criteria->params = array(
            'type_project' => 'Project',
            'type_domain' => 'Domain',
            'project_id' => $model->id,
            'domain_id' => $model->domain->id,
            'date' => date("Y-m-d H:i:s", time() - 24*3600)
        );

        $queue = Queue::model()->findAll($criteria);

        $criteria->condition .= ' and t.status = 0';  //  если в Queue у прожекта status = 0 отчет еще не готов 

        $total = Queue::model()->count($criteria);

        if ($total and $queue) {
            echo CJavaScript::jsonEncode(array(
                'retry' => 5,
	            'html' => $this->renderPartial(
	                'status', 
	                array(
	                    'queue' => $queue, 
	                ), 
	                true
	            )
            ));
        }
        else {
	        echo CJavaScript::jsonEncode(array(
	            'html' => $this->renderPartial(
	                'status', 
	                array(), 
	                true
	            )
	        ));
        }

        Yii::app()->end();
    }

    public function actionTplText()
    {
    	$t = TplText::model()->findByPk( $_POST['id'] );

    	if ($t) {
    		$t->html = $_POST['html'];
    		$t->save();

    		print 'ok';
    	}

    	else {
    		print 'not found';
    	}

        Yii::app()->end();
    }

}
