<div class="form">

<?php

$cs = Yii::app()->getClientScript();

$cs->registerScript('edit-post', "
	activateEditPost();
");

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'post-form',
	'enableAjaxValidation'=>false,
));

?>
	<?php echo $form->errorSummary($model); ?>

	<div id="post-body">

		<div class="row row-first">
			<?php echo $form->textField($model, 'title'); ?>
		</div>

		<div class="row">
			<?php
			$this->widget('application.modules.content.extensions.tinymce.ETinyMce', array(
				'name'=>'Post[content]',
				'id'=>'Post_content',
				'width'=>'560px',
				'language'=>'ru',
				'contentCSS'=>$this->module->cssAssetUrl . '/tinyContent.css',
				'value'=>$model->content,
				'plugins'=>array('preview, spellchecker'),
                'useSwitch'=>false,
                'useCompression'=>false,
				'options'=>array(
					'theme'=>'advanced',
					'skin'=>'o2k7',
					'theme_advanced_toolbar_location'=>'top',
					//'theme_advanced_buttons3_add'=>'preview',
					//'plugin_preview_pageurl'=>'plugins/preview/example.html'
				)
				//'editorTemplate'=>'full',
				//'fontSizes'=>array('11', '12', '13')
			));
			?>
		</div>

	</div>

	<div id="post-sidebar">
		<div class="postbox" id="publish-box">
			<h6><?php echo Yii::t('ContentModule.blog', 'Publish'); ?></h6>
			<div class="row minor-buttons">
				<?php
					if ($model->isNewRecord){
						echo CHtml::link(Yii::t('ContentModule.blog', 'Preview'), '#', array('class'=>'button extra', 'target'=>'_blank', 'id'=>'preview-post')) .
							CHtml::submitButton(Yii::t('ContentModule.blog', 'Draft'), array('class'=>'button bold right'));
					}
					else{
						echo CHtml::link(Yii::t('ContentModule.blog', 'Preview Changes'), '#', array('class'=>'button extra', 'target'=>'_blank', 'id'=>'preview-post'));
					}
				?>
			</div>

			<div class="row"><?php echo CHtml::label(Yii::t('ContentModule.blog', 'Status') . ':', 'Post_status') .
				CHtml::tag('span', array('id'=>'post-status', 'class'=>'black bold'), Yii::t('ContentModule.blog', $model->getStatusTitle())) .
				CHtml::link(Yii::t('ContentModule.blog', 'Edit'), '#', array('class'=>'edit-status')); ?>
				<div id="statusdiv">
					<?php echo $form->dropDownList($model, 'status', $model->getStatusList()); ?>
					<a href="#" class="save-status button"><?php echo Yii::t('ContentModule.blog', 'OK'); ?></a>
					<a href="#" class="cancel-status"><?php echo Yii::t('ContentModule.blog', 'Cancel'); ?></a>
				</div>
			</div>

			<div class="row publish-date row-last"><i></i><?php echo CHtml::label(Yii::t('ContentModule.blog', $model->isNewRecord ? 'Publish' : 'Published on'), 'Post_publish_date') .
				CHtml::tag('span', array('id'=>'timestamp', 'class'=>'black bold'), $model->isNewRecord ?
					Yii::t('ContentModule.blog', 'immediately') :
					$model->jj . ' ' . Yii::app()->dateFormatter->format('MMM', $model->publish_date) . ' ' . $model->aa . ' ' . Yii::t('ContentModule.blog', '@') . ' ' . $model->hh . ':' . $model->mn
				) .
				CHtml::link(Yii::t('ContentModule.blog', 'Edit'), '#', array('class'=>'edit-timestamp')); ?>
				<div id="timestampdiv">
					<div class="timestamp-wrap">
					<?php echo $form->textField($model, 'jj', array('autocomplete'=>'off', 'maxlength'=>2, 'size'=>2)) .
					$form->dropDownList($model, 'mm', $this->getMonthsList()) .
					$form->textField($model, 'aa', array('autocomplete'=>'off', 'maxlength'=>4, 'size'=>4)) . CHtml::tag('span', array('id'=>'at'), Yii::t('ContentModule.blog', '@')) .
					$form->textField($model, 'hh', array('autocomplete'=>'off', 'maxlength'=>2, 'size'=>2)) . ' : ' .
					$form->textField($model, 'mn', array('autocomplete'=>'off', 'maxlength'=>2, 'size'=>2))
					; ?>
					</div>
					<a href="#" class="save-timestamp button"><?php echo Yii::t('ContentModule.blog', 'OK'); ?></a>
					<a href="#" class="cancel-timestamp"><?php echo Yii::t('ContentModule.blog', 'Cancel'); ?></a>
				</div>
			</div>

			<div class="row major-buttons text-right">
				<?php echo CHtml::link(Yii::t('ContentModule.blog', 'Cancel'), '/blog' . ($model->isNewRecord ? '' : '/' . $model->slug), array('class'=>'link-button')) .
                CHtml::submitButton($model->isNewRecord ? Yii::t('ContentModule.blog', 'Publish') : Yii::t('ContentModule.blog', 'Save'), array('class'=>'button primary')); ?>
			</div>
		</div>
		<div class="postbox" id="tag-box">
			<h6><?php
                echo Yii::t('ContentModule.blog', 'Post Tags');
            ?></h6><?php
                echo $form->hiddenField($model, 'tags');
            ?><div id="tag-editor"><?php
                if (count(($tags = $model->tagsAsArray())))
                {
                    echo '<span class="tag">'
                        . implode('<span class="sprite delete-tag"></span></span><span class="tag">', $tags)
                        . '<span class="sprite delete-tag"></span></span>';
                }

                echo CHtml::textField('', '', array('data-url'=>'/blog/getDistinctTags', 'placeholder'=>Yii::t('ContentModule.blog', 'List of tags will appear upon typing.')));
            ?></div><div id="tag-results"></div>
		</div>
	</div>


<?php $this->endWidget(); ?>

</div><!-- form -->