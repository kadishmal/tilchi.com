<?php
$this->breadcrumbs=array(
	'Пользователи',
);

$this->menu=array(
	//array('label'=>'Добавить нового пользователя', 'url'=>array('create')),
	//array('label'=>'Управление пользователями', 'url'=>array('admin')),
);
?>

<h1>Список всех пользователей</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
