<?php
$this->pageTitle = Yii::t('UserModule.user', 'My profile') . ' | ' . Yii::app()->name;

Yii::app()->clientScript->registerScript('enable-profile-edit', "
    enableSettingsEdit();
");

$this->menu=array(
	array('label'=>'Изменить мои данные', 'url'=>array('edit', 'c'=>'profile'), 'visible'=>!Yii::app()->user->isGuest),
	array('label'=>'Сменить мой пароль', 'url'=>array('edit', 'c'=>'pwd'), 'visible'=>!Yii::app()->user->isGuest),
);
?>
<div id="profile">
	<h1><?php echo Yii::t('UserModule.user', 'My profile'); ?></h1>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('first_name') ?></div>
		<div class="td"><b><?php echo $model->first_name . ' ' . $model->last_name; ?></b> <?php echo CHtml::link(Yii::t('UserModule.user', 'Change'), '/user/edit/profile', array('class'=>'link-button')); ?></div>
	</div>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('email') ?></div>
		<div class="td"><b><?php echo $model->email; ?></b></div>
	</div>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('password') ?></div>
		<div class="td"><?php echo '******' . CHtml::link(Yii::t('UserModule.user', 'Change'), '/user/edit/password', array('class'=>'link-button mLeft10')); ?></div>
	</div>
    <hr />
    <?php
        $prevGroup = '';

        foreach($userAccessibleSettings as $setting)
        {
            $group = substr($setting->module, strlen($this->module->id . '.' . $this->id . '.'));
            $CSSClass = SiteSettings::getCSSClass($setting->data_type);
            $htmlOptions = array('type'=>$CSSClass, 'id'=>$setting->name);

            if ($setting->data_type == SiteSettings::TYPE_CHECKBOX)
            {
                if ($setting->default_value)
                {
                    $htmlOptions['checked'] = 'checked';
                }
            }
            else{
                $htmlOptions['value'] = $setting->default_value;
            }

            echo CHtml::tag('div', array('class'=>'row'),
                CHtml::tag('div', array('class'=>'th'), ($group != $prevGroup ? Yii::t('UserModule.user', ucfirst($group)) : ''))
                . CHtml::tag('div', array('class'=>'td'),
                    CHtml::tag('div', array('class'=>'settings ' . $CSSClass),
                        CHtml::tag('input', $htmlOptions, false)
                        . CHtml::label(Yii::t('UserModule.user', $setting->en_label), $setting->name)
                        . CHtml::tag('span', array('class'=>'sprite msg', 'title'=>Yii::t('UserModule.user', $setting->en_hint)), '')
                    )
                )
            );

            $prevGroup = $group;
        }
    ?>
</div>