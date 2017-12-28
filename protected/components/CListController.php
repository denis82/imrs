<?php

class CListController extends CSiteController {

    public $order = "id asc";

    public function actionIndex(){
		//die('INDEX CONTROLLER');
	
        $this->title = $this->name;
        $this->description = $this->description;
        $this->breadcrumbs[] = $this->title;
        $criteria = new CDbCriteria();
        if (isset($this->order))
            $criteria->order = $this->order;
        if (Yii::app()->request->getParam($this->type . '_sort', "") != "") {
            $criteria->order = str_replace(".", " ", Yii::app()->request->getParam($this->type . '_sort'));
        }
        foreach ($this->baseFilters as $key => $value) {
            $criteria->condition .= (($criteria->condition) ? " and " : "") . "$key=:$key";
            $criteria->params["$key"] = $value;
        }
        $filtered = false;
        foreach ($this->filters as $key => $filter) {
            if (Yii::app()->request->getParam($key)) {
                $filtered = true;
                $criteria->condition .= (($criteria->condition) ? " and " : "") . "$key=:$key";
                $criteria->params["$key"] = Yii::app()->request->getParam($key);
            }
        }
        if (!$filtered) {
            foreach ($this->filters as $key => $filter) {
                if ($filter["default"]) {
                    $criteria->condition .= (($criteria->condition) ? " and " : "") . "$key=:$key";
                    $criteria->params["$key"] = $filter["value"];
                }
            }
        }
        $this->order = $criteria->order;
        $dataProvider = new CActiveDataProvider($this->type, array('pagination' => array('pageSize' => 10), 'criteria' => $criteria));
        $this->render('list', array('list' => $dataProvider));
    }

    public function actionNew() {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->title = 'Добавить';
        $this->description = 'Создать новый элемент';
        $this->breadcrumbs[] = $this->title;
        $type = $this->type;
        $model = new $type();
        if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            if ($model->save()) {
                if (!isset($_POST["apply"])) {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index'));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }
            }
        }
        $this->render('form', array("model" => $model, 'formElements' => $this->getForm($model)));
    }

    public function actionUpdate($id) {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $type = $this->type;
        $model = $type::model()->findByPk($id);
        $this->breadcrumbs[] = strip_tags($model->name);
        $this->title = strip_tags($model->name);
        $this->description = 'Изменить элемент';
        if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            if ($model->save()) {
                if (!isset($_POST["apply"])) {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index'));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/update', array('id' => $model->id)));
                }
            }
        }
        $this->render('form', array('model' => $model, 'formElements' => $this->getForm($model)));
    }

    public function actionRemove($id) {
        $type = $this->type;
        $model = $type::model()->findByPk($id);
        $model->delete();
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index'));
        }
    }

    public function getActions() {
        return array(
            array('url' => Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/new'), 'label' => 'Добавить', 'icon' => 'plus', 'color' => 'green')
        );
    }

    public function getColumns($columns = array()) {

        $columns[] =
                array(
                    'class' => 'CAdminButtonColumn',
                    'template' => '{update} {remove}',
                    'buttons' => array(
                        'update' => array(
                            'label' => 'Изменить',
                            'icon' => 'edit',
                            'color' => 'purple',
                            'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/update", array("id"=>$data->id))',
                        ),
                        'remove' => array(
                            'label' => 'Удалить',
                            'icon' => 'trash',
                            'color' => 'red',
                            'url' => 'Yii::app()->createUrl("' . $this->module->id . '/' . $this->id . '/remove", array("id"=>$data->id))',
                            'class' => 'remove-element',
                        ),
                    ),
                    'htmlOptions' => array('style' => 'width:170px;'),
        );
        return $columns;
    }

    public function getFilters() {
        return array();
    }

    public function getBaseFilters() {
        return array();
    }

}

