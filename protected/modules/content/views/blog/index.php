<?php
$this->breadcrumbs=array(
	Yii::t('ContentModule.blog', 'Blog')=>'/blog',
);

?>

<div id="blog">

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
    'summaryText'=>''
)); ?>

</div>
