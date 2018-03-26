<?php

namespace sommerce\components\validators;

use common\models\store\Packages;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * Class LinkValidator
 * @package app\components\validators
 */
class LinkValidator extends Validator
{
    /**
     * @var integer
     */
    protected $package_id;

    /**
     * @var array
     */
    private static $_instances = [];

    /**
     * Validate attribute
     * @param \yii\base\Model $model
     * @param string $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        if (empty($model->$attribute)) {
            return false;
        }
        $package = $model->getPackage();

        if (!$package) {
            return false;
        }

        $link = $model->$attribute;
        $link = $this->clearLink($link);
        
        $validator = $this->getValidator($package->link_type);

        if (!$validator) {
            return true;
        }

        if (!$validator->run($link)) {
            $model->addError($attribute, $validator->getError());

            return false;
        }

        $model->$attribute = $validator->getLink();

        return true;
    }

    /**
     * Clear link
     * @param $link
     * @return string
     */
    public function clearLink($link)
    {
        $link = trim($link);
        $link = preg_replace(["/((http|https)\:\/\/)?(www\.|ww2\.)?/uis"], [""], $link);

        return $link;
    }

    /**
     * Get link validator by link type
     * @param $linkType
     * @return bool|mixed
     */
    public function getValidator($linkType)
    {
        $linkValidators = $this->getValidatorNames();

        $validatorName = ArrayHelper::getValue($linkValidators, $linkType);

        if (!$validatorName) {
            return null;
        }

        if (!isset(self::$_instances[$validatorName])) {
            $className = "\\sommerce\\components\\validators\\link\\{$validatorName}";
            self::$_instances[$validatorName] = new $className;
        }

        return self::$_instances[$validatorName];
    }

    /**
     * Get validators by link type
     * @return array
     */
    public function getValidatorNames()
    {
        return [
            1 => 'InstagramProfile',
            2 => 'InstagramPost',
            3 => 'FacebookPage',
            4 => 'FacebookProfile',
            5 => 'FacebookPost',
            6 => 'FacebookGroup',
            7 => 'FacebookEvent',
            8 => 'TwitterProfile',
            9 => 'TwitterPost',
            10 => 'YoutubeChannel',
            11 => 'YoutubeVideo',
            12 => 'VinePicture',
            13 => 'VineProfile',
            14 => 'PinterestProfile',
            15 => 'PinterestBoard',
            16 => 'PinterestPost',
            17 => 'SoundcloudTrack',
            18 => 'SoundcloudProfile',
            19 => 'MixcloudTrack',
            20 => 'MixcloudProfile',
            21 => 'PeriscopeProfile',
            22 => 'PeriscopeVideo',
            25 => 'LinkedinProfile',
            26 => 'LinkedinGroup',
            27 => 'LinkedinPost',
            28 => 'RadiojavanVideo',
            29 => 'RadiojavanTrack',
            30 => 'RadiojavanPodcast',
            31 => 'RadiojavanPlaylist',
            32 => 'ShazamProfile',
            33 => 'ShazamTrack',
            34 => 'ReverbnationTrack',
            35 => 'ReverbnationVideo',
            36 => 'ReverbnationProfile',
            37 => 'TumblrProfile',
            38 => 'TumblrPost',
            39 => 'VimeoChannel',
            40 => 'VimeoVideo',
            41 => 'FyuseProfile',
            42 => 'FyusePicture',
            43 => 'GoogleProfile',
            44 => 'GooglePost',
            45 => 'TwitchChannel'
        ];
    }
}