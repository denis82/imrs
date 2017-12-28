<?php

class CProjectController extends CSiteController
{

    public $name = 'Контент';
    public $title = 'Контент';
    public $description = '';

	protected function genBreadcrumbs( ) {
        $this->breadcrumbs[$this->project->name] = Yii::app()->createUrl($this->module->id . '/index/view', array('id' => $this->project->id));

        if ($this->description) {
	        $this->breadcrumbs[$this->title] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index', array('id' => $this->project->id));
	        $this->breadcrumbs[] = $this->description;
        }
        else {
	        $this->breadcrumbs[] = $this->title;
        }

	}

    protected function checkProject() {
        if (!$this->project or !$this->project) {
            throw new CHttpException(404, 'Страница не найдена.'); 
        }

        if ($this->project->user_id !== Yii::app()->user->id) {
            throw new CHttpException(404, 'Страница не найдена.'); 
        }
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

}
