<?php

class ForumController extends Controller
{
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
				'actions'=>array('search', 'index', 'questions', 'ideas', 'issues', 'view', 'support'),
				'users'=>array('*'),
			),
            array('allow',  // allow all users to perform view actions
				'actions'=>array('new', 'edit'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform administrative actions
				'actions'=>array('setStatusNew', 'setStatusUnderReview', 'setStatusAccepted', 'setStatusAssigned', 'setStatusCompleted', 'setStatusDuplicate', 'setTypeQuestion', 'setTypeIdea', 'setTypeIssue'),
				'users'=>array(Yii::app()->params['adminEmail']),
			),
			array('deny',  // deny all other actions to all users
				'users'=>array('*'),
			),
		);
	}
	public function actionIndex()
	{
        $question = Post::TYPE_QUESTION;
        $idea = Post::TYPE_IDEA;
        $issue = Post::TYPE_ISSUE;

        $sql = 'SELECT  p.title, p.publish_date, p.slug, p.type,
                        p.response_type, u.first_name, u.last_name,
                        COUNT(c.id) as commentsCount,
                        COUNT(v.id) as votesCount
                FROM tbl_users u, tbl_posts p
                LEFT OUTER JOIN tbl_comments c ON p.id = c.post_id
                LEFT OUTER JOIN tbl_votes v ON p.id = v.post_id
                WHERE   p.type = :type
                        AND p.user_id = u.id
                GROUP BY p.id
                ORDER BY p.type, p.response_type, p.publish_date DESC
                LIMIT 3
        ';

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(':type', $question, PDO::PARAM_INT);
        $rows = $command->queryAll();

        $command->bindParam(':type', $idea, PDO::PARAM_INT);
        $rows = array_merge($rows, $command->queryAll());

        $command->bindParam(':type', $issue, PDO::PARAM_INT);
        $rows = array_merge($rows, $command->queryAll());

        $this->render('index', array(
            'dataProvider'=>$rows
        ));
	}
	public function actionSupport()
	{
		$question = Post::TYPE_QUESTION;
        $issue = Post::TYPE_ISSUE;

        $sql = 'SELECT  p.title, p.publish_date, p.slug, p.type,
                        p.response_type, u.first_name, u.last_name,
                        COUNT(c.id) as commentsCount,
                        COUNT(v.id) as votesCount
                FROM tbl_users u, tbl_posts p
                LEFT OUTER JOIN tbl_comments c ON p.id = c.post_id
                LEFT OUTER JOIN tbl_votes v ON p.id = v.post_id
                WHERE   p.type = :type
                        AND p.user_id = u.id
                GROUP BY p.id
                ORDER BY p.type, p.response_type, p.publish_date DESC
                LIMIT 3
        ';

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(':type', $question, PDO::PARAM_INT);
        $rows = $command->queryAll();

        $command->bindParam(':type', $issue, PDO::PARAM_INT);
        $rows = array_merge($rows, $command->queryAll());

        $this->render('support', array(
            'dataProvider'=>$rows
        ));
	}
    /**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($slug)
	{
		$model = Post::model()->with('author')->findByAttributes(
			array('slug'=>$slug, 'status'=>Post::STATUS_PUBLISHED)
		);

		if ($model === null){
			throw new CHttpException(404, 'The requested page does not exist.');
		}

        if ($model->type == Post::TYPE_QUESTION)
        {
            $this->render('viewQuestion', array(
                'model'=>$model,
            ));
        }
        else if ($model->type == Post::TYPE_IDEA)
        {
            $this->render('viewIdea', array(
                'model'=>$model,
            ));
        }
        else if ($model->type == Post::TYPE_ISSUE)
        {
            $this->render('viewIssue', array(
                'model'=>$model,
            ));
        }
        else{
            $this->redirect('/forum');
        }
	}
    public function actionSearch()
    {
        $results = array();

        if(isset($_POST['Forum']))
		{
            $phrase = $_POST['Forum']['phrase'];

            if (strlen(trim($phrase)) > 0)
            {
                $dataProvider = new CActiveDataProvider('Post', array(
                    'criteria'=>array(
                        'condition'=>'title LIKE :phrase',
                        'limit'=>3,
                        'params'=>array(':phrase'=>'%' . $phrase . '%'),
                    ),
                ));

                $results['count'] = $dataProvider->itemCount;
            }
            else{
                $dataProvider = null;
                $results['count'] = 0;
            }

            if(isset($_POST['ajax']) && $_POST['ajax'] === 'forum-search-form')
            {
                if ($results['count'] > 0){
                    $results['posts'] = array();

                    foreach($dataProvider->getData() as $data){
                        $type = Post::getTypeTitle($data->type);
                        $category = $type . 's';

                        $results['posts'][] = array(
                            'title'=>$data->title,
                            'type'=>$type,
                            'summary'=>CHtml::encode(substr(strip_tags($data->content), 0, 150)),
                            'link'=>'/forum/' . $data->slug,
                            'categoryLink'=>'/forum/' . $category,
                            'categoryText'=>Yii::t('ContentModule.forum', ucfirst($category)),
                            'votesCount'=>$data->votesCount,
                            'votesTitle'=>Yii::t('ContentModule.forum', '{n} vote|{n} votes', $data->votesCount)
                        );
                    }
                }
                else{
                    $results['status'] = Yii::t('ContentModule.forum', 'No topics found');
                }

                echo CJSON::encode($results);
            }
            else{
                $this->render('_viewSearchResults', array(
                    'dataProvider'=>$dataProvider
                ));
            }
        }
    }
    public function actionNew($type)
    {
        $model = new Post('forum');

        $ucType = ucfirst($type);

        if(isset($_POST[$ucType]))
		{
            $model->attributes = $_POST[$ucType];
            $model->status = Post::STATUS_PUBLISHED;

            if ($model->validate())
            {
                $model->user_id = Yii::app()->user->id;
                $model->type = $model->getTypeCode($type);

                $model->slug = ContentModule::sanitize_title_with_dashes($model->title);

				while (!$model->validate('slug')){
					$model->slug = $model->slug . '-2';
				}

                if($model->save(false))
                {
                    $vote = new Vote;
                    $vote->user_id = $model->user_id;
                    $vote->post_id = $model->id;
                    $vote->type = $model->type;
                    $vote->save();
                    $this->redirect('/forum/' . $model->slug);
                }
            }
        }

		switch($type){
			case 'idea': $this->render('newIdea', array('model'=>$model)); break;
			case 'issue': $this->render('newIssue', array('model'=>$model)); break;
			default: $this->render('newQuestion', array('model'=>$model));
		}
    }
	public function actionEdit($id)
	{
		$model = $this->loadModelById($id);

        $ucType = ucfirst(Post::getTypeTitle($model->type));

        if(isset($_POST[$ucType]))
		{
            $model->attributes = $_POST[$ucType];
			$model->scenario = 'forum';

			if($model->save())
			{
				$this->redirect('/forum/' . $model->slug);
			}
        }

		switch($model->type){
			case Post::TYPE_IDEA: $this->render('editIdea', array('model'=>$model)); break;
			case Post::TYPE_ISSUE: $this->render('editIssue', array('model'=>$model)); break;
			default: $this->render('editQuestion', array('model'=>$model));
		}
	}
    public function actionQuestions()
    {
        $dataProvider = new CActiveDataProvider('Post', array(
            'criteria'=>array(
                'condition'=>'status = :status AND type = :type',
                'params'=>array(':status'=>Post::STATUS_PUBLISHED, ':type'=>Post::TYPE_QUESTION),
                'order'=>'response_type, publish_date DESC',
                'with'=>array('author')
            ),
        ));

        $this->render('questions', array(
            'dataProvider'=>$dataProvider
        ));
    }
    public function actionIdeas()
    {
        $dataProvider = new CActiveDataProvider('Post', array(
            'criteria'=>array(
                'condition'=>'status = :status AND type = :type',
                'params'=>array(':status'=>Post::STATUS_PUBLISHED, ':type'=>Post::TYPE_IDEA),
                'order'=>'response_type, publish_date DESC',
                'with'=>array('author')
            ),
        ));

        $this->render('ideas', array(
            'dataProvider'=>$dataProvider
        ));
    }
    public function actionIssues()
    {
        $dataProvider = new CActiveDataProvider('Post', array(
            'criteria'=>array(
                'condition'=>'status = :status AND type = :type',
                'params'=>array(':status'=>Post::STATUS_PUBLISHED, ':type'=>Post::TYPE_ISSUE),
                'order'=>'response_type, publish_date DESC',
                'with'=>array('author')
            ),
        ));

        $this->render('issues', array(
            'dataProvider'=>$dataProvider
        ));
    }
    public function actionSetStatusNew()
    {
       $this->changeStatus($id, Post::RESPONSE_NEW);
    }
    public function actionSetStatusUnderReview($id)
	{
        $this->changeStatus($id, Post::RESPONSE_UNDER_REVIEW);
	}
	public function actionSetStatusAccepted($id)
	{
        $this->changeStatus($id, Post::RESPONSE_ACCEPTED);
	}
	public function actionSetStatusAssigned($id)
	{
        $this->changeStatus($id, Post::RESPONSE_ASSIGNED);
	}
	public function actionSetStatusCompleted($id)
	{
        $this->changeStatus($id, Post::RESPONSE_COMPLETED);
	}
	public function actionSetStatusDuplicate($id)
	{
        $this->changeStatus($id, Post::RESPONSE_DUPLICATE);
	}
    private function changeStatus($id, $status)
    {
        $model = $this->loadModelById($id);

        if ($model->response_type != $status){
            $model->response_type = $status;
            $model->save(false, array('response_type'));
        }

        $returnUrl = Yii::app()->request->urlReferrer;
        $this->redirect($returnUrl == null ? '/forum' : $returnUrl);
    }
    public function actionSetTypeQuestion($id)
	{
        $this->changeType($id, Post::TYPE_QUESTION);
	}
	public function actionSetTypeIdea($id)
	{
        $this->changeType($id, Post::TYPE_IDEA);
	}
	public function actionSetTypeIssue($id)
	{
        $this->changeType($id, Post::TYPE_ISSUE);
	}
    private function changeType($id, $type)
    {
        $model = $this->loadModelById($id);

        if ($model->type != $type){
            $model->type = $type;
            $model->save(false, array('type'));
        }

        $returnUrl = Yii::app()->request->urlReferrer;
        $this->redirect($returnUrl == null ? '/forum' : $returnUrl);
    }
    /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModelById($id)
	{
		$model = Post::model()->findByPk($id);

		if ($model === null){
			throw new CHttpException(404, 'The requested comment does not exist.');
		}

		return $model;
	}
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model, $form = 'forum-search-form')
	{
		if(isset($_POST['ajax']) && $_POST['ajax'] === $form)
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}