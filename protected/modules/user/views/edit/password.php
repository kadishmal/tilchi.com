<?php
	$this->pageTitle = Yii::t('UserModule.user', 'Change my password') . ' | ' . Yii::t('UserModule.user', 'My profile') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('UserModule.user', 'My profile')=>'/user',
		Yii::t('UserModule.user', 'Change my password')
	);

	$cs = Yii::app()->clientScript;
    $cs->registerScript('password-form',"
        $('#password-form input:password').each(function(){
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
    "
    );
?>
<div id="profile" class="form">
	<h1><?php echo Yii::t('UserModule.user', 'Change my password'); ?></h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'password-form',
	'enableAjaxValidation'=>false,
	'focus'=>array($model,'password_repeat'),
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row password-old"><i></i><?php
		echo CHtml::label(
                    Yii::t('UserModule.user', 'Enter your <b>old password</b>.') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'password_repeat', array('class'=>'required')) .
            $form->passwordField($model,'password_repeat');
	?></div>

	<div class="row password"><i></i><?php
		echo CHtml::label(
                    Yii::t('UserModule.user', 'Enter a <b>new password</b>.') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'passwordNew', array('class'=>'required')) .
            $form->passwordField($model,'passwordNew');
	?></div>

	<div class="row password"><i></i><?php
		echo CHtml::label(
                    Yii::t('UserModule.user', 'Repeat your <b>new password</b>.') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'passwordNew_repeat', array('class'=>'required')) .
            $form->passwordField($model,'passwordNew_repeat');
	?></div>

	<div class="major-buttons text-right">
        <?php echo CHtml::link(Yii::t('UserModule.user', 'Cancel'), '/user/profile', array('class'=>'link-button mRight10', 'tabindex'=>'-1')) .
				CHtml::submitButton(Yii::t('UserModule.user', 'Save'), array('class'=>'button primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->