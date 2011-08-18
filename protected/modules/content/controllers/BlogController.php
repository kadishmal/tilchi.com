<?php

class BlogController extends Controller
{
	const POSTS_PER_PAGE = 10;
    const COMMENTS_PER_PAGE = 30;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

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
				'actions'=>array('index', 'view', 'tags'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform administrative actions
				'actions'=>array('new', 'edit', 'comments', 'delete', 'posts'),
				'users'=>array(Yii::app()->params['adminEmail']),
			),
			array('deny',  // deny all other actions to all users
				'users'=>array('*'),
			),
		);
	}
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('Post', array(
			'criteria'=>array(
				'condition'=>'status = :status AND type = :type',
				'order'=>'publish_date DESC',
                'params'=>array(':status'=>Post::STATUS_PUBLISHED, 'type'=>Post::TYPE_BLOG),
				'with'=>array('author', 'commentsCount'),
			),
			'pagination'=>array(
				'pageSize'=>self::POSTS_PER_PAGE,
			),
    	));

		$this->render('index', array(
			'dataProvider'=>$dataProvider,
		));
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionNew()
	{
		$model = new Post('blog');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Post']))
		{
			$model->attributes = $_POST['Post'];

            if ($model->validate())
            {
                $model->user_id = Yii::app()->user->id;
                $model->type = Post::TYPE_BLOG;

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
                    if ($model->status == Post::STATUS_PUBLISHED)
                    {
                        $this->redirect('/blog/' . $model->slug);
                    }
                    else if ($model->status == Post::STATUS_DRAFT)
					{
                        $this->redirect('/blog/edit/' . $model->id);
                    }
                }
            }
		}

		$this->render('new',array(
			'model'=>$model,
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
		else{
			$this->render('view', array(
				'model'=>$model,
			));
		}
	}
	/**
	 * Lists all models.
	 */
	public function actionPosts()
	{
		$dataProvider = new CActiveDataProvider('Post', array(
			'criteria'=>array(
				'condition'=>'type = :type',
				'order'=>'publish_date DESC',
                'params'=>array('type'=>Post::TYPE_BLOG),
				'with'=>array('author', 'commentsCount'),
			)
    	));

		$this->render('posts', array(
			'dataProvider'=>$dataProvider,
		));
	}
	/**
	 * Updates a particular model.
	 * If edit is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be edited
	 */
	public function actionEdit($id)
	{
		$model = $this->loadModelById($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Post']))
		{
			$model->attributes = $_POST['Post'];
            $model->scenario = 'blog';

            $model->save();
		}

		$this->render('edit',array(
			'model'=>$model,
		));
	}
	public function actionTags($param)
	{
		if (Tag::model()->exists(
			'slug = :slug',
			array(':slug'=>$param)
		)){
			$dataProvider = new CActiveDataProvider('Post', array(
				'criteria'=>array(
					'condition'=>'status = ' . Post::STATUS_PUBLISHED . ' AND `tag`.`slug` = \'' . $param . '\'',
					'order'=>'publish_date DESC',
					'with'=>array('postTags', 'postTags.tag', 'author'),
					'together'=>true
				),
				'pagination'=>array(
					'pageSize'=>self::POSTS_PER_PAGE,
				),
	    	));

			$this->render('index', array(
				'dataProvider'=>$dataProvider,
			));
		}
		else{
			//throw new CHttpException(404, 'The requested page does not exist.');
            $dataProvider = new CActiveDataProvider('Tag', array(
                'criteria'=>array(
                    'order'=>'name ASC',
                    'limit'=>Tag::MAX_DISPLAY_COUNT
                )
            ));

			$this->render('tags', array(
				'dataProvider'=>$dataProvider,
			));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Manages all models.
	 */
	public function actionComments($param)
	{
        switch($param){
            case 'pending': $status = Comment::STATUS_PENDING; break;
            case 'approved': $status = Comment::STATUS_APPROVED; break;
            case 'spam': $status = Comment::STATUS_SPAM; break;
            case 'trash': $status = Comment::STATUS_TRASH; break;
            default:
                // If status is not specified or show=all is requested,
                // both APPROVED and PENDING comments will be shown
                $status = Comment::STATUS_APPROVED . ' OR t.status = ' . Comment::STATUS_PENDING;
        }


		$dataProvider = new CActiveDataProvider('Comment', array(
			'criteria'=>array(
				'order'=>'date DESC',
                'condition'=>'t.status = ' . $status,
                'with'=>array('user', 'post')
			),
			'pagination'=>array(
				'pageSize'=>self::COMMENTS_PER_PAGE,
			),
    	));

		$this->render('comments', array(
			'dataProvider'=>$dataProvider,
		));
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
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModelByTitle($slug)
	{
		$model = Post::model()->findByAttributes(
			array('slug'=>$slug)
		);

		if ($model === null){
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='post-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function getMonthsList(){
		return array(
			'01'=>Yii::app()->dateFormatter->format('MMM', strtotime('Jan')),
			'02'=>Yii::app()->dateFormatter->format('MMM', strtotime('Feb')),
			'03'=>Yii::app()->dateFormatter->format('MMM', strtotime('Mar')),
			'04'=>Yii::app()->dateFormatter->format('MMM', strtotime('Apr')),
			'05'=>Yii::app()->dateFormatter->format('MMM', strtotime('May')),
			'06'=>Yii::app()->dateFormatter->format('MMM', strtotime('Jun')),
			'07'=>Yii::app()->dateFormatter->format('MMM', strtotime('Jul')),
			'08'=>Yii::app()->dateFormatter->format('MMM', strtotime('Aug')),
			'09'=>Yii::app()->dateFormatter->format('MMM', strtotime('Sep')),
			'10'=>Yii::app()->dateFormatter->format('MMM', strtotime('Oct')),
			'11'=>Yii::app()->dateFormatter->format('MMM', strtotime('Nov')),
			'12'=>Yii::app()->dateFormatter->format('MMM', strtotime('Dec'))
		);
	}
}