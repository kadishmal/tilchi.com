<?php
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery.ui');
$cs->registerScript('filter-comments',
"activateCommentForm();"
);
?>

<div class="post-comments" id="comments">
    <h2><?php echo Yii::t('ContentModule.comment', 'Comments'); ?></h2>
    <?php
    echo CHtml::tag('div', array('class'=>'row row-last text-right', 'id'=>'button-leave-comment'),
                CHtml::tag('span',array('class'=>'button primary'), Yii::t('ContentModule.comment', 'Leave a comment'))
                );

    if (Yii::app()->user->isGuest){
        echo CHtml::tag('p', array('id'=>'comment-form'), Yii::t('ContentModule.comment', 'In order to leave a comment you need to be <a href="/site/login">logged in</a>. If you are not our member yet, <a href="/user/register">join us</a> and share your thoughts with other member of our community!'));
    }
    else{
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>'comment-form',
            'enableClientValidation'=>true,
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

        echo CHtml::tag('div', array('class'=>'row row-last text-right'),
                    CHtml::tag('span', array('class'=>'link-button button-cancel-comment'), Yii::t('ContentModule.comment', 'Cancel')) .
                    CHtml::submitButton(Yii::t('ContentModule.comment', 'Publish'), array('class'=>'button primary'))
                    );

        //echo CHtml::hiddenField('Comment[post_id]', $data->id);

        $this->endWidget();
    }
    // Each indent is 30px
    $indent = 30;
    // This array will hold values which indicate how many times to indent
    // the current comment. Ex. 0, 1, 2, ...
    $indentArray = array();
    // root comments do not need to have any indentation, so when +1
    // their indent value should be 0. This is why the first value in the
    // array is -1.
    $indentArray[0] = -1;

    foreach ($data->comments as $comment)
    {
        echo '<div class="comment' . ($comment->status == Comment::STATUS_PENDING ? ' pending' : '') . '" id="comment-' . $comment->id . '" style="padding-left:';

        $indentArray[$comment->id] = $indentArray[$comment->parent_id] + 1;

        echo $indentArray[$comment->id] * $indent . 'px">' .
                '<div class="meta">' .
                    '<span class="author">' .
                        '<span class="avatar"><i></i></span>' .
                        '<span class="name">';
                        if ($comment->user_id){
                            echo $comment->user->first_name . ' ' . mb_substr($comment->user->last_name, 0, 1, 'UTF-8') . '.';
                        }
                        else{
                            echo $comment->author;
                        }
                   echo '</span>' .
                    '</span>' .
                    '<span class="date">' . CHtml::link(ContentModule::getFormattedRelativeDate($comment->date), '/blog/' . $data->slug . '/#comment-' . $comment->id, array('title'=>ContentModule::getFormattedFullDate($comment->date))) . '</span>' .
                    ($comment->status == Comment::STATUS_PENDING ? '<span class="msg" title="' . Yii::t('ContentModule.blog', 'Your comment is pending moderation. At this moment it is visible only to you and administrators.') . '"><i></i>' . Yii::t('ContentModule.blog', 'Pending moderation...') . '</span>' : '') .
                    '<span class="actions">';

                    if ($comment->user_id == Yii::app()->user->id){
                        echo '<a class="trash" href="/content/comment/delete/' . $comment->id .'">' . Yii::t('ContentModule.comment', 'Trash it') . '</a>';
                    }

               echo '</span>' .
               '</div>' .
               '<div class="message">' . $comment->content . '</div>' .
               '<div class="text-right">' .
               CHtml::tag('span', array('class'=>'button reply-button'), Yii::t('ContentModule.comment', 'Reply')) .
               '</div>';
        echo '</div>';
    }
    ?>
</div>