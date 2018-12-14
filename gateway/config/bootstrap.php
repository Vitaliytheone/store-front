<?php
Yii::setAlias('@runtime', dirname(dirname(__DIR__)) . '/gateway/runtime/');
Yii::setAlias('@admin', dirname(dirname(__DIR__)) . '/gateway/modules/admin/');
Yii::setAlias('@paymentsLog', dirname(dirname(__DIR__)) . '/gateway/runtime/logs/payments');
Yii::setAlias('@themes', dirname(dirname(__DIR__)) . '/gateway/views/themes');
Yii::setAlias('@customThemes', dirname(dirname(__DIR__)) . '/gateway/views/themes/custom');
Yii::setAlias('@defaultThemes', dirname(dirname(__DIR__)) . '/gateway/views/themes/default');