<?php
    echo CHtml::tag('div', array('style'=>'font-weight:bold;background:#3B5998;border-top:1px solid #3B5998;border-left:1px solid #3B5998;border-right:1px solid #3B5998;font-size:16px;width:604px;padding:5px 8px;color:#FFF'),
            'Tilchi.com'
    );

    echo CHtml::tag('div', array('style'=>'background:#FFF;border-bottom:1px solid #CCC;border-left:1px solid #CCC;border-right:1px solid #CCC;font-size:12px;width:580px;padding:20px'),
            Yii::t('ContentModule.forum', 'Hi,') . '<br /><br />' .
            Yii::t('ContentModule.forum', 'There is a new comment on the <b>forum_name</b> post.', array('forum_name'=>$title)) . '<br /><br />' .
            Yii::t('ContentModule.forum', 'user_name wrote: "message"', array('user_name'=>$comment['first_name'], 'message'=>$comment['content'])) . '<br /><br /><br />' .
            CHtml::tag('span', array('style'=>'background:#FFF9D7;border:1px solid #E2C822;padding:10px;margin:15px 0'),
                    CHtml::link(Yii::t('ContentModule.forum', 'See the comment thread'), $link, array('style'=>'color:#3B5998;text-decoration:none'))
            ),
            Yii::t('ContentModule.forum', 'Thanks') . '<br />' .
            Yii::t('ContentModule.forum', 'Tilchi.com Team')
    );
?>
