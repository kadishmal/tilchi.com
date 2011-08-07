<?php
$this->breadcrumbs=array(
	Yii::t('ContentModule.blog', 'Blog')=>'/blog',
);
?>

<div id="blog">
    <h2><?php echo Yii::t('ContentModule.comment', 'Comments Management') ?></h2>
    <div class="filters">
    <?php echo CHtml::link(Yii::t('ContentModule.comment', 'All'), '/blog/comments/all', array('class'=>($_GET['param'] == 'all' ? 'active' : ''))) .
            CHtml::link(Yii::t('ContentModule.comment', 'Pending') . CHtml::tag('span', array('class'=>'count'), '(' . Comment::model()->count('status = ' . Comment::STATUS_PENDING) . ')'), '/blog/comments/pending', array('class'=>($_GET['param'] == 'pending' ? 'active' : ''))) .
             CHtml::link(Yii::t('ContentModule.comment', 'Approved') . CHtml::tag('span', array('class'=>'count'), '(' . Comment::model()->count('status = ' . Comment::STATUS_APPROVED) . ')'), '/blog/comments/approved', array('class'=>($_GET['param'] == 'approved' ? 'active' : ''))) .
             CHtml::link(Yii::t('ContentModule.comment', 'Spam') . CHtml::tag('span', array('class'=>'count'), '(' . Comment::model()->count('status = ' . Comment::STATUS_SPAM) . ')'), '/blog/comments/spam', array('class'=>($_GET['param'] == 'spam' ? 'active' : ''))) .
             CHtml::link(Yii::t('ContentModule.comment', 'Trash') . CHtml::tag('span', array('class'=>'count'), '(' . Comment::model()->count('status = ' . Comment::STATUS_TRASH) . ')'), '/blog/comments/trash', array('class'=>'last' . ($_GET['param'] == 'trash' ? ' active' : '')));
    ?>
    </div>

<?php $this->widget('zii.widgets.CListView', array(
    'id'=>'comments-list',
	'dataProvider'=>$dataProvider,
	'itemView'=>'_adminComments'
)); ?>

</div>
