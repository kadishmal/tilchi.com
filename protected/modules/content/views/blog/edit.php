<?php
$this->pageTitle = Yii::t('ContentModule.blog', 'Edit Post') . ' | ' . Yii::app()->name;

$this->breadcrumbs=array(
	Yii::t('ContentModule.blog', 'Blog')=>array('index'),
	Yii::t('ContentModule.blog', 'Edit Post')
);

Yii::app()->clientScript->registerScript('edit-post', "
	setLocalText('blog', 'Published on', '" . Yii::t('ContentModule.blog', 'Published on') . "');
");

/*$this->menu=array(
	array('label'=>'Manage Post', 'url'=>array('admin')),
);*/
?>

<div id="blog">
	<div class="post draft"></div>
	<div id="edit-post">
		<h1><i></i><?php echo Yii::t('ContentModule.blog', 'Edit Post'); ?></h1>
		<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
	</div>
</div>