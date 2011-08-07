<?php
    $this->pageTitle = Yii::t('ContentModule.forum', 'Ask a question') . ' | ' .
            Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;

    $this->breadcrumbs=array(
        Yii::t('ContentModule.forum', 'Forums')=>'/forum',
        Yii::t('ContentModule.forum', 'Support')=>'/forum/support',
        Yii::t('ContentModule.forum', 'Questions')=>'/forum/questions',
        Yii::t('ContentModule.forum', 'New question'),
    );
?>

<div id="forum">
    <div id="question">
        <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id'=>'forum-new-form'
            ));

            echo CHtml::label(Yii::t('ContentModule.forum', 'What\'s your question?'), 'Question_title', array('class'=>'h2')) .
                 CHtml::textField('Question[title]', '', array('class'=>'textField')) .
                 CHtml::label(Yii::t('ContentModule.forum', 'Describe your question, or the problem you\'re experiencing, in detail'), 'Question_content', array('class'=>'h3'));

            $this->widget('application.modules.content.extensions.tinymce.ETinyMce', array(
				'name'=>'Question[content]',
				'id'=>'Question_content',
				'height'=>'200px',
				'language'=>'ru',
                'useSwitch'=>false,
				'contentCSS'=>$this->module->cssAssetUrl . '/tinyContent.css',
				'plugins'=>array('spellchecker'),
                'useSwitch'=>false,
                'useCompression'=>false,
				'options'=>array(
					'theme'=>'advanced',
                    'skin'=>'o2k7',
                    'theme_advanced_toolbar_location'=>'bottom',
                    'theme_advanced_toolbar_align'=>'center',
                    'theme_advanced_buttons1'=>"bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,cleanup",
                    'theme_advanced_buttons2'=>'',
                    'theme_advanced_buttons3'=>''
				)
			));

            echo CHtml::tag('div',
                array('class'=>'top-10 text-right'),
                CHtml::submitButton(Yii::t('ContentModule.forum', 'Ask now'), array('class'=>'button primary'))
            );

            $this->endWidget();
        ?>
    </div>
</div>