<?php
    $this->pageTitle = Yii::t('UserModule.user', 'Permission management') . ' | ' .
        Yii::t('UserModule.user', 'User management') . ' | ' . Yii::app()->name;

    Yii::app()->clientScript->registerScript('manage-permissions', "enablePermissionManagement(); enableTabs('permissionsTab');");
?>

<div id="permissions">
    <h1><?php echo Yii::t('UserModule.user', 'Permission management'); ?></h1>

    <div class="tabContainer" id="permissionsTab">
        <div class="tabs">
            <?php
                echo CHtml::tag('span', array('class'=>'active', 'id'=>'operation'), Yii::t('UserModule.user', 'Operations'))
                    . CHtml::tag('span', array('id'=>'task'), Yii::t('UserModule.user', 'Tasks'))
                    . CHtml::tag('span', array('id'=>'role'), Yii::t('UserModule.user', 'Roles'))
                    . CHtml::tag('span', array('id'=>'assignment'), Yii::t('UserModule.user', 'Assignments'));
            ?>
        </div>
        <div class="tabContents">
            <div id="operation" class="active">
                <table class="input-table border-blue widthAuto tdNoPadding" id="operation-table">
                    <thead><tr><?php
                        echo CHtml::tag('th', array(), Yii::t('UserModule.user', 'Operations'))
                            . CHtml::tag('th', array(), Yii::t('UserModule.user', 'Description'))
                            . CHtml::tag('th', array(), '');
                        ?></tr></thead><tbody>
                <?php
                foreach($operations as $operation)
                {
                    echo CHtml::tag('tr', array(),
                        CHtml::tag('td', array(), CHtml::textField($operation->name, $operation->name, array('id'=>ManageController::OPERATION,'class'=>'name')))
                            . CHtml::tag('td', array(), CHtml::textField($operation->name, $operation->description, array('id'=>ManageController::OPERATION,'class'=>'description')))
                            . CHtml::tag('td', array(), CHtml::link(Yii::t('UserModule.user', 'Remove'), '', array('class'=>'remove link-button')))
                    );
                }
                ?><tr class="last"><td><?php echo CHtml::textField('', '', array('id'=>ManageController::OPERATION, 'class'=>'name')); ?></td>
                    <td><?php echo CHtml::textField('', '', array('id'=>ManageController::OPERATION, 'class'=>'description')); ?></td>
                    <td><?php echo CHtml::link(Yii::t('UserModule.user', 'Remove'), '', array('class'=>'remove link-button')); ?></td>
                </tr>
                </tbody>
                </table>
            </div>
            <div id="task">
                <table class="input-table border-blue widthAuto tdNoPadding" id="task-table">
                    <thead><tr><?php
                        echo CHtml::tag('th', array(), Yii::t('UserModule.user', 'Tasks'))
                            . CHtml::tag('th', array(), Yii::t('UserModule.user', 'Description'))
                            . CHtml::tag('th', array(), Yii::t('UserModule.user', 'Biz rule'))
                            . CHtml::tag('th', array(), '');
                        ?></tr></thead><tbody>
                <?php
                foreach($tasks as $task)
                {
                    echo CHtml::tag('tr', array(),
                        CHtml::tag('td', array(), CHtml::textField($task->name, $task->name, array('id'=>ManageController::TASK,'class'=>'name')))
                            . CHtml::tag('td', array(), CHtml::textField($task->name, $task->description, array('id'=>ManageController::TASK,'class'=>'description')))
                            . CHtml::tag('td', array(), CHtml::textField($task->name, $task->bizrule, array('id'=>ManageController::TASK,'class'=>'bizrule')))
                            . CHtml::tag('td', array(), CHtml::link(Yii::t('UserModule.user', 'Remove'), '', array('class'=>'remove link-button')))
                    );
                }
                ?><tr class="last"><td><?php echo CHtml::textField('', '', array('id'=>ManageController::TASK, 'class'=>'name')); ?></td>
                    <td><?php echo CHtml::textField('', '', array('id'=>ManageController::TASK, 'class'=>'description')); ?></td>
                    <td><?php echo CHtml::textField('', '', array('id'=>ManageController::TASK, 'class'=>'bizrule')); ?></td>
                    <td><?php echo CHtml::link(Yii::t('UserModule.user', 'Remove'), '', array('class'=>'remove link-button')); ?></td>
                </tr>
                </tbody>
                </table>
            </div>
            <div id="role">
                <table class="input-table border-blue widthAuto tdNoPadding" id="role-table">
                    <thead><tr><?php
                        echo CHtml::tag('th', array(), '')
                            . CHtml::tag('th', array(), Yii::t('UserModule.user', 'Roles'))
                            . CHtml::tag('th', array(), Yii::t('UserModule.user', 'Description'))
                            . CHtml::tag('th', array(), Yii::t('UserModule.user', 'Biz rule'))
                            . CHtml::tag('th', array(), '');
                        ?></tr></thead><tbody>
                <?php
                foreach($roles as $role)
                {
                    echo CHtml::tag('tr', array(),
                        CHtml::tag('td', array(), CHtml::link(Yii::t('UserModule.user', 'Expand'), '', array('class'=>'expand link-button')))
                            . CHtml::tag('td', array(), CHtml::textField($role->name, $role->name, array('id'=>ManageController::ROLE,'class'=>'name')))
                            . CHtml::tag('td', array(), CHtml::textField($role->name, $role->description, array('id'=>ManageController::ROLE,'class'=>'description')))
                            . CHtml::tag('td', array(), CHtml::textField($role->name, $role->bizrule, array('id'=>ManageController::ROLE,'class'=>'bizrule')))
                            . CHtml::tag('td', array(), CHtml::link(Yii::t('UserModule.user', 'Remove'), '', array('class'=>'remove link-button')))
                    );
                }

                echo CHtml::tag('tr', array('class'=>'last'),
                    CHtml::tag('td', array(), CHtml::link(Yii::t('UserModule.user', 'Expand'), '', array('class'=>'expand link-button')))
                        . CHtml::tag('td', array(), CHtml::textField('', '', array('id'=>ManageController::ROLE,'class'=>'name')))
                        . CHtml::tag('td', array(), CHtml::textField('', '', array('id'=>ManageController::ROLE,'class'=>'description')))
                        . CHtml::tag('td', array(), CHtml::textField('', '', array('id'=>ManageController::ROLE,'class'=>'bizrule')))
                        . CHtml::tag('td', array(), CHtml::link(Yii::t('UserModule.user', 'Remove'), '', array('class'=>'remove link-button')))
                );
                ?>
                </tbody>
                </table>
            </div>
            <div id="assignment">

            </div>
        </div>
    </div>
</div>