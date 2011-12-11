<?php
	$this->pageTitle = Yii::t('UserModule.login', 'Restore Password') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('UserModule.login', 'Login')=>'/user/signin',
		Yii::t('UserModule.login', 'Restore Password'),
	);
?>

<div id="login" style="width:413px">
    <h1 class="text-center"><?php echo Yii::t('UserModule.login', 'Email sent!'); ?></h1>

    <p><?php echo Yii::t('UserModule.login', 'Please check your inbox. We have sent an email regarding how to reset your password.<br /><br />You need to reset your password within 24 hours. Otherwise, you will have to request password reset again.'); ?></p>

    <div class="form" style="text-align:center">
        <span class="button-blue"><?php
        echo CHtml::link(Yii::t('UserModule.login', 'Go and check my inbox'), 'http://' . substr($model->email, strpos($model->email, '@') + 1), array('target'=>'_blank'));
    ?></span>
    </div>

</div>