<?php
    $this->pageTitle = $model->title . ' | ' .
            Yii::t('ContentModule.forum', 'Ideas') . ' | ' .
            Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;

    Yii::app()->clientScript->registerScript('vote-post', "
        enableVoting();
    ");
?>

<div id="forum">
    <div class="post">
        <div class="forum-body">
            <div class="frame">
                <div class="title"><div class="links"><?php
                    echo CHtml::link(Yii::t('ContentModule.forum', 'Forums'), '/forum') . ' / ' .
                         CHtml::link(Yii::t('ContentModule.forum', 'Ideas'), '/forum/ideas');
                ?></div>
                </div>
                <div class="body">
                    <h2><?php echo $model->title ?></h2>
                    <span class="meta"><?php echo Yii::t('ContentModule.blog', 'Published on') . ' ' . ContentModule::getFormattedDate($model->publish_date) . '. ' . Yii::t('ContentModule.blog', 'Author') . ': ' . $model->author->first_name . ' ' . mb_substr($model->author->last_name, 0, 1, 'UTF-8') . '.'; ?></span>
                    <?php
                        echo CHtml::tag('div', array('class'=>'content'), $model->content);

                        echo '<span class="meta last"><span class="tags">';

                        if (strlen($model->tags) > 0){
                            echo Yii::t('ContentModule.blog', 'Tagged as') . ':';
                            $tags = Tag::model()->findAll('name in (\'' . implode('\',\'', $model->tagsAsArray()) . '\')');

                            foreach($tags as $tag){
                                echo CHtml::link($tag->name, '/forum/tags/' . $tag->slug);
                            }
                        }

                        echo '</span></span>';

						if (Yii::app()->user->id == $model->user_id || Yii::app()->user->checkAccess('admin'))
						{
							echo CHtml::tag('div', array('id'=>'post-actions'),
								CHtml::link('<i class="edit"></i>', '/forum/edit/' . $model->id, array('title'=>Yii::t('ContentModule.blog', 'Edit')))
							);
						}
                    ?>
                    <div class="vote-panel">
                        <?php
                            echo CHtml::tag('span', array('class'=>'vote-amount'), Yii::t('ContentModule.forum', '<b>{1} user</b> likes this idea|<b>{n} users</b> like this idea', $model->votesCount));

                            $isVoted = $model->isVoted();

                            echo CHtml::tag('span', array('class'=>'vote-up' . ($isVoted ? ' voted' : ''), 'id'=>$model->id), '<i></i>' .
                                   CHtml::tag('span', array('class'=>'vote-msg yes'), Yii::t('ContentModule.forum', 'n==1#Me too!|n==2#Me too!|n==3#Me too!', $model->type)) .
                                   CHtml::tag('span', array('class'=>'vote-msg no'), Yii::t('ContentModule.forum', 'Undo'))
                            );
                        ?>
                    </div>
                </div>
            </div>
            <div class="frame">
                <div class="title"><h2><?php echo Yii::t('ContentModule.comment', 'Comments'); ?></h2></div>
                <div class="body">
                    <?php
                        $this->renderPartial('_postComments', array('data'=>$model));
                    ?>
                </div>
                <div class="title"><h3><?php echo Yii::t('ContentModule.comment', 'Leave a comment'); ?></h3></div>
                <div class="body">
                    <?php
                        $this->renderPartial('_commentForm', array('data'=>$model));
                    ?>
                </div>
            </div>
        </div><div class="forum-sidebar">
            <div class="frame status">
                <div class="title"><h3><?php
                    echo Yii::t('ContentModule.forum', 'The idea status') .
                    (Yii::app()->user->checkAccess('admin') ? '<span class="msg" title="' . Yii::t('ContentModule.forum', 'To change the status, choose one from the list below.') . '"><i></i></span>' : '')
                ?></h3></div>
                <div class="body">
                    <?php
                        if (Yii::app()->user->checkAccess('admin'))
                        {
                            echo CHtml::link(Yii::t('ContentModule.forum', 'n==1#New|n==2||n==3#New', Post::TYPE_IDEA), '/forum/setStatusNew/' . $model->id, array('class'=>'new' . ($model->response_type == Post::RESPONSE_NEW ? ' active' : ''))) .
                                CHtml::link(Yii::t('ContentModule.forum', 'Under Review'), '/forum/setStatusUnderReview/' . $model->id, array('class'=>'under-review' . ($model->response_type == Post::RESPONSE_UNDER_REVIEW ? ' active' : ''))) .
                                CHtml::link(Yii::t('ContentModule.forum', 'n==1#Accepted|n==2||n==3#Accepted', Post::TYPE_IDEA), '/forum/setStatusAccepted/' . $model->id, array('class'=>'accepted' . ($model->response_type == Post::RESPONSE_ACCEPTED ? ' active' : ''))) .
                                CHtml::link(Yii::t('ContentModule.forum', 'Assigned'), '/forum/setStatusAssigned/' . $model->id, array('class'=>'assigned' . ($model->response_type == Post::RESPONSE_ASSIGNED ? ' active' : ''))) .
                                CHtml::link(Yii::t('ContentModule.forum', 'n==1#Answered|n==2#Completed|n==3#Fixed', Post::TYPE_IDEA), '/forum/setStatusCompleted/' . $model->id, array('class'=>'completed' . ($model->response_type == Post::RESPONSE_COMPLETED ? ' active' : ''))) .
                                CHtml::link(Yii::t('ContentModule.forum', 'Duplicate'), '/forum/setStatusDuplicate/' . $model->id, array('class'=>'duplicate' . ($model->response_type == Post::RESPONSE_DUPLICATE ? ' active' : '')))
                            ;
                        }
                        else{
                            echo CHtml::tag('div', array('class'=>Post::getResponseTitle($model->response_type) . ' active'), Post::getResponseText($model->response_type, $model->type));
                        }
                    ?>
                </div>
            </div>
            <?php
                if (Yii::app()->user->checkAccess('admin')){
                    echo '<div class="frame type">
                            <div class="title"><h3>' . Yii::t('ContentModule.forum', 'Category') .
                                '<span class="msg" title="' . Yii::t('ContentModule.forum', 'To change the category, choose one from the list below.') . '"><i></i></span></h3>
                            </div>
                            <div class="body">' .
                            CHtml::link('<i></i>', '/forum/setTypeQuestion/' . $model->id, array(
                                'class'=>'question' . ($model->type == Post::TYPE_QUESTION ? ' active' : ''),
                                'title'=>Yii::t('ContentModule.forum', ucfirst(Post::getTypeTitle(Post::TYPE_QUESTION)) . 's'),
                            )) .
                            CHtml::link('<i></i>', '/forum/setTypeIdea/' . $model->id, array(
                                'class'=>'idea' . ($model->type == Post::TYPE_IDEA ? ' active' : ''),
                                'title'=>Yii::t('ContentModule.forum', ucfirst(Post::getTypeTitle(Post::TYPE_IDEA)) . 's')
                            )) .
                            CHtml::link('<i></i>', '/forum/setTypeIssue/' . $model->id, array(
                                'class'=>'issue' . ($model->type == Post::TYPE_ISSUE ? ' active' : ''),
                                'title'=>Yii::t('ContentModule.forum', ucfirst(Post::getTypeTitle(Post::TYPE_ISSUE)) . 's')
                            )) .
                            '</div>
                        </div>';
                }
            ?>
        </div>
    </div>
</div>