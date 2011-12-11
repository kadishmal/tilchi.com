<?php
	$this->pageTitle = Yii::t('UserModule.login', 'Restore Password') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('UserModule.login', 'Login')=>'/user/signin',
		Yii::t('UserModule.login', 'Restore Password'),
	);

	Yii::app()->clientScript->registerScript('login-button',"
		$('#login-form').submit(function(){
			$('#login-button').attr('disabled','disabled');
		});
	");
?>

<div id="login" style="width:413px">
    <h1 class="text-center"><?php echo Yii::t('UserModule.login', 'Enter new password'); ?></h1>

    <p><?php echo Yii::t('UserModule.login', 'Now enter your new password and confirm.'); ?></p>

    <div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'reset-password-form',
        'enableAjaxValidation'=>true,
        'focus'=>array($model,'passwordNew'),
    )); ?>
        <?php echo $form->errorSummary($model); ?>
        <?php echo $form->hiddenField($model, 'email'); ?>
        <?php echo CHtml::hiddenField('reset'); ?>

        <div class="row first"><?php
            echo CHtml::label(Yii::t('UserModule.login', 'New'), 'User_passwordNew') . $form->passwordField($model, 'passwordNew');
        ?></div>
        <div class="row last"><?php
            echo CHtml::label(Yii::t('UserModule.login', 'Repeat'), 'User_passwordNew_repeat') . $form->passwordField($model, 'passwordNew_repeat') .
                CHtml::submitButton(Yii::t('UserModule.login', 'Confirm'), array('id'=>'login-button'));
            ?></div>
    <?php $this->endWidget(); ?>
    </div><!-- form -->

</div>