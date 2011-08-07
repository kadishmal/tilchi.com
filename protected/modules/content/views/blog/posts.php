<?php
$this->breadcrumbs=array(
	Yii::t('ContentModule.blog', 'Blog')=>'/blog',
);

?>

<div id="blog">

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
        array(
            'class'=>'CLinkColumn',
            'header'=>Yii::t('ContentModule.blog', 'Title'),
            'labelExpression'=>'$data->title',
            'urlExpression'=>'"/blog/edit/".$data->id'
        ),
        array(
            'name'=>Yii::t('ContentModule.blog', 'Author'),
            'value'=>'$data->author->last_name . " " . $data->author->first_name'
        ),
        array(
            'name'=>Yii::t('ContentModule.blog', 'Tags'),
            'value'=>'$data->tags'
        ),
        array(
            'name'=>Yii::t('ContentModule.blog', 'Date'),
            'value'=>'ContentModule::getFormattedDate($data->publish_date) . CHtml::tag("br") . $data->getStatusTitle()',
            'type'=>'html'
        )
    )
)); ?>

</div>
