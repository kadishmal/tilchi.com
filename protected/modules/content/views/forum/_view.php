<div class="item <?php echo Post::getTypeTitle($data->type); ?>"><?php
    if ($data->response_type > Post::RESPONSE_NEW){
        echo '<div class="status ' . Post::getResponseTitle($data->response_type) . '">' . Post::getResponseText($data->response_type, $data->type) . '</div>';
    }
?><div class="icon"><i></i><?php
        echo CHtml::tag('span', array('class'=>'answer', 'title'=>Yii::t('ContentModule.forum', '{n} vote|{n} votes', $data->votesCount)), '<i></i>' . $data->votesCount);
    ?></div><div class="info"><h3><?php echo CHtml::link($data->title, '/forum/' . $data->slug); ?></h3><span class="meta"><?php
            echo $data->author->first_name . ' ' . mb_substr($data->author->last_name, 0, 1, 'UTF-8') . '.' .
                CHtml::tag('span', array('class'=>'date', 'title'=>ContentModule::getFormattedFullDate($data->publish_date)),
                    '<i></i>' . ContentModule::getFormattedRelativeDate($data->publish_date)
                ) .
                CHtml::tag('span', array('class'=>'count', 'title'=>Yii::t('ContentModule.comment', '{n} comment|{n} comments', $data->commentsCount)), '<i></i>' . $data->commentsCount);
        ?></span></div></div>