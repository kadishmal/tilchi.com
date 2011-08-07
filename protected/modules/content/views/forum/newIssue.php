<?php
    $this->pageTitle = Yii::t('ContentModule.forum', 'Ask a question') . ' | ' .
            Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;

    $this->breadcrumbs=array(
        Yii::t('ContentModule.forum', 'Forums')=>'/forum',
        Yii::t('ContentModule.forum', 'Issues')=>'/forum/issues',
        Yii::t('ContentModule.forum', 'New issue'),
    );
?>

<div id="forum">
    <div id="question">
        <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id'=>'forum-new-form'
            ));

            echo CHtml::label(Yii::t('ContentModule.forum', 'What issue did you find?'), 'Issue_title', array('class'=>'h2')) .
                 CHtml::textField('Issue[title]', '', array('class'=>'textField')) .
                 CHtml::label(Yii::t('ContentModule.forum', 'Tell us more details about the issue you\'ve encountered on our site Tilchi.com'), 'Issue_content', array('class'=>'h3'));

            $this->widget('application.modules.content.extensions.tinymce.ETinyMce', array(
				'name'=>'Issue[content]',
				'id'=>'Issue_content',
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
                CHtml::submitButton(Yii::t('ContentModule.blog', 'Publish'), array('class'=>'button primary'))
            );

            $this->endWidget();
        ?>
    </div>
</div>