<?php
    $this->pageTitle = Yii::app()->name;
	$baseUrl = Yii::app()->baseUrl;

	Yii::app()->clientScript->registerCoreScript('cookie')->
		registerScriptFile($baseUrl . '/js/jquery.typing-0.2.0.min.js')->
		registerScriptFile($baseUrl . '/js/tilchi.js')->
		registerScript('tilchi-search',"
		activateTilchiSearch('tilchi-search-form');
	");
?>

<div id="tilchi">
     <div class="tilchi-body">
         <div class="frame">
            <div class="title"><h2><?php
				echo Yii::t('tilchi', 'Dictionary');

				$lang = Yii::app()->language;

				$langs = CHtml::listData(Language::model()->findAll(array(
					'select'=>'id, ' . $lang
				)), 'id', $lang);

				echo CHtml::dropDownList('fromLang', (isset(Yii::app()->request->cookies['Tilchi_fromLang']) ? Yii::app()->request->cookies['Tilchi_fromLang']->value : 1), $langs) .
					CHtml::tag('span', array('style'=>'margin:0 10px', 'class'=>'sprite switch'), '') . CHtml::dropDownList('toLang', (isset(Yii::app()->request->cookies['Tilchi_toLang']) ? Yii::app()->request->cookies['Tilchi_toLang']->value : 2), $langs);

			?></h2></div><div id="tilchi-search">
                <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id'=>'tilchi-search-form',
                        'action'=>'/site/search',
						'focus'=>'#Tilchi_phrase'
                    ));

                    echo CHtml::hiddenField('Tilchi[fromLang]') .
						CHtml::hiddenField('Tilchi[toLang]') .
						CHtml::textField('Tilchi[phrase]', '', array('class'=>'textField', 'autocomplete'=>'off')) .
						CHtml::submitButton(Yii::t('site', 'Search'), array('class'=>'button big'));

                    $this->endWidget();
                ?><div id="results"></div>
            </div><div class="body" id="search-container">
				<div id="translation"></div>
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

						echo CHtml::hiddenField('Tilchi[fromLang]') .
							CHtml::hiddenField('Tilchi[toLang]') .
							CHtml::hiddenField('Tilchi[phrase]') .
							CHtml::label(Yii::t('tilchi', 'Enter the exact translation'), 'Tilchi_translation', array('class'=>'h3')) .
							CHtml::textField('Tilchi[translation]', '', array('class'=>'textField', 'autocomplete'=>'off')) .
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
            </div>
        </div>
		<div class="frame">
			<div class="title"><h3><?php echo CHtml::link(Yii::t('tilchi', 'Latest translations'), '/site/translations'); ?></h3></div>
			<div class="body"><?php
				for($j = 0; $j < 3; ++$j)
				{
					$to = $j*10 + 10;

					echo '<span id="translations">';

					for($i = $to - 10; $i < $to; ++$i)
					{
						echo CHtml::tag('div', array('class'=>'phrase'),
							CHtml::link($translations[$i]->phrase->phrase, '/site/' . $translations[$i]->phrase->language->abbreviation . '/' . $translations[$i]->phrase->phrase) .
							CHtml::tag('span', array('class'=>'author'), $translations[$i]->user->first_name . ' ' . $translations[$i]->user->last_name)
						);
					}

					echo '</span>';
				}
			?>
			</div>
		</div>
    </div>
</div>