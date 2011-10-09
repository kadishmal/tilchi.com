<?php
    $this->pageTitle = Yii::t('tilchi', 'Translations') . ' | ' . Yii::app()->name;
?>

<div id="tilchi">
     <div class="tilchi-body">
         <div class="frame">
            <div class="title"><h2><?php echo Yii::t('tilchi', 'Latest translations');?></h2></div>
			<div class="body">
			<?php $this->widget('zii.widgets.CListView', array(
				'id'=>'translations-list',
				'dataProvider'=>$dataProvider,
				'itemView'=>'_translations'
			)); ?>
			</div>
        </div>
    </div>
</div>