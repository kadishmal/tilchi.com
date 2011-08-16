<?php
    $this->pageTitle = Yii::t('ContentModule.blog', 'Edit Post') . ' | ' .
            Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;

    $this->breadcrumbs=array(
        Yii::t('ContentModule.forum', 'Forums')=>'/forum',
        Yii::t('ContentModule.forum', 'Issues')=>'/forum/issues',
        Yii::t('ContentModule.blog', 'Edit Post'),
    );

	$this->renderPartial('_new', array(
		'model'=>$model,
		'title'=>Yii::t('ContentModule.forum', 'What issue did you find?'),
		'description'=>Yii::t('ContentModule.forum', 'Tell us more details about the issue you\'ve encountered on Tilchi.com'),
		'type'=>'issue',
		'uctype'=>'Issue',
		'buttonText'=>Yii::t('ContentModule.blog', 'Publish')
	));
?>