<?php
class AddUserSearchHistoryCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        $amqp = Yii::app()->amqp;

        if ($amqp->declareQueue(SiteController::QUEUE_PHRASE_VIEWS, AMQP_DURABLE) > 0)
        {
            $queue = $amqp->queue(SiteController::QUEUE_PHRASE_VIEWS);
            $queue->bind(SiteController::EXCHANGE_PHRASE_VIEWS, SiteController::QUEUE_PHRASE_VIEWS);

            while ($queueMessage = $queue->get(AMQP_NOACK))
            {
                // stop the loop if there are no more messages
                if ($queueMessage['count'] < 0)
                {
                    break;
                }

                $historyObj = unserialize($queueMessage['msg']);

                // if there is a user
                if (isset($historyObj['user_id']))
                {
                    $user = User::model()->findByPk($historyObj['user_id']);
                }
                else{
                    $user = User::model()->findByAttributes(array('email'=>'office@incorex.com'));
                }

                $fromLangId = $historyObj['fromLangId'];
                $toLangId = $historyObj['toLangId'];

                if (isset($historyObj['phrase_id']))
                {
                    $phrase_id = $historyObj['phrase_id'];
                }
                // otherwise, add this phrase to the database
                else{
                    $phrase = new Phrase('new');
                    $phrase->phrase = $historyObj['phrase'];
                    $phrase->language_id = $fromLangId;
                    $phrase->user_id = $user->id;
                    $phrase->date = $historyObj['searchDate'];

                    // the phrase may have already been inserted to the database. Check it.
                    if ($phrase->save())
                    {
                        $phrase_id = $phrase->id;
                    }
                    else{
                        $fromLang = Language::model()->cache(2592000)->findByPk($fromLangId);
                        $idx = 'idx_phrases';
                        // use customized indexes for certain languages
                        if ($fromLang->abbreviation == 'ru' || $fromLang->abbreviation == 'ky'
                            || $fromLang->abbreviation == 'en' || $fromLang->abbreviation == 'tr')
                        {
                            $idx .= '_' . $fromLang->abbreviation;
                        }

                        // we need to find it using Sphinx
                        $search = Yii::app()->search;

                        $search->select('id')->
                            from($idx)->
                            where($phrase->phrase)->
                            filters(array('language_id'=>$fromLang->id))->
                            limit(0, 1, 1000);

                        $searchResults = $search->search();

                        if ($searchResults->getTotal() > 0)
                        {
                            $phrase_id = reset($searchResults->getIdList());
                        }
                        elseif ($phrase->hasErrors('phrase')){
                            $existingPhrases = Phrase::model()->findAllByAttributes(array(
                                'phrase'=>$phrase->phrase,
                                'language_id'=>$phrase->language_id
                            ));

                            foreach($existingPhrases as $ph)
                            {
                                if ($ph->phrase == $phrase->phrase)
                                {
                                    $phrase_id = $ph->id;
                                    break;
                                }
                            }
                        }
                    }
                }

                if (isset($phrase_id) && $phrase_id > 0)
                {
                    // no add the search history
                    $searchHistory = new PhraseSearchHistory;
                    $searchHistory->user_id = $user->id;
                    $searchHistory->phrase_id = $phrase_id;
                    $searchHistory->language_id = $toLangId;
                    $searchHistory->search_date = $historyObj['searchDate'];

                    if ($searchHistory->save())
                    {
                        $queue->ack($queueMessage['delivery_tag']);
                    }
                }
            }
        }
    }
}
?>
