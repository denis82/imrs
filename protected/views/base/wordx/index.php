
<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Текст на проверку</h5>

        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">

        <?php $f = $this->beginWidget('CActiveForm', array('htmlOptions' => array())); ?>

            <div class="form-group">
                <?/*label><?= $form->getAttributeLabel('text') ?></label*/?>
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
        <h5 class="panel-title text-semiold">Результат проверки</h5>
    </div>

    <div class="panel-body">
    	<?= $form->check() ?>
    </div>
</div>

<?/*
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


*/?>