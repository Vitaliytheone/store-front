<?php
    /* @var $name */
    /* @var $subject */
    /* @var $email */
    /* @var $message */
    /* @var $ip */
    /* @var $user_agent */
?>
Name: <?= $name ?> <br>
Subject: <?= $subject ?> <br>
E-mail: <?= $email ?><br><br>
Message: <?= $message ?><br><br>
IP: <?= $ip?> <br>
<?php if (!empty($user_agent)) : ?>
    Browser: <?= $user_agent?>
<?php endif; ?>
