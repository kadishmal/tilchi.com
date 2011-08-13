<?php
    $this->pageTitle = Yii::t('UserModule.user', 'My profile') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('UserModule.user', 'My profile')=>'/user',
		Yii::t('UserModule.user', 'Edit')
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
?>

<div id="profile">
    <h1 class="text-center"><?php echo Yii::t('UserModule.user', 'My profile'); ?></h1>
    <?php $this->renderPartial('_info', array('model'=>$model)); ?>
</div>