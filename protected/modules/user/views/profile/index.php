<?php
$this->menu=array(
	array('label'=>'Изменить мои данные', 'url'=>array('edit', 'c'=>'profile'), 'visible'=>!Yii::app()->user->isGuest),
	array('label'=>'Сменить мой пароль', 'url'=>array('edit', 'c'=>'pwd'), 'visible'=>!Yii::app()->user->isGuest),
);
?>
<div id="profile">
	<h1><?php echo Yii::t('UserModule.user', 'My profile'); ?></h1>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('first_name') ?></div>
		<div class="td"><b><?php echo $model->first_name . ' ' . $model->last_name; ?></b></div>
	</div>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('email') ?></div>
		<div class="td"><b><?php echo $model->email; ?></b></div>
	</div>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('password') ?></div>
		<div class="td"><?php echo '******'; ?></div>
	</div>
</div>