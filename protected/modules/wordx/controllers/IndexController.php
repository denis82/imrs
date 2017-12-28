<?php

class IndexController extends CSiteController
{

    public $name = '';
    public $description = '';

    public function actionIndex(){

        $form = new CheckForm;
        if (isset($_POST['CheckForm'])) {
            $form->attributes = $_POST['CheckForm'];

            /*if ($form->save()) {
                $this->redirect(Yii::app()->urlManager->createUrl('project/positions/semantic', array('id' => $model->id)));
                Yii::app()->end();
            }*/
        }

        $this->render('wordx.index', array(
            "form" => $form,
        ));

    }

    public function actionSearch(){

        $this->clientScript->registerScriptFile('https://webasr.yandex.net/jsapi/v1/webspeechkit.js');
        $this->clientScript->registerScriptFile('https://webasr.yandex.net/jsapi/v1/webspeechkit-settings.js');

        $form = new CheckForm;
        if (isset($_POST['CheckForm'])) {
            $form->attributes = $_POST['CheckForm'];
        }

        $this->render('wordx.search', array(
        	"total" => array(
        		"q" => Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_search')->queryScalar(),
        		"d" => Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_word')->queryScalar(),
        		"d1" =>Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_form')->queryScalar(),
        		"o" => Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_chain')->queryScalar(),
        	),

            "form" => $form,
        ));

    }

    public function actionSpeech(){

        $this->clientScript->registerScriptFile('https://webasr.yandex.net/jsapi/v1/webspeechkit.js');

        $form = new CheckForm;
        if (isset($_POST['CheckForm'])) {
            $form->attributes = $_POST['CheckForm'];
        }

        $this->render('wordx.speech', array(
            "form" => $form,
        ));

    }

    public function actionLoad( $method )
    {
    	$error_code = 423;

    	if (Yii::app()->request->isAjaxRequest) {
			$action = 'load' . $method;
			if (method_exists($this, $action)) {
				return $this->$action( $model );
			}
			else {
				$error_code = 404;
			}
    	}

    	$this->renderPartial('//error/load/' . $error_code);

    	Yii::app()->end();
    }

    private function loadQuestion() {
    	$_POST['CheckForm']['text'] = $_POST['phrase'];
    	$_POST['CheckForm']['search_id'] = $_POST['question'];

        $form = new CheckForm;
        $form->attributes = $_POST['CheckForm'];
        print $form->ask();

    	Yii::app()->end();
    }

    private function loadGetQuestion() {

    	if ($ws = WordxSearch::model()->find(array('order' => 'qty desc, rand()'))) {
	        echo CJavaScript::jsonEncode(array(
	        	'id' => $ws->id,
	            'html' => $ws->phrase
	        ));
    	}
    	else {
	        echo CJavaScript::jsonEncode(array(
	        	'id' => 0,
	            'html' => ''
	        ));
    	}

    	Yii::app()->end();
    }

    private function loadStatistic() {

    	$total = array(
    		"q" => Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_search')->queryScalar(),
    		"d" => Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_word')->queryScalar(),
    		"d1" =>Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_form')->queryScalar(),
    		"o" => Yii::app()->db->createCommand()->select('count(*)')->from('tbl_wordx_chain')->queryScalar(),
    	);

    	?>

    				q = <?= number_format($total['q'], 0, '', ' ') ?><br>
    				d = <?= number_format($total['d'], 0, '', ' ') ?><br>
    				d<sub>1</sub> = <?= number_format($total['d1'], 0, '', ' ') ?><br>
    				i = <?= number_format($total['o'], 0, '', ' ') ?><br>

    	<?

    	Yii::app()->end();
    }

    private function loadTheory() {
    	$_POST['CheckForm']['text'] = $_POST['phrase'];
    	$_POST['CheckForm']['search_id'] = $_POST['question'];

        $form = new CheckForm;
        $form->attributes = $_POST['CheckForm'];
        print $form->theory();

    	Yii::app()->end();
    }

}
