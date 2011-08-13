<?php
$this->breadcrumbs=array(
	Yii::t('user', 'My Account')=>array('index'),
	$model->isNewRecord ? Yii::t('user', 'Add address') : Yii::t('user', 'Edit address'),
);

$this->menu=array(
	//array('label'=>'List User', 'url'=>array('index')),
	//array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1><?php echo $model->isNewRecord ? Yii::t('user', 'Add address') : Yii::t('user', 'Edit address'); ?></h1>

<?php echo $this->renderPartial('_formAddress', array('model'=>$model)); ?>