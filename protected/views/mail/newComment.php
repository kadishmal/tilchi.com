<?php
    echo CHtml::tag('div', array('style'=>'background:#F4F4F4;padding:8px;margin-bottom:15px;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px;'),
        CHtml::tag('div', array('style'=>'margin:8px 0 10px 10px;font-size:24px;color:#555'), 'Tilchi.com') .
        CHtml::tag('div', array('style'=>'border-radius:6px;-moz-border-radius:6px;-webkit-border-radius: 6px;border:1px solid #E9E9E9;background:#FFF;padding:15px 15px 20px 20px;'),
            Yii::t('ContentModule.forum', 'Hi,') . '<br /><br />' .
            Yii::t('ContentModule.' . $type, 'There is a new comment on the "<b>_title</b>" post.', array('_title'=>$comment->post->title)) . '<br /><br />' .
            Yii::t('ContentModule.comment', 'user_name wrote:', array($comment->user->gender, 'user_name'=>$comment->user->first_name)) .
            CHtml::tag('blockquote', array('style'=>'padding:0 0 0 15px;margin:10px 0 18px;border-left:5px solid #EEE;font-size:16px;font-weight:300;line-height:22.5px;'), $comment->content) .
            CHtml::tag('span', array('style'=>''),
                CHtml::link(Yii::t('ContentModule.forum', 'See the comment thread'), $link, array('style'=>''))
            ) . '<br /><br />' .
            Yii::t('ContentModule.forum', 'Respectfully Yours,<br />Tilchi team.')
        )
    );
?>
