<?

$tpl_name = explode('/', str_replace('.php', '', __FILE__));
$tpl_name = implode('/', array_slice($tpl_name, -3) );

$tpl = TplText::model()->findByAttributes(array('name' => $tpl_name));

if (!$tpl) {
	$tpl = new TplText;
	$tpl->name = $tpl_name;
	$tpl->save();
}

if ($tpl) {
	print '<div class="panel panel-flat"><div class="panel-body jEditablePanel" data-id="'. $tpl->id .'">'. $tpl->html .'</div></div>';
}

?>


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Добавить фразы</h5>

        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">

        <?php $f = $this->beginWidget('CActiveForm', array('htmlOptions' => array())); ?>

            <div class="form-group">
                <label><?= $form->getAttributeLabel('text') ?></label>
                <?php echo $f->textArea($form, 'text', array('class' => 'form-control', 'required' => 'required', 'rows' => 5)); ?>
            </div>                    

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Сохранить <i class="icon-arrow-right14 position-right"></i></button>
            </div>

        <?php $this->endWidget(); ?>

    </div>
</div>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Семантическое ядро сайта</h5>

        <div class="heading-elements">
            <span class="heading-text jLastUpdate">
            </span>

            <ul class="icons-list">
                <li><a data-action="reload"></a></li>
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>

    <div class="table-responsive jSemantic">

        <table class="table table-bordered table-striped datatable-complex-header">
            <thead>
                <tr>
                    <th rowspan="2">№</th>
                    <th rowspan="2">Ключевое слово/фраза</th>
                    <th rowspan="2">Частотность</th>
                    <th colspan="3" class="text-center">Посадочная</th>
                </tr>
                <tr>
                    <th>по содержанию</th>
                    <th>по пользователю</th>
                    <th>по Яндексу</th>
                </tr>
            </thead>

            <tbody>
                <? if ($words) {
                    foreach ($words as $n => $word) {
                        $stat  = $word->stat();

                        ?>
                        <tr id="word<?= $word->id ?>">
                            <td><?= $n+1 ?></td>
                            <td><?= $word->phrase ?></td>
                            <td class="stat <?= $stat ? '' : 'jEmpty' ?>"><?= $stat ? $stat : '<span class="icon-spinner4 spinner"></span>' ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?
                    }
                } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(function(){

        $('.datatable-complex-header').DataTable({
            autoWidth: false,
            searching: false,
            paging: false,
            info: false,
            columnDefs: [{ 
                orderable: false,
                targets: [ 0, 3, 4, 5 ]
            }],
            order: [[ 1, "asc" ]]
        });

        if ($('.jSemantic td.jEmpty').length) {

            var $table = $('.jSemantic');
            var data = { 'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>' };

            $.ajax({
                type: "POST",
                url: "<?= Yii::app()->urlManager->createUrl("project/positions/load", array('id' => $model->id, 'method' => 'semantic')) ?>",
                data: data,
                success: function(html){
                    try {
                        var data = JSON.parse(html);

                        if (data.stat && data.stat !== undefined) {
                            $.each(data.stat, function(j, i){
                                 console.log(j, i);
                                $('#word' + j + ' .jEmpty').html( i ).removeClass('jEmpty');
                            });
                        }

                        if ($('.jEmpty', $table).length) {
                            $.ajax(this);
                        }
                    } catch (e) {
                        $table.before( panelBody(html) ); 
                    }
                },
                error: function(xhr){

                    $('.jEmpty', $table).html('');

                    if (xhr.responseText !== undefined) {
                        $table.before( alertDanger(xhr.responseText) );
                    }
                    else {
                        $table.before( alertDanger("Информация недоступна в данный момент.") );
                    }

                },
                dataType: 'html'
            });                             

        }

        function alertDanger( text ) {
            return panelBody('<div class="alert alert-danger alert-styled-left alert-bordered">' + text + '</div>');
        }

        function panelBody( html ) {
            return '<div class="panel-body">' + html + '</div>';
        }

    });

</script>


