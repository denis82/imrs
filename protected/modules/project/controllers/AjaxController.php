<?php

class AjaxController extends CController 
{
	// actionIndex вызывается всегда, когда action не указан явно.
	function actionIndex(){
		
            $id = Yii::app()->request->getPost('id');
            
            $modelProject = Project::model()->findByPk($id);
            $modelProject->status = 2;
            $successSaeve = $modelProject->save();

            ////////////////////////////////////  ПЕРЕДЕЛАТЬ  //////////////////////////////////////////////////////////////////
            $output = '<img src="/html/assets/images/loading.gif" class="position-left" id="waitAudit" alt="" data="uploadAudit">
            <p>
                Жди ты!
            </p>';
            if(!$successSaeve) {
                $output = '
                <p>
                    Процесс не запущен что-то пошло не так!
                </p>';
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            if(Yii::app()->request->isAjaxRequest){
                    echo $output;
                    // Завершаем приложение
                    Yii::app()->end();
            }
                        else {
                    // если запрос не асинхронный, отдаём форму полностью
                                $this->render('form', array(
                                        'input'=>$input,
                                        'output'=>$output,
                                ));
                        }
        }
    
}