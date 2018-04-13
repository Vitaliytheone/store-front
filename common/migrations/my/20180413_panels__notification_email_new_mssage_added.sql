USE `panels`;
INSERT INTO `notification_email` (`subject`, `message`, `code`, `enabled`)
VALUES
  ('Paypal payment verification required', 'To confirm the authenticity of your billing account, please click on the link below. \n{{verify_link}}', 'paypal_verify', 1);
