<?php
    $this->pageTitle = Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;
?>

<div id="forum">
     <div class="forum-body">
         <div class="frame">
            <div class="title""><h2><?php echo CHtml::link(Yii::t('ContentModule.forum', 'Forums'), '/forum') . ' / ' .
                    Yii::t('ContentModule.forum', 'Support');
            ?></h2></div>
            <div id="forum-search">
                <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id'=>'forum-search-form',
                        'action'=>'/forum/search'
                    ));

                    echo CHtml::textField('Forum[phrase]', '', array('class'=>'textField')) . CHtml::submitButton(Yii::t('site', 'Search'), array('class'=>'button big'));

                    $this->endWidget();
                ?>
            </div>
            <div class="body" id="search-container">
                <div id="results" class="list">
                    <?php
                        $cs = Yii::app()->clientScript;
                        $cs->registerScript('forum-search-form',"
                            activateSearchForm('forum-search-form');
                        ");
                    ?>
                </div>
                <div class="options">
                    <h2><?php echo Yii::t('ContentModule.forum', 'Didn\'t find what you were looking for?'); ?></h2>
                    <div class="option question"><a href="/forum/new/question"><i></i><h3><?php echo Yii::t('ContentModule.forum', 'Ask a question'); ?></h3></a></div>
                    <div class="option idea"><a href="/forum/new/idea"><i></i><h3><?php echo Yii::t('ContentModule.forum', 'Submit an idea'); ?></h3></a></div>
                    <div class="option issue"><a href="/forum/new/issue"><i></i><h3><?php echo Yii::t('ContentModule.forum', 'Report an issue'); ?></h3></a></div>
                </div>
            </div>
        </div>
        <?php
            $questions = 0;
            $ideas = 0;
            $issues = 0;

            foreach ($dataProvider as $post)
            {
                if ($post['type'] == Post::TYPE_QUESTION)
                {
                    if ($questions++ == 0){
                        echo '<div class="frame">
                                <div class="title""><h3>' . CHtml::link(Yii::t('ContentModule.forum', 'Questions'), '/forum/questions') . '</h3></div>
                                <div class="body list">';
                    }
                }
                else if ($post['type'] == Post::TYPE_ISSUE)
                {
                    if ($questions > 0){
                        $questions = 0;
                        echo '</div>
                         </div>';
                    }
                    if ($issues++ == 0){
                        echo '<div class="frame">
                                <div class="title""><h3>' . CHtml::link(Yii::t('ContentModule.forum', 'Issues'), '/forum/issues') . '</h3></div>
                                <div class="body list">';
                    }
                }

                echo '<div class="item  ' . Post::getTypeTitle($post['type']) . '">' .
                        ($post['response_type'] > Post::RESPONSE_NEW ?
                            '<div class="status ' . Post::getResponseTitle($post['response_type']) . '">' . Post::getResponseText($post['response_type'], $post['type']) . '</div>'
                            : ''
                        ) .
                        '<div class="icon"><i></i>' .
                            '<span class="answer" title="' . Yii::t('ContentModule.forum', '{n} vote|{n} votes', $post['votesCount']) . '">' . '<i></i>' . $post['votesCount'] . '</span>' .
                        '</div>' .
                        '<div class="info">' .
                            '<h3>' . CHtml::link($post['title'], '/forum/' . $post['slug']) . '</h3>' .
                            '<span class="meta">' .
                                $post['first_name'] . ' ' . mb_substr($post['last_name'], 0, 1, 'UTF-8') . '.' .
                                '<span class="date" title="' . ContentModule::getFormattedFullDate($post['publish_date']) . '">' .
                                    '<i></i>' . ContentModule::getFormattedRelativeDate($post['publish_date']) .
                                    '<span class="count" title="' . Yii::t('ContentModule.comment', '{n} comment|{n} comments', $post['commentsCount']) . '">' .
                                        '<i></i>' . $post['commentsCount'] .
                                    '</span>' .
                                '</span>' .
                            '</span>' .
                        '</div>' .
                     '</div>';
            }

            if ($issues > 0){
                echo '</div>
                 </div>';
            }
        ?>
    </div>
</div>