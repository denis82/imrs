
<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Поиск</h5>

        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>

    </div>
    <div class="panel-body" id="inputPanel">
        <?php $f = $this->beginWidget('CActiveForm', array('htmlOptions' => array())); ?>

            <div class="form-group">
                <?php echo $f->textArea($form, 'text', array('class' => 'form-control', 'required' => 'required', 'rows' => 5)); ?>
            </div>                    

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Искать <i class="icon-arrow-right14 position-right"></i></button>
            </div>

        <?php $this->endWidget(); ?>

        <div id="yaspeech"></div>

        <script type="text/javascript">
		window.onload = function () {
		    var textline = new ya.speechkit.Textline('yaspeech', {
		        apikey: '6d1cc0b9-f9ba-4fa3-9690-8feb0cb83eef',
		        onInputFinished: function(text) {
		        	$('#inputPanel').val( $('#inputPanel').val() + text );
		        }
		    });
		};
        </script>
    </div>
</div>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Результат поиска</h5>
    </div>

    <div class="panel-body">
    	<?= $form->search() ?>
    </div>
</div>

