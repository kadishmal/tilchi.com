<?php
    $this->pageTitle = Yii::t('ContentModule.forum', 'Ask a question') . ' | ' .
            Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;

    $this->breadcrumbs=array(
        Yii::t('ContentModule.forum', 'Forums')=>'/forum',
        Yii::t('ContentModule.forum', 'Support')=>'/forum/support',
        Yii::t('ContentModule.forum', 'Questions')=>'/forum/questions',
        Yii::t('ContentModule.forum', 'New question'),
    );

	$this->renderPartial('_new', array(
		'model'=>$model,
		'title'=>Yii::t('ContentModule.forum', 'What\'s your question?'),
		'description'=>Yii::t('ContentModule.forum', 'Describe your question, or the problem you\'re experiencing, in detail'),
		'type'=>'question',
		'uctype'=>'Question',
		'buttonText'=>Yii::t('ContentModule.blog', 'Publish')
	));
?>