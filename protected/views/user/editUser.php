<?php
$this->breadcrumbs=array(
	'Все пользователи'=>array('index'),
	'Изменить',
);

$this->menu=array(
	//array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Сменить пароль пользователя', 'url'=>array('editUser', 'c'=>'pwd', 'id'=>$model->id)),
	//array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id)),
	//array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1>Изменить данные пользователя #<?php echo $model->id; ?>: <b><?php echo $model->email; ?></b></h1>

<?php echo $this->renderPartial('_formEdit', array('model'=>$model)); ?>