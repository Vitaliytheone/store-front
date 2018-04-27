<?php

use yii\db\Migration;

/**
 * Class m180424_144550_table_stores__fill_default_languages
 */
class m180424_144550_table_stores__fill_default_languages extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES
              (\'en\', \'cart\', \'title\', \'Cart\'),
              (\'en\', \'cart\', \'package\', \'Package name\'),
              (\'en\', \'cart\', \'details\', \'Details\'),
              (\'en\', \'cart\', \'price\', \'Price\'),
              (\'en\', \'cart\', \'remove\', \'Remove\'),
              (\'en\', \'cart\', \'total\', \'Total\'),
              (\'en\', \'cart\', \'total_price\', \'Total price\'),
              (\'en\', \'cart\', \'email\', \'Email\'),
              (\'en\', \'cart\', \'payment_method\', \'Payment method\'),
              (\'en\', \'cart\', \'button.continue\', \'Continue shopping\'),
              (\'en\', \'cart\', \'button.checkout\', \'Proceed to Checkout\'),
              (\'en\', \'cart\', \'no_orders\', \'No orders\'),
              (\'en\', \'cart\', \'quantity\', \'Quantity\'),
              (\'en\', \'contact\', \'header\', \'Get in touch\'),
              (\'en\', \'contact\', \'subject\', \'Subject\'),
              (\'en\', \'contact\', \'name\', \'Name\'),
              (\'en\', \'contact\', \'email\', \'Email\'),
              (\'en\', \'contact\', \'message\', \'Message\'),
              (\'en\', \'contact\', \'button.submit\', \'Send message\'),
              (\'en\', \'contact\', \'form.message.success\', \'Thank you! Your message has been sent\'),
              (\'en\', \'contact\', \'form.message.error\', \'Internal error. Try again later!\'),
              (\'en\', \'order\', \'title\', \'Add to cart\'),
              (\'en\', \'order\', \'package\', \'Package\'),
              (\'en\', \'order\', \'quantity\', \'Quantity\'),
              (\'en\', \'order\', \'price\', \'Price\'),
              (\'en\', \'order\', \'link\', \'Link\'),
              (\'en\', \'order\', \'button.back_url\', \'Cancel\'),
              (\'en\', \'order\', \'button.add_to_cart\', \'Add to Cart\'),
              (\'en\', \'order\', \'details\', \'Order details\'),
              (\'en\', \'order\', \'info\', \'Order info\'),
              (\'en\', \'product\', \'button.buy_now\', \'Buy now\'),
              (\'en\', \'payment_result\', \'completed.title\', \'Completed\'),
              (\'en\', \'payment_result\', \'completed.description\', \'Thank you! Your order has been successfully processed.\'),
              (\'en\', \'payment_result\', \'failed.title\', \'Failed\'),
              (\'en\', \'payment_result\', \'failed.description\', \'Your payment failed! Please try again later\'),
              (\'en\', \'payment_result\', \'awaiting.title\', \'Awaiting\'),
              (\'en\', \'payment_result\', \'awaiting.description\', \'Your payment awaiting!\'),
              (\'en\', \'footer\', \'terms\', \'Terms of Service\'),
              (\'en\', \'footer\', \'policy\', \'Privacy Policy\'),
              (\'en\', \'footer\', \'contact\', \'Contact Us\'),
              (\'en\', \'404\', \'title\', \'404\'),
              (\'en\', \'404\', \'description\', \'The requested page was not found on this server\'),
              (\'en\', \'404\', \'button.home_page\', \'Home page\'),
              (\'en\', \'checkout\', \'redirect.title\', \'Checkout\'),
              (\'en\', \'checkout\', \'redirect.redirect\', \'Redirecting...\'),
              (\'en\', \'checkout\', \'redirect.go\', \'Go\');
        ');

        $this->execute('
            USE `' . DB_STORES . '`;
            INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
            VALUES
              (null, \'ru\', \'cart\', \'title\', \'Корзина\'),
              (null, \'ru\', \'cart\', \'package\', \'Пакет\'),
              (null, \'ru\', \'cart\', \'details\', \'Детали\'),
              (null, \'ru\', \'cart\', \'price\', \'Цена\'),
              (null, \'ru\', \'cart\', \'remove\', \'Удалить\'),
              (null, \'ru\', \'cart\', \'total\', \'Всего\'),
              (null, \'ru\', \'cart\', \'total_price\', \'Общая стоимость\'),
              (null, \'ru\', \'cart\', \'email\', \'Email\'),
              (null, \'ru\', \'cart\', \'payment_method\', \'Метод оплаты\'),
              (null, \'ru\', \'cart\', \'button.continue\', \'Продолжить с покупками\'),
              (null, \'ru\', \'cart\', \'button.checkout\', \'Оплатить\'),
              (null, \'ru\', \'cart\', \'no_orders\', \'Нет товаров\'),
              (null, \'ru\', \'cart\', \'quantity\', \'Количество\'),
              (null, \'ru\', \'contact\', \'header\', \'Связаться с нами\'),
              (null, \'ru\', \'contact\', \'subject\', \'Тема\'),
              (null, \'ru\', \'contact\', \'name\', \'Имя\'),
              (null, \'ru\', \'contact\', \'email\', \'Email\'),
              (null, \'ru\', \'contact\', \'message\', \'Сообщение\'),
              (null, \'ru\', \'contact\', \'button.submit\', \'Отправить\'),
              (null, \'ru\', \'contact\', \'form.message.success\', \'Спасибо! Ваше сообщение отправлено\'),
              (null, \'ru\', \'contact\', \'form.message.error\', \'Ошибка! Попробуйте отправить сообщение позже\'),
              (null, \'ru\', \'order\', \'title\', \'Добавить в корзину\'),
              (null, \'ru\', \'order\', \'package\', \'Пакет\'),
              (null, \'ru\', \'order\', \'quantity\', \'Количество\'),
              (null, \'ru\', \'order\', \'price\', \'Цена\'),
              (null, \'ru\', \'order\', \'link\', \'Ссылка\'),
              (null, \'ru\', \'order\', \'button.back_url\', \'Вернуться\'),
              (null, \'ru\', \'order\', \'button.add_to_cart\', \'Добавить в корзину\'),
              (null, \'ru\', \'order\', \'details\', \'Дополнительные детали заказа\'),
              (null, \'ru\', \'order\', \'info\', \'Информация о заказе\'),
              (null, \'ru\', \'product\', \'button.buy_now\', \'Купить\'),
              (null, \'ru\', \'payment_result\', \'completed.title\', \'Выполнено\'),
              (null, \'ru\', \'payment_result\', \'completed.description\', \'Спасибо! Ваш платеж успешно обработан\'),
              (null, \'ru\', \'payment_result\', \'failed.title\', \'Ошибка\'),
              (null, \'ru\', \'payment_result\', \'failed.description\', \'Платеж не прошел! Пожалуйста, повторите попытку позже\'),
              (null, \'ru\', \'payment_result\', \'awaiting.title\', \'В ожидании\'),
              (null, \'ru\', \'payment_result\', \'awaiting.description\', \'Платеж принят и ожидает проверки\'),
              (null, \'ru\', \'footer\', \'terms\', \'Условия использования\'),
              (null, \'ru\', \'footer\', \'policy\', \'Политика конфиденциальности\'),
              (null, \'ru\', \'footer\', \'contact\', \'Контакты\'),
              (null, \'ru\', \'404\', \'title\', \'404\'),
              (null, \'ru\', \'404\', \'description\', \'Запрашиваемая страница не найдена\'),
              (null, \'ru\', \'404\', \'button.home_page\', \'Главная\'),
              (null, \'ru\', \'checkout\', \'redirect.title\', \'Ожидайте\'),
              (null, \'ru\', \'checkout\', \'redirect.redirect\', \'Перенаправление...\'),
              (null, \'ru\', \'checkout\', \'redirect.go\', \'Перейти\');
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            DELETE FROM `store_default_messages`;
        ');
    }
}
