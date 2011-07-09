<?php
    $this->pageTitle = Yii::t('user', 'My personal info') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('site', 'Register'),
	);

	$cs = Yii::app()->clientScript;
    $cs->registerScript('info-form',"
        $('#info-form input[type=\'text\']').each(function(){
            var input = $(this);

            input.siblings('label').css('left', input.offset().left + input.outerWidth());
        })
        .focus(function(){
            $(this).siblings('label').show();
        })
        .blur(function(){
            var input = $(this);

            input.siblings('label').hide();
        });

        $('.errorBox').each(function(){
            var msg = $(this);
            msg.css('right', msg.parent().offset().left + msg.parent().outerWidth() + 20);
        });
    "
    );
	//$cs->registerCoreScript('jquery');
	//$cs->registerCssFile($cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css');
	//Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/site.js');
?>

<div id="register">
    <h1 class="text-center"><?php echo Yii::t('site', 'Create your profile'); ?></h1>
    <div id="progress-bar">
        <div class="bar first completed"><span><?php echo Yii::t('site', 'Register'); ?></span><i></i></div><div class="bar active"><span><?php echo Yii::t('user', 'My personal info'); ?></span><i></i></div><div class="bar last"><span><?php echo Yii::t('user', 'Add address'); ?></span></div>
    </div>
    <?php $this->renderPartial('_info', array('model'=>$model)); ?>
</div>