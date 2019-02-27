<?php

namespace sommerce\modules\admin\models\forms;


use common\models\store\Pages;
use common\models\store\Products;
use sommerce\modules\admin\components\CustomUser;
use Yii;
use yii\base\Model;

/**
 * Class PageForm
 * @package sommerce\modules\admin\models\forms
 */
class PageForm extends Model
{
    public $name;
    public $title;
    public $description;
    public $keywords;
    public $url;
    public $visibility = 1;

    /**
     * @var CustomUser
     */
    protected $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['title', 'keywords', 'url', 'name'], 'string'],
            [['visibility'], 'integer'],
            [['url', 'title'], 'string', 'max' => 70],
            [['description'], 'string', 'max' => 160],
            ['url', 'match', 'pattern' => '/^[a-z0-9-_]+$/i'],
            ['url', 'unique', 'targetClass' => Products::class, 'targetAttribute' => ['url' => 'url']],
            ['url', 'unique', 'targetClass' => Pages::class, 'targetAttribute' => ['url' => 'url']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mame' => Yii::t('admin', 'pages.name'),
            'url' => Yii::t('admin', 'pages.url'),
            'title' => Yii::t('admin', 'pages.title'),
            'description' => Yii::t('admin', 'pages.description'),
            'keywords' => Yii::t('admin', 'pages.keywords'),
        ];
    }


    /**
     * @return bool|int
     */
    public function save() {
        return true;
    }


    /**
     * @return CustomUser
     */
    public function getUser(): CustomUser
    {
        return $this->user;
    }

    /**
     * @param CustomUser $user
     */
    public function setUser(CustomUser $user)
    {
        $this->user = $user;
    }
}