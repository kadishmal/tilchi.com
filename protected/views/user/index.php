<?php
$this->menu=array(
	array('label'=>'Изменить мои данные', 'url'=>array('edit', 'c'=>'profile'), 'visible'=>!Yii::app()->user->isGuest),
	array('label'=>'Сменить мой пароль', 'url'=>array('edit', 'c'=>'pwd'), 'visible'=>!Yii::app()->user->isGuest),
);
?>

<h1><?php echo Yii::t('user', 'My account'); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'email',
		array(
			'label'=>$model->getAttributeLabel('password'),
			'value'=>'******',
		),
	),
)); ?>