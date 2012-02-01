<?php
    $home_url = 'http://tilchi.com';
    echo CHtml::tag('div', array('style'=>'background:#F4F4F4;padding:8px;margin-bottom:15px;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px;'),
            CHtml::tag('div', array('style'=>'margin:8px 0 10px 10px;font-size:24px;color:#555'), 'Tilchi.com') .
            CHtml::tag('div', array('style'=>'border-radius:6px;-moz-border-radius:6px;-webkit-border-radius: 6px;border:1px solid #E9E9E9;background:#FFF;padding:15px 15px 20px 20px;'),
                   Yii::t('UserModule.register', 'Hi user_name,', array('user_name'=>$name)) . '<br /><br />' .
                   Yii::t('UserModule.register', 'Thank you for joining us on the adventure of learning languages.<br /><br />You can now start using Tilchi.com community site in its fullest potential. Start translating directly from the <a href="home_url">Tilchi.com home page</a>.<br /><br />If you have questions regarding how to use the site or have suggestions, feel free to use our <a href="forum_url">Community Forum</a>.',
                          array('home_url'=>$home_url, 'forum_url'=>$home_url . '/forum')) . '<br /><br />' .
                   Yii::t('UserModule.register', 'Respectfully Yours,<br />Tilchi team.') . '<br /><br />' .
                   Yii::t('UserModule.register', 'P.S. We have Facebook and Twitter accounts! Join us!') .
                   '<br /><br /><a href="http://facebook.com/tilchi">http://facebook.com/tilchi</a><br /><a href="http://twitter.com/tilchi">http://twitter.com/tilchi</a>'
            )
    );
?>
