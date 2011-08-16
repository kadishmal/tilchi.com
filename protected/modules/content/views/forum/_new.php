<div id="forum">
    <div id="<?php echo $type; ?>">
        <?php
			$titleId = $uctype . '_title';
			$titleName = $uctype . '[title]';
			$contentId = $uctype . '_content';
			$contentName = $uctype . '[content]';

            $form = $this->beginWidget('CActiveForm', array(
                'id'=>'forum-new-form'
            ));

            echo CHtml::label($title, $titleId, array('class'=>'h2')) .
                 CHtml::textField($titleName, $model->title, array('class'=>'textField')) .
                 CHtml::label($description, $contentId, array('class'=>'h3'));

            $this->widget('application.modules.content.extensions.tinymce.ETinyMce', array(
				'name'=>$contentName,
				'id'=>$contentId,
				'height'=>'200px',
				'language'=>'ru',
                'useSwitch'=>false,
				'contentCSS'=>$this->module->cssAssetUrl . '/tinyContent.css',
				'value'=>$model->content,
				'plugins'=>array('spellchecker'),
                'useSwitch'=>false,
                'useCompression'=>true,
				'options'=>array(
					'theme'=>'advanced',
                    'skin'=>'o2k7',
                    'theme_advanced_toolbar_location'=>'bottom',
                    'theme_advanced_toolbar_align'=>'center',
                    'theme_advanced_buttons1'=>"bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,cleanup",
                    'theme_advanced_buttons2'=>'',
                    'theme_advanced_buttons3'=>''
				)
			));

            echo CHtml::tag('div',
                array('class'=>'top-10 text-right'),
                CHtml::link(Yii::t('ContentModule.blog', 'Cancel'), '/forum/' . $model->slug, array('class'=>'link-button', 'tabindex'=>'-1')) .
				CHtml::submitButton($buttonText, array('class'=>'button primary'))
            );

            $this->endWidget();
        ?>
    </div>
</div>