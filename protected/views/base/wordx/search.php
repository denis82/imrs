
<?/*div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Поиск</h5>

        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">
        <?php $f = $this->beginWidget('CActiveForm', array('htmlOptions' => array())); ?>

            <div class="form-group">
                <?php echo $f->textArea($form, 'text', array('class' => 'form-control', 'required' => 'required', 'rows' => 5)); ?>
            </div>                    

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Искать <i class="icon-arrow-right14 position-right"></i></button>
            </div>

        <?php $this->endWidget(); ?>
    </div>
</div*/?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Поиск</h5>

        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">
		<div id="result" style="height: 250px; border: 1px solid #ddd; padding: 10px; overflow: auto; margin-bottom: 15px;">
		</div>

    	<form id="searchForm" action="">
    	<div class="row">
    		<div class="col-lg-9 col-md-9">
    			<input type="text" class="form-control">
    		</div>
    		<div class="col-lg-2 col-md-2">
                <button type="submit" class="btn btn-primary">Искать <i class="icon-arrow-right14 position-right"></i></button>
    		</div>
    		<div class="col-lg-1 col-md-1">
    			<div style="font-size: 11px; line-height:11px;" id="stat">
    				q = <?= number_format($total['q'], 0, '', ' ') ?><br>
    				d = <?= number_format($total['d'], 0, '', ' ') ?><br>
    				d<sub>1</sub> = <?= number_format($total['d1'], 0, '', ' ') ?><br>
    				i = <?= number_format($total['o'], 0, '', ' ') ?><br>
    			</div>
    		</div>
    	</div>
    	</form>

    	<script type="text/javascript">

    	var askTime = null;
    	var askTimeOut = 10000;
    	var questionId = 0;

    	$(function(){

			var tts = new ya.speechkit.Tts({
				apikey: '6d1cc0b9-f9ba-4fa3-9690-8feb0cb83eef',// 'efe49eed-0ce0-4482-8c14-0cf141204bd9',
				emotion: 'good',
				speed: 1,
				speaker: 'oksana',
				lang: 'ru-RU'
			});

    		$('#searchForm').submit(function(){
    			clearTimeout(askTime);

    			var $this = $(this);

    			var t = $('input', $this).val();

    			$('#result').append('<b>' + ( (questionId == 0) ? 'Вопрос' : 'Ваш ответ') + ':</b> ' + $('input', $this).val() + '<br>' );

    			$("#result").stop(true, true).animate({ scrollTop: $('#result').prop("scrollHeight")}, 100);

    			$('input', $this).val('');

    			var data = { 'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>' };

    			data.phrase = t;
    			data.question = questionId;

    			questionId = 0;

    			$.post(
    				'/wordx/index/load?method=question', data,
    				function( r ){
    					$('#result').append('<b>Ответ:</b> ' + r + '<br>');
		    			$("#result").stop(true, true).animate({ scrollTop: $('#result').prop("scrollHeight")}, 100);

						tts.speak(r.replace(/<\/?[^>]+(>|$)/g, ""));
		    			getStatistic();

		    			clearTimeout(askTime);
		    			askTime = setTimeout(getQuestion, askTimeOut);
    				}
    			);

    			$.post(
    				'/wordx/index/load?method=theory', data,
    				function( r ){
    					$('#theory').html( r );
    				}
    			);

    			return false;
    		});

    		$('#searchForm input').on('keyup', function(){
    			clearTimeout(askTime);
	    		askTime = setTimeout(getQuestion, askTimeOut);
    		});

    		askTime = setTimeout(getQuestion, askTimeOut);

	    	function getStatistic() {
				var data = { 'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>' };

				$.post(
					'/wordx/index/load?method=statistic', data,
					function( r ){
						$('#stat').html( r );
					}
				);
	    	}

	    	function getQuestion() {
    			clearTimeout(askTime);

				var data = { 'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>' };

				$.post(
					'/wordx/index/load?method=getquestion', data,
					function( r ){
						questionId = r.id;

						if (questionId > 0) {
							$('#result').append('<b>Вопрос системы:</b> ' + r.html + '<br>');
						}
						else {
							$('#result').append('<b>У нас кончились вопросы. Задавайте вопрос.</b><br>');
						}

		    			$("#result").stop(true, true).animate({ scrollTop: $('#result').prop("scrollHeight")}, 100);

						tts.speak(r.html.replace(/<\/?[^>]+(>|$)/g, ""));

		    			clearTimeout(askTime);
		    			askTime = setTimeout(getPing, askTimeOut * 5);
					},
					'json'
				);
	    	}

	    	function getPing() {
	    		if (questionId > 0) {
					$('#result').append('<b>Отвечай скорее!</b><br>');
					$("#result").stop(true, true).animate({ scrollTop: $('#result').prop("scrollHeight")}, 100);

					tts.speak('Отвечай скорее!', {emotion: 'evil', speaker: 'levitan'});
	    		}
	    	}

    	});
    	</script>
    </div>
</div>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Теории</h5>

        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">
		<div id="theory">
		</div>
    </div>
</div>

<?/*div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Результат поиска</h5>
    </div>

    <div class="panel-body">
    	<?= $form->search() ?>
    </div>
</div*/?>

