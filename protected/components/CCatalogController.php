<?php

class CCatalogController extends CAdminController {

    public $includeRoot = false;
    public $root;

    public function actionIndex($id = null) {
        $this->title = $this->name;
        $this->description = $this->description;
        $this->breadcrumbs[] = $this->title;
        $type = $this->type;
        $typeElement = $this->typeElement;


        $tree = array();
        if ($this->includeRoot)
            $tree = $type::model()->findAll(array('order' => 'lft'));
        else
            $tree = $this->root->descendants()->findAll();

        if ($id) {
            $this->root = $type::model()->findByPk($id);
        }


        $list = array();
        $list = $typeElement::model()->findAllByAttributes(array('path' => $this->root->path));

        $this->render('catalog', array(
            'tree' => $tree,
            'list' => $list
        ));
    }

    public function actionTreeNew($id) {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->title = 'Добавить';
        $this->description = 'Создать новый элемент';
        $type = $this->type;
        $root = $type::model()->findByPk($id);
        $roots = $root->ancestors()->findAll();
        $roots[] = $root;
        foreach ($roots as $p => $r) {
            if ($p == 0)
                continue;
            $this->breadcrumbs[$r->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/treeupdate', array('id' => $r->id));
        }
        $this->breadcrumbs[] = $this->title;
        $model = new $type();
        if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            $root = $type::model()->findByPk($id);
            if (!isset($root))
                $root = $this->root;
            if ($model->appendTo($root)) {
                if (!isset($_POST["apply"])) {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index'));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/treeupdate', array('id' => $model->id)));
                }
            }
        }
        $this->render('form', array("model" => $model, 'formElements' => $this->getFormCategory($model)));
    }

    public function actionTreeUpdate($id) {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->description = 'Изменить текущий элемент';
        $type = $this->type;
        $root = $type::model()->findByPk($id);
        $roots = $root->ancestors()->findAll();
        foreach ($roots as $p => $r) {
            if ($p == 0)
                continue;
            $this->breadcrumbs[$r->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/treeupdate', array('id' => $r->id));
        }
        $model = $type::model()->findByPk($id);
        $this->breadcrumbs[] = $model->name;
        $this->title = $model->name;
        if (isset($_POST[$type])) {
            $model->attributes = $_POST[$type];
            if ($model->saveNode()) {
                if (!isset($_POST["apply"])) {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index'));
                } else {
                    $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/treeupdate', array('id' => $model->id)));
                }
            }
        }
        $this->render('form', array("model" => $model, 'formElements' => $this->getFormCategory($model)));
    }

    public function actionTreeRemove($id) {
        $type = $this->type;
        $model = $type::model()->findByPk($id);
        $model->deleteNode();
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index'));
        }
    }

    public function actionTreeSort($id, $prev, $next, $parent) {
        if (Yii::app()->request->isAjaxRequest) {
            $type = $this->type;
            $model = $type::model()->findByPk($id);
            if ($model) {
                if ($prev) {
                    $prev = $type::model()->findByPk($prev);
                    $model->moveAfter($prev);
                } elseif ($next) {
                    $next = $type::model()->findByPk($next);
                    $model->moveBefore($next);
                } elseif ($parent) {
                    $parent = $type::model()->findByPk($parent);
                    $model->moveAsFirst($parent);
                }
                Yii::app()->end();
            }
            Yii::app()->end();
        }
    }

    public function getTreeActions() {
        return array(
            array('url' => Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treenew', array('id' => $this->root->id)), 'label' => 'Добавить', 'icon' => 'plus', 'color' => 'green'),
        );
    }

    public function getItemTreeActions($item) {
        return array(
            array('url' => Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treenew', array('id' => $item->id)), 'label' => 'Добавить', 'icon' => 'plus', 'color' => 'green'),
            array('url' => Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treeupdate', array('id' => $item->id)), 'label' => 'Изменить', 'icon' => 'edit', 'color' => 'purple'),
            array('url' => Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treeremove', array('id' => $item->id)), 'label' => 'Удалить', 'icon' => 'trash', 'color' => 'red', 'class' => 'remove-element'),
        );
    }

    public function actionNew() {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $this->title = 'Добавить';
        $this->description = 'Создать новый элемент';
        $this->breadcrumbs[] = $this->title;
        $type = $this->typeElement;
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
        $this->render('form', array("model" => $model, 'formElements' => $this->getFormElement($model)));
    }

    public function actionUpdate($id) {
        $this->breadcrumbs[$this->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index');
        $typeSection = $this->type;
        $type = $this->typeElement;
        $model = $type::model()->findByPk($id);
        //TODO:Вынести в параметры имя поля 
        $root  = $typeSection::model()->findByPk($model->parent_id);
        $roots = $root->ancestors()->findAll();
        $roots[] = $root;
        foreach ($roots as $p => $r) {
            if ($p == 0)
                continue;
            $this->breadcrumbs[$r->name] = Yii::app()->createUrl($this->module->id . '/' . $this->id . '/index', array('id' => $r->id));
        }

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
        $this->render('form', array("model" => $model, 'formElements' => $this->getFormElement($model)));
    }

    public function actionRemove($id) {
        $type = $this->typeElement;
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

}

?>
