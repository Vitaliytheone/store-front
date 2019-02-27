<?php

return [
    'api_endpoints' => [
        /**
         * Получение данных о странице
         *
         * Тип запроса: GET
         * Обязательные параметры:
         *      id (int) — ID страницы.
         * Необязательные параметы:
         */
        'get_page' => '/admin/settings/pages/get-page',

        /**
         * Получение списка страниц
         *
         * Тип запроса: GET
         * Обязательные параметры:
         * Необязательные параметы:
         */
        'get_pages' => '/admin/settings/pages/get-pages',

        /**
         * Сохранение draft-версии страницы
         *
         * Тип запроса: POST
         * Обязательные параметры:
         * Необязательные параметы:
         *      {{id}} (int) – ID страницы. Обновление — если определен, создание — если не определен
         * Данные формы:
         *      JSON SERIALIZE DATA {}
         */
        'save_draft_page' => '/admin/settings/pages/draft/{{id}}',

        /**
         * Сохранение страницы
         *
         * Тип запроса: POST
         * Обязательные параметры:
         *      {{id}} (int) – ID страницы
         * Необязательные параметы:
         * Данные формы:
         *      JSON SERIALIZE DATA {}
         */
        'save_publish_page' => '/admin/settings/pages/publish/{{id}}',

        /**
         * Получение списка продуктов
         *
         * Тип запроса: GET
         * Обязательные параметры:
         * Необязательные параметы:
         */
        'get_products' => '/admin/settings/pages/get-products',

        /**
         * Получение данных о продукте и его пакатах
         *
         * Тип запроса: GET
         * Обязательные параметры:
         *      id (int) — ID продукта.
         * Необязательные параметы:
         */
        'get_product' => '/admin/settings/pages/get-product',

        /**
         * Сохранение продукта
         *
         * Тип запроса: POST
         * Обязательные параметры:
         *      {{id}} (int) – ID продукта
         * Необязательные параметы:
         * Данные формы:
         *      JSON SERIALIZE DATA {}
         */
        'save_product' => '/admin/settings/pages/set-product/{{id}}',

        /**
         * Сохранение пакета
         *
         * Тип запроса: POST
         * Обязательные параметры:
         *      {{id}} (int) – ID пакета
         * Необязательные параметы:
         * Данные формы:
         *      JSON SERIALIZE DATA {}
         */
        'save_package' => '/admin/settings/pages/set-package/{{id}}',

        /**
         * Загрузка картинки
         *
         * Тип запроса: POST
         * Обязательные параметры:
         * Необязательные параметы:
         * Данные формы:
         *      MULTIPART/FORM-DATA
         */
        'upload_image' => '/admin/settings/pages/set-image',

        /**
         * Удаление картинки
         *
         * Тип запроса: POST
         * Обязательные параметры:
         *  {{id}} (int) – ID картинки
         * Необязательные параметы:
         * Данные формы:
         *     NULL
         */
        'delete_image' => '/admin/settings/pages/unset-image/{{id}}',

        /**
         * Получение списка картинок
         *
         * Тип запроса: GET
         * Обязательные параметры:
         * Необязательные параметы:
         */
        'get_images' => '/admin/settings/pages/get-images',
    ],
];