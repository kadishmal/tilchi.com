<?php
    $this->pageTitle = $phrase . ' | ' . $fromLang . ' ' . Yii::t('tilchi', 'Dictionary') . ' | ' . Yii::app()->name;

	Yii::app()->clientScript->registerScript('tilchi-search',"
		activateTilchiSearch('tilchi-search-form');
	");
?>

<div id="tilchi">
     <div class="tilchi-body">
         <div class="frame">
            <div class="title"><h2><?php echo Yii::t('tilchi', 'Dictionary') . ' ' . $fromLang; ?></h2></div>
            <div class="body" id="search-container" style="display:block">
				<div id="translation">
					<h3><?php echo $phrase; ?></h3>
					<div id="translation-body">
					<?php
						if (isset($results['translations']))
						{
							if (count($results['translations']) > 0)
							{
								foreach ($results['translations'] as $translation)
								{
									echo '<h4 class="language">' . Yii::t('tilchi', 'Translation') . ': ' . $translation['language'] . '</h4><p>' . $translation['phrase'] . '</p>';
								}
							}
							else{
								echo CHtml::tag('p', array('class'=>'msg'), Yii::t('tilchi', 'The word <b>_phrase</b> exists in our database, but no translation has been found for it. If you know the translation, help us improve Tilchi.com by adding your translation. You will be recorded as a contributor.', array('_phrase'=>$phrase)));
							}
						}
						else{
							echo CHtml::tag('p', array(), Yii::t('tilchi', 'The phrase <b>_phrase</b> has not been found, but we have already added it to our to-translate list.', array('_phrase'=>$phrase)));
						}
					?>
					</div>
				</div>
				<?php if (isset($results['translations'])) : ?>
				<div id="add-translation">
					<a class="block-button" id="add-text"><?php echo Yii::t('tilchi', 'Add translation'); ?></a>
                    <?php
					if (Yii::app()->user->isGuest)
					{
						echo CHtml::tag('p', array('id'=>'add-translation-form'), Yii::t('tilchi', 'In order to add translations you need to be <a href="_url">logged in</a>. If you are not our member yet, <a href="/user/register">join us</a> and start translating!', array('_url'=>Yii::app()->user->loginUrl[0])));
					}
					else{
						$form = $this->beginWidget('CActiveForm', array(
							'id'=>'add-translation-form',
							'action'=>'/site/new'
						));

						$langs = CHtml::listData(Language::model()->findAll(array(
							'select'=>'id, ' . $sysLang
						)), 'id', $sysLang);

						echo CHtml::hiddenField('Tilchi[fromLang]', $fromLangId) .
							CHtml::hiddenField('Tilchi[phrase]', $phrase) .
							CHtml::label(Yii::t('tilchi', 'Choose a language to translate to'), 'Tilchi_translation', array('class'=>'h3')) .
							CHtml::dropDownList('Tilchi[toLang]', '', $langs, array('id'=>'Tilchi_toLang')) .
							CHtml::label(Yii::t('tilchi', 'Enter the exact translation'), 'Tilchi_translation', array('class'=>'h3')) .
							CHtml::textField('Tilchi[translation]', '', array('class'=>'textField')) .
							CHtml::tag('div', array('id'=>'translationResults'), '') .
							CHtml::tag('div', array('class'=>'hint'), Yii::t('tilchi', 'Please enter the precise translation and <b>only one at a time</b>. If there are more than one meanings, enter only one. Other meanings can be entered later. If you think there is no exact translation, leave this field empty and instead enter the explanation below.')) .
							CHtml::label(Yii::t('tilchi', 'Enter the explanation'), 'Tilchi_explanation', array('class'=>'h3 top-10 block-button', 'id'=>'add-explanation')) .
							'<div id="Tilchi_explanation_block">';
						$this->widget('application.modules.content.extensions.tinymce.ETinyMce', array(
							'name'=>'Tilchi[explanation]',
							'id'=>'Tilchi_explanation',
							'height'=>'100px',
							'language'=>'ru',
							'useSwitch'=>false,
							//'contentCSS'=>'/tinyContent.css',
							//'value'=>$model->content,
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

						echo CHtml::tag('div', array('class'=>'hint'), Yii::t('tilchi', 'If you think that the translation has to be clarified, please enter its explanation below. If there is no need for explanation, leave this field empty.')) .
							'</div>' .
							CHtml::tag('div', array('class'=>'top-10 text-right'),
								CHtml::link(Yii::t('tilchi', 'Cancel'), '', array('class'=>'link-button')) .
								CHtml::submitButton(Yii::t('tilchi', 'Add'), array('class'=>'button primary'))
							);

						$this->endWidget();
					}
                ?>
                </div>
				<?php endif; ?>
            </div>
        </div>
    </div>
</div>