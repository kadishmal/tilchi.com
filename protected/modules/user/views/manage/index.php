<?php
    $this->pageTitle = Yii::t('UserModule.user', 'User management') . ' | ' . Yii::app()->name;

    echo CHtml::link(Yii::t('UserModule.user', 'Permission management'), array('permissions')) . '<br />';
    echo CHtml::link(Yii::t('UserModule.user', 'Settings management'), array('settings'));
?>
