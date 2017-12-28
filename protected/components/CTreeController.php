<?php

class CTreeController extends CAdminController {

    public $includeRoot = false;
    public $root;

    public function actionIndex() {
        $this->title = $this->name;
        $this->description = $this->description;
        $this->breadcrumbs[] = $this->title;
        $type = $this->type;
        $tree = array();
        if ($this->includeRoot)
            $tree = $type::model()->findAll(array('order' => 'lft'));
        else
            $tree = $this->root->descendants()->findAll();
        $this->render('tree', array(
            'tree' => $tree,
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
        $this->render('form', array("model" => $model, 'formElements' => $this->getForm($model)));
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
        $this->render('form', array("model" => $model, 'formElements' => $this->getForm($model)));
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
            array('url' => Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/treeupdate', array('id' => $this->root->id)), 'label' => 'Главная', 'icon' => 'edit', 'color' => 'purple'),
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

    public function getTreeLabel($element) {
        throw new CException('Переопределите метод');
    }

    public function getForm($element) {
        throw new CException('Переопределите метод');
    }

}

?>
