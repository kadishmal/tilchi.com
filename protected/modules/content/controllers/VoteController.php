<?php

class VoteController extends Controller
{
    const STATUS_SUCCESS = 0;
    const STATUS_UNAUTHORIZED = 1;
    const STATUS_ERROR = 2;
    const STATUS_MAX_IDEA_VOTE_REACHED = 3;
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
				'actions'=>array('up'),
				'users'=>array('*'),
			),
			array('deny',  // deny all other actions to all users
				'users'=>array('*'),
			),
		);
	}
	public function actionIndex()
	{
		$this->render('index');
	}
	public function actionUp()
	{
        $results = array();

        if (Yii::app()->user->isGuest)
        {
            $results['status'] = self::STATUS_UNAUTHORIZED;
            $results['title'] = Yii::t('ContentModule.vote', 'Do you want to login?');
            $results['message'] = Yii::t('ContentModule.vote', 'You need to be logged in to vote. Proceed to <b>Login</b> page?');
            $results['yes'] = Yii::t('ContentModule.vote', 'Yes');
            $results['no'] = Yii::t('ContentModule.vote', 'No');
        }
        else{
            $results['status'] = self::STATUS_ERROR;

            if(isset($_POST['Vote']))
            {
                $model = new Vote;
                $model->attributes = $_POST['Vote'];
                $model->user_id = Yii::app()->user->id;

                $userVote = Vote::model()->my()->findByAttributes(array(
                    'post_id'=>$model->post_id
                ));

                if ($userVote)
                {
                    if ($userVote->delete())
                    {
                        $results['status'] = self::STATUS_SUCCESS;
                        $results['count'] = Yii::t('ContentModule.forum', '<b>{1} user</b> would like this answered|<b>{n} users</b> would like this answered', Vote::model()->countByAttributes(array('post_id'=>$model->post_id)));
                    }
                }
                else
                {
                    $post = Post::model()->findByPk($model->post_id);

                    if ($post)
                    {
                        $model->type = $post->type;
						// Allows all users to post questions or issues regardless of
						// the number of votes they have left.
                        if ($model->type == Post::TYPE_QUESTION || $model->type == Post::TYPE_ISSUE)
                        {
                            if ($model->save())
                            {
                                $results['status'] = self::STATUS_SUCCESS;
                                $results['count'] = Yii::t('ContentModule.forum', '<b>{1} user</b> would like this answered|<b>{n} users</b> would like this answered', Vote::model()->countByAttributes(array('post_id'=>$model->post_id)));
                            }
                        }
                        else if ($model->type == Post::TYPE_IDEA)
                        {
                            $criteria = new CDbCriteria;
							// find all votes for this user given for any idea which was not yet completed
                            $criteria->condition = 't.type = :type AND t.user_id = :user_id AND p.response_type < :response_type';
                            $criteria->join = 'LEFT JOIN tbl_posts p ON t.post_id = p.id';
                            $criteria->params = array(':type'=>$model->type, ':user_id'=>$model->user_id, ':response_type'=>Post::RESPONSE_COMPLETED);
							// if user has exhausted all his votes, inform about it.
                            if (Vote::model()->count($criteria) >= ContentModule::USER_MAX_ACTIVE_IDEA)
                            {
                                $results['status'] = self::STATUS_MAX_IDEA_VOTE_REACHED;
                                $results['title'] = Yii::t('ContentModule.forum', 'No more votes');
                                $results['message'] = Yii::t('ContentModule.forum', 'You have exhausted all your votes. You can either unvote active ideas or wait until one is accepted.');
                                $results['ok'] = Yii::t('ContentModule.vote', 'OK');
                            }
                            else{
                                if ($model->save())
                                {
                                    $results['status'] = self::STATUS_SUCCESS;
                                    $results['count'] = Yii::t('ContentModule.forum', '<b>{1} user</b> likes this idea|<b>{n} users</b> like this idea', Vote::model()->countByAttributes(array('post_id'=>$model->post_id)));
                                }
                            }
                        }
                    }
                }
            }
        }

        echo CJSON::encode($results);
	}
}