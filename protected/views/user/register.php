<?php
    $this->pageTitle = Yii::t('site', 'Register') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('site', 'Register'),
	);

	$cs = Yii::app()->clientScript;
    $cs->registerScript('registration-form',"
        $('#registration-form input[type=\'text\'],#registration-form input[type=\'password\']').each(function(){
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
?>

<div id="register">
    <h1 class="text-center"><?php echo Yii::t('site', 'Create your profile'); ?></h1>
    <?php $this->renderPartial('_register', array('model'=>$model)); ?>
</div>