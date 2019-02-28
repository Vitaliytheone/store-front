<?php

namespace control_panel\controllers;

use Yii;
use yii\web\Controller;

/**
 * Class WebhookController
 * @package control_panel\controllers
 */
class WebhookController extends Controller
{
	public $enableDomainValidation = false;

    /**
     * @param $action
     */
    public function actionIndex($action)
    {
        @file_put_contents(Yii::getAlias('@runtime') . '/webhook/' . (string)$action, json_encode([
            'GET' => $_GET,
            'POST' => $_POST,
            'SERVER' => $_SERVER,
        ], JSON_PRETTY_PRINT). "\n", FILE_APPEND);
    }
}