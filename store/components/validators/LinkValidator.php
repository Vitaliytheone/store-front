<?php

namespace store\components\validators;

use common\models\store\Packages;
use common\models\stores\LinkValidations;
use common\models\stores\Stores;
use store\helpers\LinkTypeHelper;
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

        /**
         * @var Packages $package
         * @var Stores $store
         */
        $package = $model->getPackage();
        $store = $model->getStore();

        if (!$package) {
            return false;
        }

        $linkOrig = $model->$attribute;
        $link = $this->clearLink($linkOrig);
        
        $validator = $this->getValidator($package->link_type);

        if (!$validator) {
            return true;
        }


        if (!$validator->run($link, $package->name)) {
            LinkValidations::add($linkOrig, $package->link_type, $store->id);

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
            $className = "\\store\\components\\validators\\link\\{$validatorName}";
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
            LinkTypeHelper::INSTAGRAM_PROFILE => 'InstagramProfile',
            LinkTypeHelper::INSTAGRAM_POST => 'InstagramPost',
            LinkTypeHelper::FACEBOOK_PAGE => 'FacebookPage',
            LinkTypeHelper::FACEBOOK_PROFILE => 'FacebookProfile',
            LinkTypeHelper::FACEBOOK_POST => 'FacebookPost',
            LinkTypeHelper::FACEBOOK_GROUP => 'FacebookGroup',
            LinkTypeHelper::FACEBOOK_EVENT => 'FacebookEvent',
            LinkTypeHelper::TWITTER_PROFILE => 'TwitterProfile',
            LinkTypeHelper::TWITTER_POST => 'TwitterPost',
            LinkTypeHelper::YOUTUBE_CHANNEL => 'YoutubeChannel',
            LinkTypeHelper::YOUTUBE_VIDEO => 'YoutubeVideo',
            LinkTypeHelper::VINE_PICTURE => 'VinePicture',
            LinkTypeHelper::VINE_PROFILE => 'VineProfile',
            LinkTypeHelper::PINTEREST_PROFILE => 'PinterestProfile',
            LinkTypeHelper::PINTEREST_BOARD => 'PinterestBoard',
            LinkTypeHelper::PINTEREST_POST => 'PinterestPost',
            LinkTypeHelper::SOUNDCLOUD_TRACK => 'SoundcloudTrack',
            LinkTypeHelper::SOUNDCLOUD_PROFILE => 'SoundcloudProfile',
            LinkTypeHelper::MIXCLOUD_TRACK => 'MixcloudTrack',
            LinkTypeHelper::MIXCLOUD_PROFILE => 'MixcloudProfile',
            LinkTypeHelper::PERISCOPE_PROFILE => 'PeriscopeProfile',
            LinkTypeHelper::PERISCOPE_VIDEO => 'PeriscopeVideo',
            LinkTypeHelper::LINKEDIN_PROFILE => 'LinkedinProfile',
            LinkTypeHelper::LINKEDIN_GROUP => 'LinkedinGroup',
            LinkTypeHelper::LINKEDIN_POST => 'LinkedinPost',
            LinkTypeHelper::RADIOJAVAN_VIDEO => 'RadiojavanVideo',
            LinkTypeHelper::RADIOJAVAN_TRACK => 'RadiojavanTrack',
            LinkTypeHelper::RADIOJAVAN_PODCAST => 'RadiojavanPodcast',
            LinkTypeHelper::RADIOJAVAN_PLAYLIST => 'RadiojavanPlaylist',
            LinkTypeHelper::SHAZAM_PROFILE => 'ShazamProfile',
            LinkTypeHelper::SHAZAM_TRACK => 'ShazamTrack',
            LinkTypeHelper::REVERBNATION_TRACK => 'ReverbnationTrack',
            LinkTypeHelper::REVERBNATION_VIDEO => 'ReverbnationVideo',
            LinkTypeHelper::REVERBNATION_PROFILE => 'ReverbnationProfile',
            LinkTypeHelper::TUMBLR_PROFILE => 'TumblrProfile',
            LinkTypeHelper::TUMBLR_POST => 'TumblrPost',
            LinkTypeHelper::VIMEO_CHANNEL => 'VimeoChannel',
            LinkTypeHelper::VIMEO_VIDEO => 'VimeoVideo',
            LinkTypeHelper::FYUSE_PROFILE => 'FyuseProfile',
            LinkTypeHelper::FYUSE_PICTURE => 'FyusePicture',
            LinkTypeHelper::GOOGLE_PROFILE => 'GoogleProfile',
            LinkTypeHelper::GOOGLE_POST => 'GooglePost',
            LinkTypeHelper::TWITCH_CHANNEL => 'TwitchChannel'
        ];
    }
}