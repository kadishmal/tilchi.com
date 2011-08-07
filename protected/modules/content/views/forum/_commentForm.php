<div class="comment-form">
    <?php
    if (Yii::app()->user->isGuest)
    {
        echo CHtml::tag('p', array('id'=>'comment-form'), Yii::t('ContentModule.comment', 'In order to leave a comment you need to be <a href="/site/login">logged in</a>. If you are not our member yet, <a href="/user/register">join us</a> and share your thoughts with other member of our community!'));
    }
    else{
        $form = $this->beginWidget('CActiveForm', array(
            'id'=>'comment-form',
            'action'=>'/content/comment/new'
        ));

        echo $form->errorSummary($data);

        echo $form->hiddenField($data, 'id', array('id'=>'Comment_post_id', 'name'=>'Comment[post_id]'));

        $this->widget('application.modules.content.extensions.tinymce.ETinyMce', array(
            'name'=>'Comment[content]',
            'id'=>'Comment_content',
            'language'=>'ru',
            'height'=>'100px',
            'contentCSS'=>$this->module->cssAssetUrl . '/tinyContent.css',
            'plugins'=>array('spellchecker'),
            'useSwitch'=>false,
            'useCompression'=>false,
            'options'=>array(
                'theme'=>'advanced',
                'skin'=>'o2k7',
                'theme_advanced_toolbar_location'=>'top',
                'theme_advanced_toolbar_align'=>'left',
                'theme_advanced_buttons1'=>"bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code",
                'theme_advanced_buttons2'=>'',
                'theme_advanced_buttons3'=>''
            )
        ));

        echo CHtml::tag('div', array('class'=>'row first text-right'),
            CHtml::submitButton(Yii::t('ContentModule.comment', 'Publish'), array('class'=>'button primary'))
        );

        $this->endWidget();
    }
    ?>
</div>