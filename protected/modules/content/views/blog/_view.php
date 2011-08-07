<div class="post">
    <h2><?php echo CHtml::link($data->title, '/blog/' . $data->slug, array('title'=>$data->title)); ?></h2>

	<span class="meta"><?php echo Yii::t('ContentModule.blog', 'Published on') . ' ' . Yii::app()->dateFormatter->format('d MMMM yyyy', $data->publish_date) . '. ' . Yii::t('ContentModule.blog', 'Author') . ': ' . $data->author->first_name . ' ' . mb_substr($data->author->last_name, 0, 1, 'UTF-8') . '.'; ?></span>

	<?php
		echo $data->content;

		echo '<span class="meta last"><span class="tags">';

		if (strlen($data->tags) > 0){
			echo Yii::t('ContentModule.blog', 'Tagged as') . ':';
			$tags = Tag::model()->findAll('name in (\'' . implode('\',\'', $data->tagsAsArray()) . '\')');

			foreach($tags as $tag){
				echo CHtml::link($tag->name, '/blog/tags/' . $tag->slug);
			}
		}

		echo '</span>' . CHtml::tag('span', array('class'=>'commentsCount'), CHtml::link(Yii::t('ContentModule.blog', 'COMMENTS') . ': ' . $data->commentsCount, '/blog/' . $data->slug . '/#comments'));

		echo '</span>';
	?>
</div>