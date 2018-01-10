<?php
namespace frontend\helpers;

use common\models\stores\Stores;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AssetsHelper
 * @package frontend\helpers
 */
class AssetsHelper {

    /**
     * Get unique file url
     * @param string $path
     * @param string $dir
     * @return string
     */
    public static function getFileUrl($path, $dir = "@frontend/web")
    {
        $filePath = !empty($dir) ? Yii::getAlias($dir . $path) : $path;

        if (file_exists($filePath)) {
            $timestamp = @filemtime($filePath);

            if ($timestamp > 0) {
                $path .= '?v=' . $timestamp;
            }
        }

        return $path;
    }

    /**
     * Get libs scripts
     * @return array
     */
    public static function getLibScripts()
    {
        $scripts = [
            [
                'src' => 'https://www.google.com/recaptcha/api.js?hl=en',
            ],
            [
                'src' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js',
            ],
            [
                'src' => 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js',
                'attributes' => 'integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"',
            ],
            [
                'src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js',
                'attributes' => 'integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"',
            ],
            [
                'src' => 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.min.js',
            ],
        ];

        return $scripts;
    }

    /**
     * Get libs styles
     * @return array
     */
    public static function getLibStyles()
    {
        $scripts = [
            [
                'href' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
                'attributes' => 'integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"',
            ],
            [
                'href' => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
                'attributes' => 'integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"',
            ],
            [
                'href' => 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/css/swiper.min.css',
            ],
        ];

        return $scripts;
    }

    /**
     * Get panel assets files list
     * @param Stores $store
     * @return array
     */
    public static function getPanelAssets(Stores $store) {

        $folderContent = $store->getFolderContentData();

        $folder = $store->getFolder();

        $styles = static::getLibStyles();

        $scripts = array_merge(static::getLibScripts(), [
            [
                'src' => static::getFileUrl('/js/main.js')
            ],
        ]);


        foreach (ArrayHelper::getValue($folderContent, 'css', []) as $filename) {
            $styles[] = [
                'href' => '/assets/' . $folder . '/' . $filename
            ];
        }

        foreach (ArrayHelper::getValue($folderContent, 'js', []) as $filename) {
            $scripts[] = [
                'src' => '/assets/' . $folder . '/' . $filename
            ];
        }

        return [
            'scripts' => $scripts,
            'styles' => $styles,
        ];
    }
}