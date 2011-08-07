<?php
$this->pageTitle = Yii::t('ContentModule.forum', 'Forum') . ' | ' . Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('ContentModule.forum', 'Forum'),
);?>
<div id="forum">
     <div class="frame">
        <div class="title""><h2><?php echo Yii::t('ContentModule.forum', 'Forums'); ?></h2></div>
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
            <div id="results">
                <?php
                    $cs = Yii::app()->clientScript;
                    $cs->registerScript('forum-search-form',"
                        activateSearchForm('forum-search-form');
                    ");
                    
                    if (!empty($dataProvider)){
                        $this->widget('zii.widgets.CListView', array(
                            'dataProvider'=>$dataProvider,
                            'itemView'=>'_searchResult',
                        ));
                    }
                ?>
            </div>
            <div class="options">
                <h2><?php echo Yii::t('ContentModule.forum', 'Didn\'t find what you were looking for?'); ?></h2>
                <div class="option question"><a href="/forum/new/question"><i></i><h3><?php echo Yii::t('ContentModule.forum', 'Ask a question'); ?></h3></a></div>
                <div class="option idea"><a href="/forum/new/idea"><i></i><h3><?php echo Yii::t('ContentModule.forum', 'Submit an idea'); ?></h3></a></div>
                <div class="option discussion" style="display:none"><a href="/forum/discussions"><i></i><h3><?php echo Yii::t('ContentModule.forum', 'Join discussions'); ?></h3></a></div>
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
                        'itemView'=>'_question',
                        'summaryText'=>''
                    ));
                }
            ?>
        </div>
    </div>
</div>