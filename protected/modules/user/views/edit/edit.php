<?php
if ($model->id !== Yii::app()->user->id)
	$this->redirect(array('index'));
	
$this->breadcrumbs=array(
	'Мой аккаунт'=>array('index'),
	'Изменить',
);
/*
$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
);*/
?>

<h1>Изменить мои данные</h1>

<?php echo $this->renderPartial('_formEdit', array('model'=>$model)); ?>