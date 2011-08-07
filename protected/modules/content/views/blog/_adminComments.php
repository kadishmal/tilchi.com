<?php
echo '<div class="comment' . ($data->status != Comment::STATUS_APPROVED ? ' pending' : '') . '" id="comment-' . $data->id . '">' .
        '<div class="meta">' .
            '<span class="author">' .
                '<span class="avatar"><i></i></span>' .
                '<span class="name">';
                if ($data->user_id){
                    echo $data->user->first_name . ' ' . mb_substr($data->user->last_name, 0, 1, 'UTF-8') . '.';
                }
                else{
                    echo $data->author;
                }
           echo '</span>' .
            '</span>' .
            '<span class="date">' . CHtml::link(ContentModule::getFormattedRelativeDate($data->date), '/blog/' . $data->post->slug . '/#comment-' . $data->id, array('title'=>ContentModule::getFormattedFullDate($data->date))) . '</span>' .
            '<span class="actions">' .
               ($data->status == Comment::STATUS_APPROVED ?
               '<a class="disapprove" href="/content/comment/disapprove/' . $data->id .'">' . Yii::t('ContentModule.comment', 'Disapprove') . '</a>'
               : '<a class="approve" href="/content/comment/approve/' . $data->id .'">' . Yii::t('ContentModule.comment', 'Approve') . '</a>' ) .
               '<a class="trash" href="/content/comment/' .
                   ($_GET['param'] == 'trash' ? 'delete' : 'trash') . '/' .
               $data->id .'">' . Yii::t('ContentModule.comment', 'Trash it') . '</a>' .
            '</span>' .
       '</div>' .
       '<div class="message">' . $data->content . '</div>';
echo '</div>';
?>