<?php
    echo CHtml::tag('div', array('style'=>'background:#F4F4F4;padding:8px;margin-bottom:15px;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px;'),
            CHtml::tag('div', array('style'=>'margin:8px 0 10px 10px;font-size:24px;color:#555'), Yii::t('UserModule.login', 'Restoring my password')) .
            CHtml::tag('div', array('style'=>'border-radius:6px;-moz-border-radius:6px;-webkit-border-radius: 6px;border:1px solid #E9E9E9;background:#FFF;padding:15px 15px 20px 20px;'),
                   Yii::t('UserModule.login', 'Dear user_name,', array('user_name'=>$name)) . '<br /><br />' .
                   Yii::t('UserModule.login', 'This email was sent automatically by Tilchi.com in response to your request to recover your password. This is done for your protection; only you, the recipient of this email can take the next step in the password recover process.') .
                   '<br /><br />' .
                   Yii::t('UserModule.login', 'To reset your password and access your account either click on or copy and paste the following link into the address bar of your browser:') .
                   '<br />' . $url . '<br /><br />' .
                   Yii::t('UserModule.login', 'If you did not forget your password, please ignore this email.') . '<br /><br />' .
                   ($ip ? Yii::t('UserModule.login', 'The request was made from:') . '<br />' . $ip . '<br /><br />' : '') .
                   Yii::t('UserModule.register', 'Respectfully Yours,<br />Tilchi team.')
            )
    );
?>
