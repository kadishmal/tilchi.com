<?php
    $this->pageTitle = Yii::t('tilchi', 'Online Kyrgyz dictionary') . ' | ' . Yii::app()->name;

    if (Yii::app()->user->getState('enable_shift_for_letters'))
    {
        Yii::app()->clientScript->registerScript('enable-shift-for-letters', 'listenToLetters();');
    }
?>

<div id="tilchi">
     <div class="tilchi-body">
         <div class="frame">
             <div class="title"><h2><?php
				 echo Yii::t('tilchi', 'Dictionary');

				 $lang = Yii::app()->language;

                 $langs = CHtml::listData(Language::model()->cache(2592000)->findAll(array(
                     'select'=>'id, ' . $lang
                 )), 'id', $lang);

                 $fromLang = isset(Yii::app()->request->cookies['Tilchi_fromLang']) ?
                     Yii::app()->request->cookies['Tilchi_fromLang']->value : 1;

                 $toLang = isset(Yii::app()->request->cookies['Tilchi_toLang']) ?
                     Yii::app()->request->cookies['Tilchi_toLang']->value : 2;

                 echo CHtml::dropDownList('fromLang', $fromLang, $langs)
                     . CHtml::tag('span', array('style'=>'margin:0 10px', 'class'=>'sprite switch'), '')
                     . CHtml::dropDownList('toLang', $toLang, $langs);
			 ?></h2></div><div id="tilchi-search">
                 <?php
                     $form = $this->beginWidget('CActiveForm', array(
                         'id'=>'tilchi-search-form',
                         'action'=>'/site/search',
				  	 	 'focus'=>'#Tilchi_phrase'
                     ));

                     echo CHtml::hiddenField('Tilchi[fromLang]', $fromLang) .
				  		 CHtml::hiddenField('Tilchi[toLang]', $toLang) .
						 CHtml::textField('Tilchi[phrase]', '', array('class'=>'textField', 'autocomplete'=>'off')) .
						 CHtml::tag('span', array('class'=>'button blue'), CHtml::submitButton(Yii::t('site', 'Search')));

                     $this->endWidget();
                 ?><div id="results"></div>
             </div><?php
                 if (Yii::app()->user->getState('enable_ajax_search'))
                 {
                     echo CHtml::tag('div', array('id'=>'search-container', 'class'=>'body'), false, false)
                         . CHtml::tag('div', array('id'=>'translation'), '');

                     if (Yii::app()->user->checkAccess('translator'))
                     {
                         echo CHtml::tag('div', array('id'=>'add-translation'),
                                 CHtml::link(Yii::t('tilchi', 'Add translation'), '', array('id'=>'add-text', 'class'=>'block-button'))
                                 , false
                             );

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

                         echo CHtml::closeTag('div');    // close <div id="add-translation">

                         Yii::app()->clientScript->registerScript('allow-add-translation', "activateTranslation();");
                     }

                     // close <div class="body">
                     echo CHtml::closeTag('div');

                     Yii::app()->clientScript->registerScript('tilchi-search', "activateTilchiSearch('tilchi-search-form');");
                 }
                 else{
                     Yii::app()->clientScript->registerScript('tilchi-search', "enableLanguageSwitch();");
                 }
             ?>
        </div>
    </div>
</div>