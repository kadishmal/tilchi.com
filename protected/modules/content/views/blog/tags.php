<?php
$this->breadcrumbs=array(
	Yii::t('ContentModule.blog', 'Blog')=>'/blog',
);
?>

<div id="blog">
    <h2><?php echo Yii::t('ContentModule.blog', 'Tags') ?></h2>
    <div id="tags">
        <?php
        $smallest = Tag::FONT_SMALLEST;
        $largest = Tag::FONT_LARGEST;
        $unit = Tag::FONT_UNIT;

        $counts = array();
        $real_counts = array();

        foreach ($dataProvider->getData() as $key => $tag){
            $real_counts[$key] = $tag->frequency;
            $counts[$key] = Tag::default_topic_count_scale($tag->frequency);
        }

        $min_count = min($counts);
        $spread = max($counts) - $min_count;

        if ($spread <= 0)
            $spread = 1;

        $font_spread = $largest - $smallest;

        if ($font_spread < 0)
            $font_spread = 1;

        $font_step = $font_spread / $spread;

        foreach ($dataProvider->getData() as $key => $tag)
        {
            $count = $counts[$key];
            
            echo CHtml::link($tag->name, '/blog/tags/' . $tag->slug, array('style'=>'font-size:' .
                ( $smallest + ( ( $count - $min_count ) * $font_step ) ) . $unit
            ));
        }
        ?>
    </div>
</div>
