<?php
class SendEmailCommand extends CConsoleCommand
{
    public function actionComments()
    {
        $model_name = 'Comment';

        $db = Yii::app()->db;

        $command = $db->createCommand('SELECT * FROM tbl_cron_job_settings WHERE model_name = \'' . $model_name . '\'');
        $cronSettings = $command->queryRow();

        $lastId = ($cronSettings ? $cronSettings['last_id'] : 0);

        $command->text = '
            SELECT DISTINCT(post_id), p.title, p.slug, p.type, u.email, u.first_name
            FROM tbl_comments c
            LEFT JOIN tbl_posts p ON c.post_id = p.id
            LEFT JOIN tbl_users u ON p.user_id = u.id
            WHERE c.id > ' . $lastId;

        $posts = $command->queryAll();
        $postIds = array();
        $users = array();
        $titles = array();
        $slugs = array();
        $types = array();
        $globalTypes = array();
        $postCommenters = array();

        foreach($posts as $post)
        {
            $id = $post['post_id'];
            $postIds[] = $id;

            $slugs[$id] = $post['slug'];
            $titles[$id] = $post['title'];
            $types[$id] = $post['type'];
            $globalTypes[$id] = Post::getGlobalTypeTitle($post['type']);

            $users[$id] = array();
            $users[$id]['email'] = $post['email'];
            $users[$id]['first_name'] = $post['first_name'];

            $postCommenters[$post['post_id']] = array();
        }

        if (!empty($postIds))
        {
            $command->text = '
                SELECT c.id, c.content, u.email, u.first_name, c.post_id
                FROM tbl_comments c
                LEFT JOIN tbl_users u ON c.user_id = u.id
                WHERE c.id > ' . $lastId . ' AND c.post_id IN(' . implode(',', $postIds) . ')' .
                'ORDER BY c.id';

            $comments = $command->queryAll();

            foreach ($comments as $comment)
            {
                $postCommenters[$comment['post_id']][$comment['email']] = $comment['first_name'];
            }

            foreach ($comments as $comment)
            {
                $postId = $comment['post_id'];
                $email = $users[$postId]['email'];
                $name = $users[$postId]['first_name'];

                if ($comment['email'] != $email)
                {
                    $message->addBcc($email, $name);
                }

                $message = New YiiMailMessage;
                $message->view = 'new' . ucfirst(Post::getTypeTitle($types[$postId])) . 'Comment';

                $message->subject = Yii::t('ContentModule.comment', 'first_name commented on "post_title"', array('first_name'=>$comment['first_name'], 'post_title'=>$titles[$postId]));

                $message->setBody(array(
                    'comment'=>$comment,
                    'title'=>$titles[$postId],
                    'link'=>'http://beta.tilchi.com/' . $globalTypes[$postId] . '/' . $slugs[$postId] . '#comment-' . $comment['id']
                ), 'text/html');

                $message->setFrom($globalTypes[$postId] . '@tilchi.com', 'Tilchi.com');

                foreach($postCommenters[$postId] as $email => $name)
                {
                    if ($email != $comment['email'])
                        $message->addBcc($email, $name);
                }

                Yii::app()->mail->send($message);
            }

            $lastComment = end($comments);

            if ($cronSettings){
                echo $command->update('tbl_cron_job_settings', array(
                    'last_id'=>$lastComment['id']
                ), 'model_name = \'' . $model_name . '\'');
            }
            else{
                echo $command->insert('tbl_cron_job_settings', array(
                    'model_name'=>$model_name,
                    'last_id'=>$lastComment['id']
                ));
            }
        }
    }
}
?>
