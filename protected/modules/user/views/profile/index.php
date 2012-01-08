<?php
$this->pageTitle = Yii::t('UserModule.user', 'My profile') . ' | ' . Yii::app()->name;

Yii::app()->clientScript->registerScript('enable-profile-edit', "
    enableProfileEdit();
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
	<div class="row">
		<div class="th"><?php echo Yii::t('UserModule.user', 'Notifications'); ?></div>
		<div class="td">
			<div class="settings checkbox"><input type="checkbox" id="subscr_post_comments" <?php echo $model->settings->subscr_post_comments ? 'checked' : ''; ?> /><label for="subscr_post_comments"><?php echo Yii::t('UserModule.user', 'Comments on my posts'); ?></label><span class="sprite msg" title="<?php echo Yii::t('UserModule.user', 'Receive email notifications, if someone comments on a post I have created or I have previously commented on.'); ?>"></span></div>
		</div>
	</div>
    <div class="row">
        <div class="th"><?php echo Yii::t('UserModule.user', 'Settings'); ?></div>
        <div class="td">
            <div class="settings checkbox"><input type="checkbox" id="ajax_search" <?php echo $model->settings->ajax_search ? 'checked' : ''; ?> /><label for="ajax_search"><?php echo Yii::t('UserModule.user', 'Activate Instant Translation'); ?></label><span class="sprite msg" title="<?php echo Yii::t('UserModule.user', 'Instant Translation allows to translate without refreshing or redirecting to another page.'); ?>"></span></div>
        </div>
    </div>
    <div class="row">
        <div class="th"></div>
        <div class="td">
            <div class="settings checkbox"><input type="checkbox" id="save_search_history" <?php echo $model->settings->save_search_history ? 'checked' : ''; ?> /><label for="save_search_history"><?php echo Yii::t('UserModule.user', 'Save my translation history'); ?></label><span class="sprite msg" title="<?php echo Yii::t('UserModule.user', 'You can easily see the statistics of which words you have already searched for and learn from them.'); ?>"></span></div>
        </div>
    </div>
    <div class="row">
        <div class="th"></div>
        <div class="td">
            <div class="settings checkbox"><input type="checkbox" id="enable_shift_for_letters" <?php echo $model->settings->enable_shift_for_letters ? 'checked' : ''; ?> /><label for="enable_shift_for_letters"><?php echo Yii::t('UserModule.user', 'Use SHIFT key to input Kyrgyz letters'); ?></label><span class="sprite msg" title="<?php echo Yii::t('UserModule.user', 'For ease of use, you can press SHIFT+о to input Kyrgyz letter \'ө\', or SHIFT+н or SHIFT+у to input Kyrgyz letters \'ң\' or \'ү\', respectively.'); ?>"></span></div>
        </div>
    </div>
</div>