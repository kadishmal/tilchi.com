<?php
class SendEmailCommand extends CConsoleCommand
{
    public function actionRegisteredUsers()
    {
        $amqp = Yii::app()->amqp;

        if ($amqp->declareQueue(RegisterController::QUEUE_REGISTERED_USERS, AMQP_DURABLE) > 0)
        {
            $queue = $amqp->queue(RegisterController::QUEUE_REGISTERED_USERS);
            $queue->bind(RegisterController::EXCHANGE_REGISTERED_USERS, RegisterController::QUEUE_REGISTERED_USERS);

            while ($queueMessage = $queue->get(AMQP_NOACK))
            {
                // stop the loop if there are no more messages
                if ($queueMessage['count'] < 0)
                {
                    break;
                }

                $user = User::model()->findByPk($queueMessage['msg']);

                if ($user)
                {
                    $message = New YiiMailMessage;
                    $message->view = 'newUser';

                    $message->subject = Yii::t('UserModule.register', 'You account details at Tilchi.com Online Dictionary site.');

                    $message->setBody(array(
                        'name'=>$user->first_name
                    ), 'text/html');

                    $message->setFrom('noreply@tilchi.com', 'Tilchi.com');
                    $message->setTo($user->email, $user->first_name . ' ' . $user->last_name);

                    if (Yii::app()->mail->send($message))
                    {
                        $queue->ack($queueMessage['delivery_tag']);
                    }
                }
            }
        }
    }

    public function actionComments()
    {
        $amqp = Yii::app()->amqp;

        if ($amqp->declareQueue(CommentController::QUEUE_COMMENTS_NEW, AMQP_DURABLE) > 0)
        {
            $queue = $amqp->queue(CommentController::QUEUE_COMMENTS_NEW);
            $queue->bind(CommentController::EXCHANGE_COMMENTS_NEW, CommentController::QUEUE_COMMENTS_NEW);

            while ($queueMessage = $queue->get(AMQP_NOACK))
            {
                // stop the loop if there are no more messages
                if ($queueMessage['count'] < 0)
                {
                    break;
                }

                $comment = Comment::model()->with('post', 'user')->findByPk($queueMessage['msg']);

                if ($comment)
                {
                    $type = Post::getTypeTitle($comment->post->type);

                    $subscrPostCommentsDefault = SiteSettings::model()->cache(2592000)->find(array(
                        'select'=>'id, default_value',
                        'condition'=>'module = :module AND name = :name',
                        'params'=>array(':module'=>'user.profile.notifications', ':name'=>'subscr_post_comments')
                    ));

                    if ($comment->user_id != $comment->post->user_id)
                    {
                        $userSetting = UserSettings::model()->find(array(
                            'select'=>'value',
                            'condition'=>'user_id = :user_id AND setting_id = :setting_id',
                            'params'=>array(':user_id'=>$comment->post->user_id, ':setting_id'=>$subscrPostCommentsDefault->id)
                        ));

                        if (($userSetting && $userSetting->value == SiteSettings::YES)
                            || (!$userSetting && $subscrPostCommentsDefault->default_value == SiteSettings::YES))
                        {
                            $message = New YiiMailMessage;
                            $message->view = 'newComment';
                            $message->setFrom('noreply@tilchi.com', 'Tilchi.com');

                            $message->setBody(array(
                                'comment'=>$comment,
                                'type'=>$type,
                                'link'=>'http://tilchi.com/' . Post::getGlobalTypeTitle($comment->post->type) . '/' . $comment->post->slug . '#comment-' . $comment->id
                            ), 'text/html');

                            $message->setTo($comment->post->author->email, $comment->post->author->getName());
                            $message->subject = Yii::t('ContentModule.comment', 'first_name commented on your_type: "post_title"',
                                array(
                                    $comment->user->gender,
                                    'first_name'=>$comment->user->first_name,
                                    'your_type'=>Yii::t('ContentModule.comment', 'your ' . $type),
                                    'post_title'=>$comment->post->title,
                                )
                            );

                            if (Yii::app()->mail->send($message))
                            {
                                $queue->ack($queueMessage['delivery_tag']);
                            }
                        }
                    }

                    // used to quote column names
                    $db = Yii::app()->db;

                    // now get all users who have previously commented on the same post,
                    // except the current commenter
                    $prevComments = Comment::model()->with('user')->findAll(array(
                        'select'=>'user_id',
                        'with'=>'user',
                        'condition'=>'post_id = :post_id AND '. $db->quoteColumnName('order') . ' < :order AND user_id <> :user_id AND user_id <> :author_id',
                        'params'=>array(':post_id'=>$comment->post_id, ':order'=>$comment->order, ':user_id'=>$comment->user_id, ':author_id'=>$comment->post->user_id)
                    ));

                    if (count($prevComments))
                    {
                        $message = New YiiMailMessage;
                        $message->view = 'newComment';
                        $message->setFrom('noreply@tilchi.com', 'Tilchi.com');

                        $message->setBody(array(
                            'comment'=>$comment,
                            'type'=>$type,
                            'link'=>'http://tilchi.com/' . Post::getGlobalTypeTitle($comment->post->type) . '/' . $comment->post->slug . '#comment-' . $comment->id
                        ), 'text/html');

                        foreach($prevComments as $prevComment)
                        {
                            $userSetting = UserSettings::model()->find(array(
                                'select'=>'value',
                                'condition'=>'user_id = :user_id AND setting_id = :setting_id',
                                'params'=>array(':user_id'=>$prevComment->user_id, ':setting_id'=>$subscrPostCommentsDefault->id)
                            ));

                            if (($userSetting && $userSetting->value == SiteSettings::YES)
                                || (!$userSetting && $subscrPostCommentsDefault->default_value == SiteSettings::YES))
                            {
                                $message->addBcc($prevComment->user->email, $prevComment->user->getName());
                            }
                        }

                        $message->subject = Yii::t('ContentModule.comment', 'first_name also commented on _type: "post_title"',
                            array(
                                $comment->user->gender,
                                'first_name'=>$comment->user->first_name,
                                '_type'=>Yii::t('ContentModule.comment', $type),
                                'post_title'=>$comment->post->title,
                            )
                        );

                        if (Yii::app()->mail->send($message))
                        {
                            $queue->ack($queueMessage['delivery_tag']);
                        }
                    }
                }
            }
        }
    }
}
?>
