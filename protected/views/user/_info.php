<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'info-form',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
    'focus'=>array($model,'last_name'),
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

    <div class="row last-name"><i></i><?php
        echo CHtml::tag('span', array('class'=>'errorBox'),
                $form->error($model,'last_name') .
                CHtml::tag('span', array('class'=>'arrow-border east'), '') .
                CHtml::tag('span', array('class'=>'arrow east'),'')
            ) .
            CHtml::label(
                    Yii::t('user', 'Enter your <b>real last name</b>.') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'User_email', array('class'=>'required')) .
            $form->textField($model, 'last_name');
    ?></div>

    <div class="row name"><i></i><?php
        echo CHtml::tag('span', array('class'=>'errorBox'),
                $form->error($model,'first_name') .
                CHtml::tag('span', array('class'=>'arrow-border east'), '') .
                CHtml::tag('span', array('class'=>'arrow east'),'')
            ) .
            CHtml::label(
                    Yii::t('user', 'Enter your <b>real name</b>. Every time you login we will greet you with your lovely name!') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'User_password', array('class'=>'required')) .
            $form->textField($model, 'first_name');
    ?></div>

    <div class="major-buttons text-right">
        <?php echo CHtml::submitButton(Yii::t('site', 'Complete'), array('class'=>'button primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->