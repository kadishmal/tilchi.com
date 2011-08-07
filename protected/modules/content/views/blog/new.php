<?php
$this->breadcrumbs=array(
	Yii::t('ContentModule.blog', 'Blog')=>array('/blog'),
	Yii::t('ContentModule.blog', 'New Post'),
);

Yii::app()->clientScript->registerScript('edit-post', "
	setLocalText('blog', 'Published on', '" . Yii::t('ContentModule.blog', 'Published on') . "');
");

?>

<div id="blog">
	<div class="post draft"></div>
	<div id="edit-post">
	<h1><i></i><?php echo Yii::t('ContentModule.blog', 'Add New Post'); ?></h1>
	<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
	</div>
</div>