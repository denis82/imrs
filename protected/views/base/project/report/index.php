
<!--<a href="<?//= Yii::app()->urlManager->createUrl("project/report/docx", array('id' => $model->id)) ?>">Отчет в DOCX</a><br>-->
<!--<a href="<?//= Yii::app()->urlManager->createUrl("project/report/mydocx", array('id' => $model->id)) ?>">Отчет в DOCX test(не нажимать, все пыхнет!!)</a>-->
<div id="output">

<?php


$dir = Yii::app()->params['report']['path'] . '/';
$fileExists = file_exists ($dir . $domain->fileName . '.docx');

    switch ($domain->status) {
    case $done:  // 

        echo CHtml::form();

        echo CHtml::ajaxSubmitButton('Сформировать отчет в DOCX', '/project/ajax/index', array(
            'type' => 'POST',
            'data' => Yii::app()->request->csrfTokenName .'='.Yii::app()->request->csrfToken .'&id='.$model->id,
            'url' => '/project/ajax/index',
            'dataType' => 'html',
            'update' => '#output',

        ),
        array(
            'type' => 'submit',
            'class' => 'btn btn-large btn-primary startAuditButton',
            'confirm'=>"Вы уверенны что хотите это сделать?",
        ));

        echo CHtml::endForm();
        if($fileExists) {?>
            <a href="<?= '/project/report/addindex/' . $model->id ?>" >Скачать файл</a>
        <? } else {?>
                <p>Файла с отчетом нет, нажмите "Сформировать отчет в DOCX". Если ссылка не появится что-то пошло не так, обратитесь к техподдержке.</p>
        <? }
        
        
        break;
    case $wait:
        
        echo CHtml::form();

        echo CHtml::ajaxSubmitButton('Сформировать отчет в DOCX', '/project/ajax/index', array(
            'type' => 'POST',
            'data' => Yii::app()->request->csrfTokenName .'='.Yii::app()->request->csrfToken .'&id='.$model->id,
            'url' => '/project/ajax/index',
            'dataType' => 'html',
            'update' => '#output',
             
        ),
        array(
            'type' => 'submit',
            'class' => 'btn btn-large btn-primary startAuditButton',
            'confirm'=>"Вы уверенны что хотите это сделать?",
        ));
 
        echo CHtml::endForm();
        
        break;
    default:?>
       <div id="auditProcess">
            <img src="/html/assets/images/loading.gif" class="position-left" id="waitAudit" alt="" data="uploadAudit">
                <p>
                    Жди ты!
                </p>
        <div >
    <? break;
}?>

<div >

<!--
  -->
<script type="text/javascript">

function locs(id){

        if (0 != id) {
            document.location.href="";
        }
    }

$(document).ready(function(){

    
//     $('.startAuditButton').on('click', function(){
//         //console.log('data');
//         $.ajax({
//             type: 'POST',
//             //data: "key=rompeprop",
//             dataType: 'json',
//             url: '/project/ajax/index',
//             success: function(data){
//                 console.log(data);
//             }
//         });
//     
//     });
    
//     var las = 0;
//     if($('#waitAudit').attr('data')) {
//         las = $('#waitAudit').attr('data');
//     }

    //setTimeout("locs()", 120000);
    setTimeout("locs(" + $('#waitAudit').attr('data') + ")", 120000);
});
</script>
  
