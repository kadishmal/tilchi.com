<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'registration-form',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
    'focus'=>array($model,'email'),
    'clientOptions'=>array('validateOnSubmit'=>true, 'afterValidateAttribute'=>'js:function(form, attribute, data, hasError){
        if(hasError){
            $("#" + attribute.id).siblings(".errorBox").show();
        }
        else{
            $("#" + attribute.id).siblings(".errorBox").hide();
        }
    }')
)); ?>

	<?php echo $form->errorSummary($model); ?>

    <div class="row email"><i></i><?php
        echo CHtml::tag('span', array('class'=>'errorBox'),
                $form->error($model,'email') .
                CHtml::tag('span', array('class'=>'arrow-border east'), '') .
                CHtml::tag('span', array('class'=>'arrow east'),'')
            ) .
            CHtml::label(
                    Yii::t('user', 'Using this <b>email address</b> you can restore your account if you forget your password, receive notifications, and other important information.') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'User_email', array('class'=>'required')) .
            $form->textField($model,'email', array('type'=>'email'));
    ?></div>

    <div class="row password"><i></i><?php
        echo CHtml::tag('span', array('class'=>'errorBox'),
                $form->error($model,'password') .
                CHtml::tag('span', array('class'=>'arrow-border east'), '') .
                CHtml::tag('span', array('class'=>'arrow east'),'')
            ) .
            CHtml::label(
                    Yii::t('user', 'To have a strong <b>password</b>, it should consist of <b>6 or more</b> characters that you can remember. You can later use it to access your account at our web site.') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'User_password', array('class'=>'required')) .
            $form->passwordField($model,'password');
    ?></div>

    <div class="major-buttons text-right">
        <?php echo CHtml::submitButton(Yii::t('site', 'Create my account!'), array('class'=>'button primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->