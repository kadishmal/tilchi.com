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
    <h1 class="text-center"><?php echo Yii::t('UserModule.login', 'Confirm Email'); ?></h1>

    <p><?php echo Yii::t('UserModule.login', 'To continue with your password reset, you need to confirm your email address.'); ?></p>

    <div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'restore-password-form',
        'enableAjaxValidation'=>true,
        'focus'=>array($model,'email'),
    )); ?>
        <?php echo $form->errorSummary($model); ?>

        <div class="row"><?php
            echo CHtml::label('Email', 'User_email') . $form->textField($model, 'email') .
                    CHtml::submitButton(Yii::t('UserModule.login', 'Continue'), array('id'=>'login-button'));
        ?></div>
    <?php $this->endWidget(); ?>
    </div><!-- form -->

</div>