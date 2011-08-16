<?php
    $this->pageTitle = Yii::t('ContentModule.blog', 'Edit Post') . ' | ' .
            Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;

    $this->breadcrumbs=array(
        Yii::t('ContentModule.forum', 'Forums')=>'/forum',
        Yii::t('ContentModule.forum', 'Ideas')=>'/forum/ideas',
        Yii::t('ContentModule.blog', 'Edit Post'),
    );

	$this->renderPartial('_new', array(
		'model'=>$model,
		'title'=>Yii::t('ContentModule.forum', 'What\'s your idea?'),
		'description'=>Yii::t('ContentModule.forum', 'Tell us more details about your idea'),
		'type'=>'idea',
		'uctype'=>'Idea',
		'buttonText'=>Yii::t('ContentModule.blog', 'Publish')
	));
?>