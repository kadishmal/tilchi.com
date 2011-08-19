<?php
    $this->pageTitle = Yii::t('ContentModule.forum', 'Questions') . ' | ' .
            Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;
?>

<div id="forum">
    <div class="forum-body">
        <div class="frame">
            <div class="title"><h2><?php
                echo CHtml::link(Yii::t('ContentModule.forum', 'Forums'), '/forum') . ' / ' .
                    CHtml::link(Yii::t('ContentModule.forum', 'Support'), '/forum/support') . ' / ' .
                    Yii::t('ContentModule.forum', 'Questions');
            ?></h2></div>
            <div id="forum-search">
                <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id'=>'forum-search-form',
                        'action'=>'/forum/search'
                    ));

                    echo CHtml::hiddenField('Forum[scope]', Post::TYPE_QUESTION) .
							CHtml::textField('Forum[phrase]', '', array('class'=>'textField')) . CHtml::submitButton(Yii::t('site', 'Search'), array('class'=>'button big'));

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
        <div class="frame">
            <div class="title""></div>
            <div class="body list">
                <?php
                    if (!empty($dataProvider)){
                        $this->widget('zii.widgets.CListView', array(
                            'dataProvider'=>$dataProvider,
                            'itemView'=>'_view',
                            'summaryText'=>''
                        ));
                    }
                ?>
            </div>
        </div>
    </div>
</div>