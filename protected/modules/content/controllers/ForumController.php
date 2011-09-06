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

		if ($model === null)
		{
			$action = 'action' . $slug;

			if (method_exists($this, $action))
			{
				call_user_func(array($this, $action));
			}
			else{
				throw new CHttpException(404, 'The requested page does not exist.');
			}
		}
		else
		{
			$this->render('view' . ucfirst(Post::getTypeTitle($model->type)), array(
				'model'=>$model,
			));
		}
	}
    public function actionSearch()
    {
        $results = array();
		$results['count'] = 0;

        if(isset($_POST['Forum']))
		{
            $phrase = $_POST['Forum']['phrase'];

            if (strlen(trim($phrase)) > 0)
            {
				$scope = $_POST['Forum']['scope'];

				switch ($scope)
				{
					case Post::TYPE_IDEA;
					case Post::TYPE_ISSUE;
					case Post::TYPE_QUESTION;
					case Post::TYPE_SUPPORT;
					case Post::TYPE_FORUMS: break;
					default: $scope = Post::TYPE_FORUMS;
				}

				$ret = $this->sendSocketRequest('/api/search/?target=forum&phrase=' . urlencode($phrase) . '&scope=' . $scope);

				if ($ret !== false)
				{
					$status = strpos($ret, 'HTTP/1.1 200 OK');

					if ($status !== false)
					{
						// remove headers or the following is returned:
						// HTTP/1.1 200 OK Date: Sun, 28 Aug 2011 12:44:52 GMT Server: Apache/2.2.17 (Ubuntu) X-Powered-By: PHP/5.3.5-1ubuntu7.2 Vary: Accept-Encoding Content-Length: 26 Connection: close Content-Type: text/html {"count":3,"ids":[12,7,6]}
						$ret = substr($ret, strpos($ret, "\r\n\r\n") + 4);

						$results = CJSON::decode($ret);

						if ($results['count'] > 0)
						{
							foreach($results['posts'] as $post)
							{
								$post['categoryText'] = Yii::t('ContentModule.forum', $post['categoryText']);
								$post['votesTitle'] = Yii::t('ContentModule.forum', '{n} vote|{n} votes', $post['votesCount']);
							}
						}
						else{
							$results['status'] = Yii::t('ContentModule.forum', 'No topics found');
						}
					}
					else{
						$results['status'] = Yii::t('ContentModule.forum', 'Not authorized.');
					}
				}
				else{
					$results['status'] = Yii::t('ContentModule.forum', 'Your request could not be processed. Please try again later.');
				}
            }
            else{
				$results['status'] = Yii::t('ContentModule.forum', 'No topics found');
            }

            if(isset($_POST['ajax']) && $_POST['ajax'] === 'forum-search-form')
            {
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
				$tags = $model->tagsAsArray();
				$i = 0;
				// If there is no dash, means the slug consists of one word, which may
				// potentially be a controller action. For this reason we will
				// not allow one-word slugs.
				if (strpos($model->slug, '-') === false)
				{
					if (count($tags) > 0)
					{
						$model->slug .= '-' . ContentModule::sanitize_title_with_dashes($tags[$i++]);
					}
					else{
						$model->slug .= '-' . rand(1, 999);
					}
				}

				while (!$model->validate('slug'))
				{
					if (count($tags) > $i)
					{
						$model->slug .= '-' . ContentModule::sanitize_title_with_dashes($tags[$i++]);
					}
					else{
						$model->slug .= '-' . rand(1, 999);
					}
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
			else{
				if ($model->hasErrors('title')){
					switch ($type){
						case 'idea': $title = Yii::t('ContentModule.forum', 'What\'s your idea?'); break;
						case 'issue': $title = Yii::t('ContentModule.forum', 'What issue did you find?'); break;
						default: $title = Yii::t('ContentModule.forum', 'What\'s your question?'); break;
					}

					$model->clearErrors('title');
					$model->addError('title', $title);
				}

				if ($model->hasErrors('content')){
					switch ($type){
						case 'idea': $title = Yii::t('ContentModule.forum', 'Tell us more details about your idea'); break;
						case 'issue': $title = Yii::t('ContentModule.forum', 'Tell us more details about the issue you\'ve encountered on Tilchi.com'); break;
						default: $title = Yii::t('ContentModule.forum', 'Describe your question, or the problem you\'re experiencing, in detail'); break;
					}

					$model->clearErrors('content');
					$model->addError('content', $title);
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
    public function actionSetStatusNew($id)
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
	private function sendRequest($uri)
	{
		$host = 'peopletranslate.com';
		$port = 80;

		if (($fp = @fsockopen($host, $port, $errno, $errstr)) == false)
		{
			return false;
		}

		$ret = '';

		$crlf = "\r\n";
		$req = 'GET ' . $uri . ' HTTP/1.1' . $crlf;
		$req .= 'Host: ' . $host . $crlf;
		$req .= 'X_USERNAME: ' . $_SERVER['HTTP_X_USERNAME'] . $crlf;
		$req .= 'X_PASSWORD: ' . $_SERVER['HTTP_X_PASSWORD'] . $crlf;
		$req .= 'Connection: Close' . $crlf . $crlf;

		fwrite($fp, $req);

		while (!feof($fp))
		{
			$ret .= fgets($fp, 128);
		}

		fclose($fp);

		return $ret;
	}
	private function sendSocketRequest($uri)
	{
		$host = '114.200.120.216';
		$port = getservbyname('www', 'tcp');

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		if ($socket === false) {
			return false;
		}

		$result = socket_connect($socket, $host, $port);

		if ($result === false) {
			return false;
		}

		$ret = '';
		$buf = '';

		$crlf = "\r\n";
		$req = 'GET ' . $uri . ' HTTP/1.1' . $crlf;
		$req .= 'Host: peopletranslate.com' . $crlf;
		$req .= 'X_USERNAME: ' . $_SERVER['HTTP_X_USERNAME'] . $crlf;
		$req .= 'X_PASSWORD: ' . $_SERVER['HTTP_X_PASSWORD'] . $crlf;
		$req .= 'Connection: Close' . $crlf . $crlf;

		socket_write($socket, $req, strlen($req));

		while ($buf = socket_read($socket, 2048)) {
			$ret .= $buf;
		}

		socket_close($socket);

		return $ret;
	}
}