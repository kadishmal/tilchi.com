<?php
	$this->pageTitle = Yii::t('site', 'Login') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('UserModule.login', 'Login'),
	);
?>

<div id="login">
    <h1 class="text-center"><?php echo Yii::t('UserModule.login', 'Login'); ?></h1>

    <p><?php echo Yii::t('UserModule.login', 'Login to your profile.'); ?></p>

    <div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'login-form',
        'enableAjaxValidation'=>true,
        'focus'=>array($model,'email'),
    )); ?>
        <?php echo $form->errorSummary($model); ?>

        <div class="row first"><?php echo CHtml::label('Email', 'LoginForm_email') . $form->textField($model, 'email'); ?></div>

        <div class="row last"><?php
            echo $form->label($model, 'password', array('class'=>'required')) .
            $form->passwordField($model,'password') . CHtml::submitButton(Yii::t('UserModule.login', 'Login'), array('id'=>'login-button'));
        ?></div>

        <div class="checkbox">
            <?php echo $form->checkBox($model, 'rememberMe'); ?><?php echo $form->label($model, 'rememberMe'); ?>
        </div>

        <div class="extra">
            <?php echo CHtml::link(Yii::t('UserModule.login', 'Not registered yet?'), '/user/register'); ?>
        </div>

    <?php $this->endWidget(); ?>
    </div><!-- form -->

</div>