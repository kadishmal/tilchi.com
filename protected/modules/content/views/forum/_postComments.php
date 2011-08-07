<div class="comments">
    <?php
    foreach ($data->comments as $comment)
    {
        echo '<div class="comment' . ($comment->status == Comment::STATUS_PENDING ? ' pending' : '') . '" id="comment-' . $comment->id . '">' .
            '<div class="author">' .
                '<img class="avatar" src="' . $this->module->cssAssetUrl . '/' . ($comment->user->avatar > 0 ? $comment->user->avatar : '') . 'user.png" />' .
                '<div class="name">';
                if ($comment->user_id){
                    echo $comment->user->first_name . ' ' . mb_substr($comment->user->last_name, 0, 1, 'UTF-8') . '.';
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