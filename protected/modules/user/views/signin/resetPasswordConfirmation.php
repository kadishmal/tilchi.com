<?php
	$this->pageTitle = Yii::t('UserModule.login', 'Restore Password') . ' | ' . Yii::app()->name;
	$this->breadcrumbs=array(
		Yii::t('UserModule.login', 'Login')=>'/user/signin',
		Yii::t('UserModule.login', 'Restore Password'),
	);
?>

<div id="login" style="width:413px">
    <h1 class="text-center"><?php echo Yii::t('UserModule.login', 'Success!'); ?></h1>

    <p><?php echo Yii::t('UserModule.login', 'Congratulations! You have successfully reset your password. Now you can proceed to the login page.'); ?></p>

    <div class="form" style="text-align:center">
        <span class="button-blue"><?php
        echo CHtml::link(Yii::t('site', 'Login'), Yii::app()->user->loginUrl[0]);
    ?></span>
    </div>

</div>