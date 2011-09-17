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

		// retrieve all post title, slug, type, and author information
        $command->text = '
            SELECT DISTINCT(post_id), p.title, p.slug, p.type, u.id as u_id, u.email, u.first_name, u.subscr_post_comments
            FROM tbl_comments c
            LEFT JOIN tbl_posts p ON c.post_id = p.id
            LEFT JOIN tbl_users u ON p.user_id = u.id
            WHERE c.id > ' . $lastId;

        $posts = $command->queryAll();

		$distinctPosts = array();

        foreach($posts as $post)
        {
			$p = array();

            $p['title'] = $post['title'];
			$p['slug'] = $post['slug'];
            $p['type'] = $post['type'];
            $p['global_type'] = Post::getGlobalTypeTitle($post['type']);
			$p['u_id'] = $post['u_id'];
			$p['email'] = $post['email'];
            $p['first_name'] = $post['first_name'];
			$p['subscr_post_comments'] = $post['subscr_post_comments'];

			$distinctPosts[$post['post_id']] = $p;
        }
		// If there are any post with comments
        if (!empty($distinctPosts))
        {
			// get all new comments with their authors
            $command->text = '
                SELECT c.id, c.content, u.email, u.first_name, u.subscr_post_comments, c.post_id
                FROM tbl_comments c
                LEFT JOIN tbl_users u ON c.user_id = u.id
                WHERE c.id > ' . $lastId .
                ' ORDER BY c.id';

            $comments = $command->queryAll();

            foreach ($comments as $comment)
            {
                $postId = $comment['post_id'];

                $postAuthorEmail = $distinctPosts[$postId]['email'];
				$isPostAuthorSubscribed = $distinctPosts[$postId]['subscr_post_comments'];
				// if the post author is different from the comment author
				// and if the post author is subscribed to receive comments
				// send notify him as well
                if ($comment['email'] != $postAuthorEmail && $isPostAuthorSubscribed)
                {
                    $message->addBcc($postAuthorEmail, $distinctPosts[$postId]['first_name']);
                }

				// Select all users who have previously commented on this same
				// post and are subscribed to receive notifications.
				// Exclude the post author as
				$command->text = '
					SELECT u.email, u.first_name
					FROM tbl_comments c
					LEFT JOIN tbl_users u ON c.user_id = u.id
					WHERE c.id < ' . $comment['id'] . ' AND post_id = ' . $postId . ' AND user_id <> ' . $distinctPosts[$postId]['u_id'] . ' AND u.subscr_post_comments = 1';

				$postCommenters = $command->queryAll();
				// These commenters are subscribed
				foreach($postCommenters as $commenter)
                {
					$message->addBcc($commenter['email'], $commenter['first_name']);
				}

                $message = New YiiMailMessage;
                $message->view = 'new' . ucfirst(Post::getTypeTitle($distinctPosts[$postId]['type'])) . 'Comment';

                $message->subject = Yii::t('ContentModule.comment', 'first_name commented on "post_title"', array('first_name'=>$comment['first_name'], 'post_title'=>$distinctPosts[$postId]['title']));

                $message->setBody(array(
                    'comment'=>$comment,
                    'title'=>$distinctPosts[$postId]['title'],
                    'link'=>'http://tilchi.info/' . $distinctPosts[$postId]['global_type'] . '/' . $distinctPosts[$postId]['slug'] . '#comment-' . $comment['id']
                ), 'text/html');

                $message->setFrom($distinctPosts[$postId]['global_type'] . '@tilchi.com', 'Tilchi.com');

                Yii::app()->mail->send($message);
            }

            $lastComment = end($comments);

            if ($cronSettings)
			{
                $command->update('tbl_cron_job_settings', array(
                    'last_id'=>$lastComment['id']
                ), 'model_name = \'' . $model_name . '\'');
            }
            else{
                $command->insert('tbl_cron_job_settings', array(
                    'model_name'=>$model_name,
                    'last_id'=>$lastComment['id']
                ));
            }
        }
    }
}
?>
