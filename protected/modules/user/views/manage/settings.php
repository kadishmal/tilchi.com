<?php
    $this->pageTitle = Yii::t('UserModule.user', 'Settings management') . ' | ' .
        Yii::t('UserModule.user', 'User management') . ' | ' . Yii::app()->name;

    Yii::app()->clientScript->registerScript('add-new-settings',
        "UIMessages['addNew'] = '" . Yii::t('UserModule.settings', 'Add a new setting') . "';
        UIMessages['submit'] = '" . Yii::t('UserModule.settings', 'Submit') . "';
        UIMessages['cancel'] = '" . Yii::t('UserModule.settings', 'Cancel') . "';"
        . ($model->hasErrors() ? "$('.button #add-settings').click();" : "") . "
        UIMessages['save'] = '" . Yii::t('UserModule.settings', 'Save') . "';
        UIMessages['confirm'] = '" . Yii::t('UserModule.settings', 'Confirm') . "';
        UIMessages['confirmMessage'] = '" . Yii::t('UserModule.settings', 'Are you sure you would like to delete this setting? Deleting the setting will remove all users\' preferences associated with this setting.') . "';
        UIMessages['yes'] = '" . Yii::t('UserModule.settings', 'Yes') . "';
        UIMessages['close'] = '" . Yii::t('UserModule.permission', 'Close') . "';
    ");
?>

<h1><?php echo Yii::t('UserModule.user', 'Settings management'); ?></h1>

<div id="settings" class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'site-settings-form',
        'focus'=>array($model, 'name'),
        'htmlOptions'=>array('class'=>'block-form'),
        'enableAjaxValidation'=>true
    )); ?>
    <?php echo $form->hiddenField($model, 'id'); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row first">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'Name'), 'SiteSettings_name'); ?>
        <?php echo $form->textField($model, 'name'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'Module'), 'SiteSettings_module'); ?>
        <?php echo $form->textField($model, 'module'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'Type'), 'SiteSettings_data_type'); ?>
        <?php echo $form->dropDownList($model,'data_type',
            array(Yii::t('UserModule.settings', 'Checkbox'), Yii::t('UserModule.settings', 'Number'),
                Yii::t('UserModule.settings', 'Text')),
            array(
                'options'=>array(
                    'integer'=>array('selected'=>true)
                )
            )
        ); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'Default'), 'SiteSettings_default_value'); ?>
        <?php echo $form->textField($model, 'default_value',
            array('placeholder'=>Yii::t('UserModule.settings', 'Default value'))
        ); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'Permission'), 'SiteSettings_auth_item'); ?>
        <?php echo $form->textField($model, 'auth_item'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'Label'), 'SiteSettings_en_label'); ?>
        <?php echo $form->textArea($model, 'en_label',
            array('placeholder'=>Yii::t('UserModule.settings', 'Label of the setting'))
        ); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'Hint'), 'SiteSettings_en_hint'); ?>
        <?php echo $form->textArea($model, 'en_hint',
            array('placeholder'=>Yii::t('UserModule.settings', 'Hint to display on mouse hover'))
        ); ?>
    </div>

    <div class="row last">
        <?php echo CHtml::label(Yii::t('UserModule.settings', 'On load'), 'SiteSettings_on_login'); ?>
        <?php echo CHtml::checkBox('SiteSettings[on_login]', false, array('onclick'=>'this.value = this.checked ? 1 : 0')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>

<div class="text-right">
    <span class="button green"><a href="#" id="add-settings" onclick="showMessage(UIMessages['addNew'], $('#settings').clone().css({display:'block'}), UIMessages['submit'], addSiteSettings, UIMessages['cancel']);return false;"><?php echo Yii::t('UserModule.settings', 'Add a new setting'); ?></a></span>
</div>

<?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'site-settings',
        'dataProvider'=>$model->search(),
        'filter'=>$model,
        'columns'=>array(
            array(
                'class'=>'CButtonColumn',
                'template'=>'{edit}',
                'buttons'=>array(
                    'edit'=>array(
                        'label'=>Yii::t('UserModule.settings', 'Edit'),
                        'click'=>'function(){ editSettings($(this)); }'
                    )
                ),
            ),
            'name',
            'module',
            'data_type',
            'default_value',
            'auth_item',
            'on_login',
            array(
                'class'=>'CButtonColumn',
                'template'=>'{delete}',
                'buttons'=>array(
                    'delete'=>array(
                        'url'=>'',
                        'click'=>'function(){ deleteSiteSettings($(this)); return false; }'
                    )
                ),
            ),
        ),
    ));
?>