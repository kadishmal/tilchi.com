<?php

class CommentController extends Controller
{
    const SAVE_SUCCESS = 1;
    const SAVE_FAIL = 2;

    const EXCHANGE_COMMENTS_NEW = 'tilchi.exchange.comments.new';
    const QUEUE_COMMENTS_NEW = 'tilchi.queue.comments.new';
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
    /**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform view actions
				'actions'=>array('new', 'delete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform administrative actions
				'actions'=>array('approve', 'disapprove', 'edit', 'trash', 'spam'),
				'users'=>array(Yii::app()->params['adminEmail']),
			),
			array('deny',  // deny all other actions to all users
				'users'=>array('*'),
			),
		);
	}
	public function actionNew()
	{
		$model = new Comment;

		if(isset($_POST['Comment']))
		{
			$model->attributes = $_POST['Comment'];

            if ($model->validate(array('post_id')) && $model->validate(array('content')))
            {
                if (!$model->validate(array('parent_id')))
                {
                    $model->parent_id = 0;
                }

                $model->user_id = Yii::app()->user->id;
                $model->date = time();

                if (Yii::app()->user->checkAccess('admin') ||
                        Comment::model()->my()->countByAttributes(array('status'=>Comment::STATUS_APPROVED)) > ContentModule::USER_PENDING_COMMENT_COUNT)
                {
                        $model->status = Comment::STATUS_APPROVED;
                }
                else{
                    $model->status = Comment::STATUS_PENDING;
                }

                $model->order = $this->getOrder($model->post_id, $model->parent_id);

                if($model->save(false))
                {
                    // queue this comment to RabbitMQ so that the latter sends email
                    // to other commenters and post author
                    $amqp = Yii::app()->amqp;
                    $amqp->declareExchange(self::EXCHANGE_COMMENTS_NEW, AMQP_EX_TYPE_DIRECT, AMQP_DURABLE);

                    $ex = $amqp->exchange(self::EXCHANGE_COMMENTS_NEW);
                    $amqp->declareQueue(self::QUEUE_COMMENTS_NEW, AMQP_DURABLE);
                    $queue = $amqp->queue(self::QUEUE_COMMENTS_NEW);
                    $queue->bind(self::EXCHANGE_COMMENTS_NEW, self::QUEUE_COMMENTS_NEW);

                    $ex->publish($model->id, self::QUEUE_COMMENTS_NEW, AMQP_MANDATORY);

                    $returnUrl = Yii::app()->request->urlReferrer;

                    $pos = strpos($returnUrl, '#');

                    if ($pos > -1)
                    {
                        $returnUrl = substr($returnUrl, 0, $pos);
                    }

                    if (substr($returnUrl, -1) == '/')
                    {
                        $returnUrl = substr($returnUrl, 0, $pos);
                    }

                    $this->redirect($returnUrl . '#comment-' . $model->id);
                }
            }
		}

		//echo CJSON::encode($results);
        $returnUrl = Yii::app()->request->urlReferrer;
        $this->redirect($returnUrl == null ? '/blog' : $returnUrl);
	}

    private function getOrder($postId, $parentId)
	{
        // if no comments, it will receive -1, then +1 will make it 0
        // otherwise, +1 will have an order +1 more.
        $lastOrder = $this->getLastCommentOrderInTree($postId, $parentId);

        // It's a root comment
        if ($lastOrder == -1){
            $order = 0;
        }
        else{
            $nextComment = Comment::model()->find(array(
                'select'=>'MIN(t.order) as \'order\'',
                'condition'=>'post_id = :post_id AND t.order > :order',
                'params'=>array(':post_id'=>$postId, ':order'=>$lastOrder)
            ));

            if ($nextComment->order)
            {
                $a = $lastOrder;
                $b = $nextComment->order;

                $order = ceil($a);

                // a has fractions
                if (floor($a) < $order){
                    if ($order > $b || $order == ceil($b)){
                        $order = ($a + $b) / 2;
                    }
                }
                // a does not have fractions
                else{
                    $order = floor($b);
                    // b has fractions
                    if ($order < ceil($b)){
                        if ($order <= floor($a)){
                            $order = ($a + $b) / 2;
                        }
                    }
                    // both a and b do not have fractions
                    else{
                        $order = ($a + $b) / 2;
                    }
                }
            }
            else{
                $order = ceil($lastOrder);

                if ($order == $lastOrder){
                    $order = $order + 1;
                }
            }
        }

		return $order;
	}
    private function getLastCommentOrderInTree($postId, $parentId)
    {
        // This will find the last child whose parent is $parentId
        $comment = Comment::model()->find(array(
            'select'=>'MAX(id) as id',
            'condition'=>'post_id = :post_id AND parent_id = :parent_id',
            'params'=>array(':post_id'=>$postId, ':parent_id'=>$parentId)
        ));

        while($comment->id){
            // assign its id as a parent, then search its last child
            $parentId = $comment->id;
            // Find the last child comment
            $comment = Comment::model()->find(array(
                'select'=>'MAX(id) as id',
                'condition'=>'post_id = :post_id AND parent_id = :parent_id',
                'params'=>array(':post_id'=>$postId, ':parent_id'=>$parentId)
            ));
        };

        if ($parentId > 0){
            $comment = Comment::model()->find(array(
                'select'=>'t.order',
                'condition'=>'id = :id',
                'params'=>array(':id'=>$parentId)
            ));

            return $comment->order;
        }

        return -1;
    }
	public function actionApprove($id)
	{
        $this->changeStatus($id, Comment::STATUS_APPROVED);
	}
	public function actionDisapprove($id)
	{
        $this->changeStatus($id, Comment::STATUS_PENDING);
	}
	public function actionSpam($id)
	{
        $this->changeStatus($id, Comment::STATUS_SPAM);
	}
	public function actionTrash($id)
	{
        $this->changeStatus($id, Comment::STATUS_TRASH);
	}
    private function changeStatus($id, $status)
    {
        $model = $this->loadModelById($id);

        if ($model->status != $status){
            $model->status = $status;
            $model->save(false, array('status'));
        }

        $returnUrl = Yii::app()->request->urlReferrer;

        $this->redirect($returnUrl == null ? '/blog/comments' : $returnUrl);
    }
    public function actionDelete($id)
    {
        $model = $this->loadModelById($id);

        if ($model->user_id == Yii::app()->user->id || Yii::app()->user->checkAccess('admin'))
        {
            // If this comment has any chlid comments, set the current comment's
            // parent_id as parent of those subcomments
            Comment::model()->updateAll(
                    array('parent_id'=>$model->parent_id),
                    'parent_id = ' . $model->id
            );

            $model->delete();

            $returnUrl = Yii::app()->request->urlReferrer;
            $this->redirect($returnUrl == null ? '/blog' : $returnUrl);
        }
        else{
			throw new CHttpException(403, 'You do not have permissions to delete other user\'s comments.');
        }
    }
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModelById($id)
	{
		$model = Comment::model()->findByPk($id);

		if ($model === null){
			throw new CHttpException(404, 'The requested comment does not exist.');
		}

		return $model;
	}
    /**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'comment-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}