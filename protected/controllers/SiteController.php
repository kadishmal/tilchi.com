<?php

class SiteController extends Controller
{
	const STATUS_NEW_FAIL = 0;
	const STATUS_NEW_FAIL_RETRY = 1;
	const STATUS_NEW_SUCCESS = 2;
	const STATUS_NEW_EXISTS = 23000;
	const STATUS_NEW_UNKNOWN_DB_ERROR = 3;
	const STATUS_NEW_PHRASE_FAIL = 4;
	const TRANSLATIONS_PER_PAGE = 25;

    const EXCHANGE_PHRASE_VIEWS = 'tilchi.exchange.phrase.views';
    const QUEUE_PHRASE_VIEWS = 'tilchi.queue.phrase.views';

	public function init()
	{
		Yii::import('application.modules.user.models.*');
		Yii::import('application.modules.user.*');
		Yii::import('application.modules.content.*');
	}
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
            array('allow',
                'actions'=>array('search', 'view', 'index', 'error'),
                'users'=>array('*')
            ),
            array('allow',  // allow all users to perform view actions
				'actions'=>array('new'),
				'roles'=>array('translator'),
			),
            array('deny', // do not allow guest users to logout
				'actions'=>array('logout'),
				'users'=>array('?'),
			),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
		);
	}
    /**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
	public function actionSearch()
    {
        $results = array();
		$results['count'] = 0;

        if(isset($_POST['ajax']) && $_POST['ajax'] === 'tilchi-search-form')
        {
            if(isset($_POST['Tilchi']))
            {
                $searchPhrase = $_POST['Tilchi']['phrase'];

                if (strlen(trim($searchPhrase)) > 0)
                {
                    $fromLang = $_POST['Tilchi']['fromLang'];
                    $toLang = $_POST['Tilchi']['toLang'];

                    $fromLang = Language::model()->cache(2592000)->find('id = :id', array(':id'=>$fromLang));
                    $toLang = Language::model()->cache(2592000)->find('id = :id', array(':id'=>$toLang));

                    if ($fromLang && $toLang)
                    {
                        $idx = 'idx_phrases';

                        if ($fromLang->abbreviation == 'ru' || $fromLang->abbreviation == 'ky'
                            || $fromLang->abbreviation == 'en' || $fromLang->abbreviation == 'tr')
                        {
                            $idx .= '_' . $fromLang->abbreviation;
                        }

                        $search = Yii::App()->search;

                        $search->select('id')->
                            from($idx)->
                            where($searchPhrase . '*')->
                            filters(array('language_id'=>$fromLang->id))->
                            limit(0, 10, 1000);

                        $searchResults = $search->search();
                        $results['count'] = $searchResults->getTotal();
                        $ids = implode(',', $searchResults->getIdList());

                        if ($results['count'] > 0)
                        {
                            $phrases = Phrase::model()->with('language')->findAll(array(
                                'condition'=>'t.id IN (' . $ids . ')',
                                'order'=>'FIELD(t.id,' . $ids . ')'
                            ));

                            $results['phrases'] = array();

                            foreach($phrases as $phrase)
                            {
                                $results['phrases'][] = array(
                                    'fromLang'=>$fromLang->abbreviation,
                                    'toLang'=>$toLang->abbreviation,
                                    'phrase'=>$phrase->phrase
                                );
                            }
                        }
                        else{
                            $results['status'] = Yii::t('tilchi', 'The phrase <b>_phrase</b> has not been found, but we have already added it to our to-translate list.', array('_phrase'=>$searchPhrase));

                            // create a history object to be sent to RabbitMQ
                            $historyObj = array(
                                'phrase'=>$searchPhrase,
                                'searchDate'=>time(),
                                'fromLangId'=>$fromLang->id,
                                'toLangId'=>$toLang->id
                            );

                            // if a user has decided to store the search history, means he is logged in.
                            // Then add his id to the history object.
                            if (Yii::app()->user->getState('save_search_history'))
                            {
                                $historyObj['user_id'] = Yii::app()->user->id;
                            }

                            $this->saveSearchHistory($historyObj);
                        }
                    }
                }
                else{
                    $results['status'] = Yii::t('tilchi', 'No translation found');
                }

                echo CJSON::encode($results);
            }
        }
        // static search
        else{
            if(isset($_POST['Tilchi']))
            {
                $uri = '/site';
                $fromLang = $_POST['Tilchi']['fromLang'];
                $toLang = $_POST['Tilchi']['toLang'];

                $fromLang = Language::model()->cache(2592000)->find('id = :id', array(':id'=>$fromLang));
                $toLang = Language::model()->cache(2592000)->find('id = :id', array(':id'=>$toLang));
                $searchPhrase = $_POST['Tilchi']['phrase'];

                if ($fromLang && $toLang)
                {
                    $uri .= '/' . $fromLang->abbreviation . '/' . $toLang->abbreviation . '/' . $searchPhrase;
                }

                $this->redirect($uri);
            }
            else{
                $this->actionIndex();
            }
        }
    }
	public function actionView($ajax)
	{
        if (isset($_GET['fromLang']) && isset($_GET['toLang']))
		{
			$lang = Language::model()->cache(2592000)->findByPk($_GET['fromLang']);
            $toLang = Language::model()->cache(2592000)->findByPk($_GET['toLang']);

			if ($lang && $toLang)
			{
                $searchPhrase = $_GET['phrase'];

                $idx = 'idx_phrases';
				// Russiang and Kyrgyz languages have their own customized indexes
				if ($lang->abbreviation == 'ru' || $lang->abbreviation == 'ky'
                    || $lang->abbreviation == 'en' || $lang->abbreviation == 'tr')
				{
					$idx .= '_' . $lang->abbreviation;
				}

				$search = Yii::app()->search;

				$search->select('id')->
					from($idx)->
					where($searchPhrase)->
					filters(array('language_id'=>$lang->id))->
					limit(0, 1, 1000);

				$searchResults = $search->search();

				$sysLang = Yii::app()->language;
				$results = array();
				$results['count'] = $searchResults->getTotal();
                // create a history object to be sent to RabbitMQ
                $historyObj = array(
                    'phrase'=>$searchPhrase,
                    'searchDate'=>time(),
                    'fromLangId'=>$lang->id,
                    'toLangId'=>$toLang->id
                );

				if ($results['count'] > 0)
				{
					$results['translations'] = array();

					$phrase = Phrase::model()->with('translations', 'translations.translationPhrase', 'translations.user')
                                ->find('t.id = :id AND translations.translation_language_id = :language_id', array(
                                    ':id'=>implode(',', $searchResults->getIdList()),
                                    ':language_id'=>$toLang->id
                                ));

					if ($phrase)
					{
                        $results['requestAuthor'] = array(
                            'requestedBy'=>Yii::t('tilchi', 'requested', $phrase->user->gender),
                            'name'=>$phrase->user->getName(),
                            'date'=>UserModule::getFormattedRelativeDate($phrase->date),
                            'gravatar'=>$phrase->user->getGravatar(Yii::app()->user->getState('avatar_size')),
                        );

						foreach ($phrase->translations as $translation)
						{
							$results['translations'][] = array(
								'phrase'=>($translation->translationPhrase ? $translation->translationPhrase->phrase : ''),
								'explanation'=>$translation->explanation ? $translation->explanation : '',
                                'translatedBy'=>Yii::t('tilchi', 'translated by', $translation->user->gender),
                                'date'=>UserModule::getFormattedRelativeDate($translation->date),
                                'author'=>$translation->user->getName(),
                                'gravatar'=>$translation->user->getGravatar(Yii::app()->user->getState('avatar_size')),
							);
						}
                        // add a phrase id to the history object
                        $historyObj['phrase_id'] = $phrase->id;
					}

					$results['translationsCount'] = count($results['translations']);

					if ($results['translationsCount'] == 0)
					{
						$isPhrase = mb_strpos($searchPhrase, ' ');

						$results['messages'] = array(
							'noTranslation'=>Yii::t('tilchi', 'The phrase <b>_phrase</b> has not been found, but we have already added it to our to-translate list.', array('_phrase'=>$searchPhrase))
						);
					}
                    // if a user has decided to store the search history, means he is logged in.
                    // Then add his id to the history object.
                    if (Yii::app()->user->getState('save_search_history'))
                    {
                        $historyObj['user_id'] = Yii::app()->user->id;
                    }
				}
                else{
                    $results['messages'] = array(
                        'noPhrase'=>Yii::t('tilchi', 'The phrase <b>_phrase</b> has not been found, but we have already added it to our to-translate list.', array('_phrase'=>$searchPhrase))
                    );

                    if (!Yii::app()->user->isGuest)
                    {
                        $results['requestAuthor'] = array(
                            'requestedBy'=>Yii::t('tilchi', 'requested', Yii::app()->user->getState('gender')),
                            'name'=>Yii::app()->user->getState('short_name'),
                            'date'=>UserModule::getFormattedRelativeDate(time()),
                            'gravatar'=>Yii::app()->user->getState('gravatar'),
                        );
                    }
                }

                $this->saveSearchHistory($historyObj);

				if ($ajax == 'ajax')
				{
					echo CJSON::encode($results);
				}
				else{
					$this->render('view', array(
						'phrase'=>$searchPhrase,
						'fromLang'=>$lang->$sysLang,
						'fromLangId'=>$lang->id,
						'toLangId'=>$_GET['toLang'],
						'results'=>$results
					));
				}
			}
            else{
                $this->actionIndex();
            }
		}
		else{
			$this->actionIndex();
		}
	}
	private function findId($phrase, $languageId)
	{
		$search = Yii::App()->search;

		$search->select('id')->
			from('idx_phrases')->
			where($phrase)->
			filters(array('language_id'=>$languageId))->
			limit(0, 1, 1000);

		return $search->search()->getIdList();
	}
	public function actionNew()
    {
        if(isset($_POST['Tilchi']))
		{
			$results = array();
			$results['status'] = self::STATUS_NEW_FAIL;

			$fromLang = $_POST['Tilchi']['fromLang'];
			$toLang = $_POST['Tilchi']['toLang'];
			$phrase = $_POST['Tilchi']['phrase'];
			$translation = $_POST['Tilchi']['translation'];
			$explanation = $_POST['Tilchi']['explanation'];

			if (Language::model()->exists('id = :id', array(':id'=>$fromLang)) &&
				Language::model()->exists('id = :id', array(':id'=>$toLang)))
			{
				$fromId = implode(',', $this->findId($phrase, $fromLang));

				if ($fromId != '')
				{
					$toId = implode(',', $this->findId($translation, $toLang));
					$user_id = Yii::app()->user->id;

					// how many approved translations does the user have
					$myTranslations = PhraseTranslation::model()->count(
						'user_id = :user_id AND status = :status',
						array(
							':user_id'=>$user_id,
							':status'=>PhraseTranslation::STATUS_NEW_APPROVED
						)
					);

					if ($toId == '')
					{
						// the translation does not exists in the database,
						// therefore insert it to DB
						$translationPhrase = new Phrase('new');
						$translationPhrase->phrase = $translation;
						$translationPhrase->language_id = $toLang;
						$translationPhrase->user_id = $user_id;
						$translationPhrase->date = time();

						$translationPhrase->status = $myTranslations > PhraseTranslation::MIN_TRANSLATOR_COUNT ? PhraseTranslation::STATUS_NEW_APPROVED : PhraseTranslation::STATUS_NEW_PENDING;

						if ($translationPhrase->save())
						{
							$toId = $translationPhrase->id;
						}
						else{
							$results['status'] = self::STATUS_NEW_PHRASE_FAIL;
							$results['messages'] = array(
								'title'=>Yii::t('tilchi', 'Translation Error'),
								'message'=>Yii::t('tilchi', 'We could not save the translation you have submitted. Please try again.'),
								'ok'=>Yii::t('tilchi', 'Ok')
							);
						}
					}

					// translation exists in DB, so just link them both direction
					if ($toId != '')
					{
						$phraseTranslation = new PhraseTranslation;

						$phraseTranslation->user_id = $user_id;
						$phraseTranslation->phrase_id = $fromId;
						$phraseTranslation->translation_phrase_id = $toId;
						$phraseTranslation->translation_language_id = $toLang;
						$phraseTranslation->date = time();
						$phraseTranslation->explanation = $explanation;

						// auto approve the translations if the user already has
						// submitted the minimum number of translations.
						$phraseTranslation->status = $myTranslations > PhraseTranslation::MIN_TRANSLATOR_COUNT ? PhraseTranslation::STATUS_NEW_APPROVED : PhraseTranslation::STATUS_NEW_PENDING;

						try
						{
							if ($phraseTranslation->save())
							{
								$results['status'] = self::STATUS_NEW_SUCCESS;
							}
							else{
								$results['status'] = self::STATUS_NEW_FAIL_RETRY;
							}
						}
						catch(Exception $e)
						{
							if ($e->getCode() == self::STATUS_NEW_EXISTS)
							{
								$results['status'] = self::STATUS_NEW_EXISTS;
								$results['messages'] = array(
									'title'=>Yii::t('tilchi', 'Translation Error'),
									'message'=>Yii::t('tilchi', 'Such translation already exists. Please enter another translation.'),
									'ok'=>Yii::t('tilchi', 'Ok')
								);
							}
							else{
								$results['status'] = self::STATUS_NEW_UNKNOWN_DB_ERROR;
							}
						}

						if ($results['status'] == self::STATUS_NEW_SUCCESS)
						{
							$pairPhraseTranslation = new PhraseTranslation;
							$pairPhraseTranslation->user_id = $user_id;
							$pairPhraseTranslation->phrase_id = $toId;
							$pairPhraseTranslation->translation_phrase_id = $fromId;
							$pairPhraseTranslation->translation_language_id = $fromLang;
							$pairPhraseTranslation->date = $phraseTranslation->date;

							try{
								$pairPhraseTranslation->save();
							}
							catch(Exception $e){
								// ignore the error.
							}
						}
					}
				}
			}

			echo CJSON::encode($results);
		}
    }

	public function actionTranslations()
	{
		$dataProvider = new CActiveDataProvider('PhraseTranslation', array(
			'criteria'=>array(
				'order'=>'t.date DESC',
                'with'=>array('user', 'phrase', 'translationPhrase', 'phrase.language')
			),
			'pagination'=>array(
				'pageSize'=>self::TRANSLATIONS_PER_PAGE,
			),
    	));

		$this->render('translations', array(
			'dataProvider'=>$dataProvider,
		));
	}

    private function saveSearchHistory(&$historyObj)
    {
        // send RabbitMQ a message that a phrase was viewed.
        $amqp = Yii::app()->amqp;
        $amqp->declareExchange(self::EXCHANGE_PHRASE_VIEWS, AMQP_EX_TYPE_DIRECT, AMQP_DURABLE);

        $ex = $amqp->exchange(self::EXCHANGE_PHRASE_VIEWS);
        $amqp->declareQueue(self::QUEUE_PHRASE_VIEWS, AMQP_DURABLE);
        $queue = $amqp->queue(self::QUEUE_PHRASE_VIEWS);
        $queue->bind(self::EXCHANGE_PHRASE_VIEWS, self::QUEUE_PHRASE_VIEWS);

        $ex->publish(serialize($historyObj), self::QUEUE_PHRASE_VIEWS, AMQP_MANDATORY);
    }
}