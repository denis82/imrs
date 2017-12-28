<?php

class ExtlinksController extends CProjectController
{

    public $name = 'Внешние ссылки';
    public $title = 'Внешние ссылки';
    public $description = '';

    public function actionIndex($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->checkProject();
    	$this->genBreadcrumbs();

        $this->render('project.extlinks.index');
    }

    public function actionIncoming($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Входящие ссылки';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.extlinks.incoming', array(
            "model" => $model,
        ));
    }

    public function actionOutgoing($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Исходящие ссылки';
        $this->checkProject();
        $this->genBreadcrumbs();

        $total = 0;

        foreach ($model->domain->sitemap as $sm) {
            $page = $sm->page;

            if ($page) {
                $total += count( $page->linkOut() );
            }
        }

        $this->render('project.extlinks.outgoing', array(
            "model" => $model,
            "pages" => $model->domain->sitemap,
            "total" => $total,
        ));
    }

    public function actionMention($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Упоминания домена';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.extlinks.mention', array(
            "model" => $model,
        ));
    }

    public function actionSocial($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Социальные сети';
        $this->checkProject();
        $this->genBreadcrumbs();

        $links = array();

        foreach ($model->domain->sitemap as $sm) {
            $page = $sm->page;

            if ($page) {
	            foreach ($page->linkOut() as $link) {
	            	if (substr($link->href, 0, 2) == '//') {
	            		$link->href = 'http:' . $link->href;
	            	}

	            	$a = parse_url($link->href);

	            	$host = implode('.', array_slice(explode('.', $a['host']), -2) );

	            	if (!is_array($links[ $host ]) or !in_array($link->href, $links[ $host ])) {
		            	$links[ $host ][] = $link->href;
	            	}
	            }
            }
        }

        $this->render('project.extlinks.social', array(
            "model" => $model,
            "links" => $links,
        ));
    }

    public function actionFormal($id){
        $this->project = $model = Project::model()->findByPk($id);
        $this->description = 'Формальные признаки';
        $this->checkProject();
        $this->genBreadcrumbs();

        $this->render('project.extlinks.formal', array(
            "model" => $model,
            "tic" => DomainsResult::model()->findByAttributes(
            	array('domain_id' => $model->domain_id, 'name' => 'yandex_tic'), 
            	array('order' => 'id desc')
            )
        ));
    }

    protected function loadMention( $project ) {

        $check = MentionCheck::model()->findByAttributes(array('domain_id' => $project->domain->id), array('order' => 'id desc'));

        if (!$check or !$check->id) {
            echo CJavaScript::jsonEncode(array(
                'html' => '-',
            ));

            Yii::app()->end();
        }

        if ($check->progress) {

            echo CJavaScript::jsonEncode(array(
                'retry' => 5,
                'html' => $this->renderPartial('progress', array('total' => $result['percent'] . '%'), true),
            ));

            Yii::app()->end();
        }

        echo CJavaScript::jsonEncode(array(
            'last_update' => TxtHelper::DateTimeFormat( $check->date ),
            'html' => $this->renderPartial(
                'mention', 
                array(
                    'domain' => $project->domain, 
                    'data' => $check->result, 
                ), 
                true
            )
        ));

        Yii::app()->end();
    }

}
