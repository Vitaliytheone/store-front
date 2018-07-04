USE `panels`;

UPDATE `content` SET `text` = 'Please check your email <strong>{{email}}</strong> and follow payment approve link' WHERE `name` = 'paypal_verify_note';
