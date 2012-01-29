<div class="comments">
    <?php
    foreach ($data->comments as $comment)
    {
        echo '<div class="comment' . ($comment->status == Comment::STATUS_PENDING ? ' pending' : '') . '" id="comment-' . $comment->id . '">' .
            '<div class="author">' .
                '<img class="avatar" src="' . $comment->user->getGravatar(80) . '" />' .
                '<div class="name">';
                if ($comment->user_id){
                    echo $comment->user->getName();
                }
                else{
                    echo $comment->author;
                }
           echo '</div>' .
            '</div>' .
            '<div class="comment-body">' .
                '<div class="message">' . $comment->content . '</div>' .
                '<span class="date">' . CHtml::link(ContentModule::getFormattedRelativeDate($comment->date), '/forum/' . $data->slug . '/#comment-' . $comment->id, array('title'=>ContentModule::getFormattedFullDate($comment->date))) . '</span>' .
            '</div>';
        echo '</div>';
    }
    ?>
</div>