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
        'get-page' => '/admin/settings/pages/get-page',

        /**
         * Получение списка страниц
         *
         * Тип запроса: GET
         * Обязательные параметры:
         * Необязательные параметы:
         */
        'get-pages' => '/admin/settings/pages/get-pages',

        /**
         * Получение списка продуктов
         *
         * Тип запроса: GET
         * Обязательные параметры:
         * Необязательные параметы:
         */
        'get-products' => '/admin/settings/pages/get-products',

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
        'save-draft-page' => '/admin/settings/pages/draft/{{id}}',

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
        'save-publish-page' => '/admin/settings/pages/publish/{{id}}',
    ],
];