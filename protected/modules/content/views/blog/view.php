<?php
$this->pageTitle = $model->title . ' | ' . Yii::t('ContentModule.blog', 'Blog') . ' | ' . Yii::app()->name;

$this->breadcrumbs=array(
	Yii::t('ContentModule.blog', 'Blog')=>array('index')
);

?>

<div id="blog">

<?php
	if (Yii::app()->user->checkAccess('blogEditor')){
		echo
			CHtml::tag('div', array('id'=>'post-controls'),
				CHtml::link(Yii::t('ContentModule.blog', 'Edit this post'), '/blog/edit/' . $model->id, array('id'=>'post-edit-link'))
			);
	}

	$this->renderPartial('_view', array('data'=>$model));
	$this->renderPartial('_commentView', array('data'=>$model));
?>

</div>