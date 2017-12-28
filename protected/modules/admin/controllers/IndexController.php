<?php

class IndexController extends CSiteController
{

    public $name = 'Администрирование';
    public $description = '';

    public function actionTplstaff() {
    	$staff = array();

    	$period_txt = array(
    		'once' => 'разово',
    		'day' => 'ежедневно',
    		'week' => 'еженедельно',
    		'month' => 'ежемесячно',
    		'quart' => 'ежеквартально',
    		'year' => 'ежегодно',
    	);

    	foreach (Staff::model()->findAll() as $el) {
    		$staff[] = array(
    			'id' => $el->id,
    			'name' => $el->name,
    			'price' => $el->price,
    		);
    	}

    	foreach (TplStaff::model()->findAllByAttributes(array('name' => $_POST['id'])) as $el) {
    		$tpl_staff[] = array(
    			'id' => $el->id,
    			'staff_id' => $el->staff_id,
    			'timer' => $el->timer,
    			'text' => $el->text,
    			'period' => $el->period,
    			'period_txt' => $period_txt[$el->period],
    			'multiple' => $el->multiple
    		);
    	}

        echo CJavaScript::jsonEncode(array(
        	'tpl' => $tpl_staff,
        	'staff' => $staff,
        ));

        Yii::app()->end();
    }

    public function actionTplstaffSave() {

    	foreach (TplStaff::model()->findAllByAttributes(array('name' => $_POST['id'])) as $el) {
    		$el->delete();
    	}

    	foreach ($_POST['params'] as $v) {
    		if ($v['timer'] and is_numeric($v['timer'])) {
    			$r = new TplStaff;
    			$r->name = $_POST['id'];
    			$r->staff_id = $v['staff'];
    			$r->timer = $v['timer'];
    			$r->period = $v['period'];
    			$r->multiple = intval($v['multiple']);
    			$r->text = $v['text'];
    			$r->save();
    		}
    	}

    	return $this->actionTplstaff();

    }

}
