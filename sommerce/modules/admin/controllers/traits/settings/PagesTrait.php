<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\components\exceptions\FirstValidationErrorHttpException;
use common\models\store\Packages;
use common\models\store\PageFiles;
use common\models\store\Pages;
use common\models\store\Products;
use sommerce\controllers\CommonController;
use sommerce\modules\admin\models\forms\SavePageDraftForm;
use sommerce\modules\admin\models\search\PagesOldSearch;
use Yii;
use yii\helpers\BaseHtml;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class PagesTrait
 * @property CommonController $this
 * @package sommerce\modules\admin\controllers
 */
trait PagesTrait {

    /**
     * Settings pages
     * @return string
     */
    public function actionPages()
    {
        $this->view->title = Yii::t('admin', "settings.pages_page_title");
        $this->addModule('adminPages');
        $search = new PagesOldSearch();
        $search->setStore($this->store);
        $pages = $search->searchPages();

        return $this->render('pages', [
            'pages' => $pages,
        ]);
    }

    /**
     * Initialize Pages ReactJs app
     * /admin/settings/edit-page
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionEditPage($id)
    {
        $this->view->title = Yii::t('admin', "settings.pages_edit_page");

        $this->layout = '@admin/views/layouts/react_app';

        return $this->render('edit_pages_app', [
            'appConfig' => [
                'api_endpoints' => Yii::$app->params['appConfigs']['page_editor']['api_endpoints'],
            ],
        ]);
    }

    /**
     * Return page data
     * @param integer $id
     * @return array
     */
    public function actionGetPage($id)
    {
        $page = static::_getPage($id);
        $pageFiles = PageFiles::find()
            ->select(['json_draft'])
            ->andWhere([
                'name' => [
                    PageFiles::NAME_STYLES,
                    PageFiles::NAME_HEADER,
                    PageFiles::NAME_FOOTER,
                ]
            ])
            ->asArray()
            ->indexBy('name')
            ->column();

        return [
            'styles' => $pageFiles[PageFiles::NAME_STYLES],
            'layouts' => [
                'header' => $pageFiles[PageFiles::NAME_HEADER],
                'footer' => $pageFiles[PageFiles::NAME_FOOTER],
            ],
            'json' => $page->json_draft,
        ];
    }

    /**
     * Return pages list
     * @return array
     */
    public function actionGetPages()
    {
        $pages = Pages::find()
            ->select([
                'id' => 'id',
                'title' => 'title',
                'url' => 'url',
            ])
            ->asArray()
            ->all();

        array_walk($pages, function (&$page){
            $page['id'] = (int)$page['id'];
            $page['title'] = BaseHtml::encode(trim($page['title']));
            $page['url'] = BaseHtml::encode(trim($page['url']));
        });

        return $pages;
    }

    /**
     * Return products with packages list
     * @return array
     */
    public function actionGetProducts()
    {
        $productPackages = Products::find()
            ->alias('pr')
            ->select([
                'pr_id' =>'pr.id', 'pr_name' => 'pr.name', 'pr_description' => 'pr.description', 'pr_properties' => 'pr.properties', 'pr_color' => 'pr.color',
                'pk_id' => 'pk.id', 'pk_name' => 'pk.name',  'pk_price' => 'pk.price', 'pk_quantity' => 'pk.quantity'
            ])
            ->leftJoin(['pk' => Packages::tableName()], 'pk.product_id = pr.id AND pk.visibility = :pk_visibility AND pk.deleted = :pk_deleted', [
                'pk_visibility' => Packages::VISIBILITY_YES,
                'pk_deleted' => Packages::DELETED_NO,
            ])
            ->andWhere(['pr.visibility' => Products::VISIBILITY_YES])
            ->orderBy(['pr.position' => SORT_DESC, 'pk.position' => SORT_DESC])
            ->asArray()
            ->all();

        $products = [];

        foreach ($productPackages as $item) {

            $productId = $item['pr_id'];
            
            if (empty($products[$productId])) {
                $products[$productId]['id'] = $productId;
                $products[$productId]['name'] = BaseHtml::encode($item['pr_name']);
                $products[$productId]['description'] = BaseHtml::encode($item['pr_description']);
                $products[$productId]['properties'] = BaseHtml::encode($item['pr_properties']);
                $products[$productId]['color'] = BaseHtml::encode($item['pr_color']);
                $products[$productId]['packages'] = [];
            }

            if ($item['pk_id']) {
                $products[$productId]['packages'][] = [
                    'id' => $item['pk_id'],
                    'name' => BaseHtml::encode($item['pk_name']),
                    'price'  => $item['pk_price'],
                    'quantity' => $item['pk_quantity'],
                ];
            }
        }

        return array_values($products);
    }

    /**
     * Save page
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionDraft($id = null)
    {
        $data = '{"key":"2Vdqu1eG0fKhpr86AZn184X0fkx31YXZ","styles":{"style":{"main":"\n    body{\n        font-size: 14px;\n    }\n    .body{\n        font-size: /*!setting.body_fontSize*/14px/*!*/;;\n        font-weight: /*!setting.body_fontWeight*/400/*!*/;;\n        font-family:  /*!setting.body_fontFamily*/30px/*!*/;\n    }\n    h2 {\n        font-size: /*!setting.header_fontSize*/32px/*!*/;;\n    }\n    a{\n        text-decoration: none;\n        color: #0a8cf0;\n    }\n    /* Slider */\n                    .slick-dots{\n                        margin: 0px;\n                        padding: 0px;\n                        text-align: center;\n                    }\n                    \n                    .slick-dots li{\n                        position: relative;\n                        display: inline-block;\n                        width: 20px;\n                        height: 20px;\n                        margin: 0 1px;\n                        padding: 0;\n                        cursor: pointer;\n                    }\n                    \n                    .slick-dots li button{\n                            font-size: 0;\n                            line-height: 0;\n                            display: block;\n                            width: 20px;\n                            height: 20px;\n                            padding: 5px;\n                            cursor: pointer;\n                            color: transparent;\n                            border: 0;\n                            outline: none;\n                            background: transparent;\n                    }\n                    \n                    .slick-dots li button:before{\n                            font-family: \'slick\';\n                            font-size: 50px;\n                            line-height: 20px;\n                            position: absolute;\n                            top: 0;\n                            left: 0;\n                            width: 20px;\n                            height: 20px;\n                            content: \'•\';\n                            text-align: center;\n                            opacity: .25;\n                            color: #000;\n                            -webkit-font-smoothing: antialiased;\n                            -moz-osx-font-smoothing: grayscale;\n                    }\n                    \n                    .slick-dots li:hover button:before{\n                           opacity: .5;\n                           color: rgba(0, 0, 0, 0.85);     \n                    }\n                    \n                    .slick-dots li.slick-active button:before {\n                        opacity: .75;\n                        color: rgba(0, 0, 0, 0.85);\n                    }\n        .slider__arrow-next{\n            right: 0;\n            position: absolute;\n            top: 40%;\n            z-index: 5;\n        }\n        .slider__arrow-next:before, .slider__arrow-prev:before{\n            font-family: \'Font Awesome 5 Free\';\n            font-weight: 900;\n            -moz-osx-font-smoothing: grayscale;\n            -webkit-font-smoothing: antialiased;\n            display: inline-block;\n            font-style: normal;\n            font-variant: normal;\n            text-rendering: auto;\n            line-height: 1;\n            font-size: 40px;\n            cursor: pointer;\n            color: rgba(0, 0, 0, 0.85);\n        }\n        .slider__arrow-next:before{\n            content: \"\\f105\";\n        }\n        .slider__arrow-prev{\n            left: 0;\n            position: absolute;\n            top: 40%;\n            z-index: 5;\n        }\n        .slider__arrow-prev:before{\n            content: \'\\f104\';\n        }\n","blocks":{"block_features":"/* Block Features */","b1f1_features__mowl":"/* B1F1 */\n                    .b1f1-features__mowl{\n                        position: relative;\n                        padding-top: /*!setting.b1f1_features_top__mowl*/30px/*!*/;\n                        padding-bottom: /*!setting.b1f1_features_bottom__mowl*/50px/*!*/;\n                        background-color: /*!setting.b1f1_features_bg_color__mowl*/#ffffff/*!*/;\n                        color: /*!setting.b1f1_features_color__mowl*/#27292b/*!*/;\n                    }\n                    \n                    .b1f1-features__mowl:before{\n                        content: \"\";\n                        position: absolute;\n                        opacity: /*!setting.b1f1_features_bg_opacity__mowl*/1/*!*/;\n                        top: 0;\n                        left: 0;\n                        width: 100%;\n                        height: 100%;\n                        background-image:  /*!setting.b1f1_features_bg_image__mowl*/none/*!*/;\n                        background-size: cover;\n                        background-position: center center;\n                        background-repeat: no-repeat;\n                    }\n\n                    .b1f1-features__mowl .features-header{\n                        position: relative;\n                        padding: 15px 0px;\n                        margin-bottom: 15px;\n                    }\n                    \n                    .b1f1-features__mowl .features-header__title{\n                        position: relative;\n                        margin: 10px 0px;\n                        text-align: center;\n                    }\n                    \n                    .b1f1-features__mowl .features-header__description{\n                        position: relative;\n                        margin: 10px 0px;\n                        text-align: center;\n                    }\n                    \n                    .b1f1-features__mowl .features-item{\n                        color: /*!setting.b1f1_features_items_color__mowl*/#27292b/*!*/;\n                        background-color: /*!setting.b1f1_features_items_bg__mowl*/#ffffff/*!*/;\n                        -webkit-border-radius: /*!setting.b1f1_features_items_radius__mowl*/18px/*!*/;\n                        -moz-border-radius: /*!setting.b1f1_features_items_radius__mowl*/18px/*!*/;\n                        border-radius: /*!setting.b1f1_features_items_radius__mowl*/18px/*!*/;\n                        padding: 30px;\n                        text-align: center;\n                        margin-top: 15px;\n                        margin-bottom: 15px;\n                        -webkit-box-shadow: /*!setting.b1f1_features_items_shadow__mowl*/none/*!*/;\n                        -moz-box-shadow: /*!setting.b1f1_features_items_shadow__mowl*/none/*!*/;\n                        box-shadow: /*!setting.b1f1_features_items_shadow__mowl*/none/*!*/;\n                    }\n                    \n                    .b1f1-features__mowl .features-item__icon{\n                        position: relative;\n                        font-size: /*!setting.b1f1_features_items_icon_size__mowl*/65px/*!*/;\n                    }\n                    \n                    .b1f1-features__mowl .features-item__title{\n                        margin: 10px 0px;\n                        font-size: 16px;\n                    }\n                    .b1f1-features__mowl .features-item__description{\n                        margin: 10px 0px;\n                    }"}},"styleData":{"main":{"body_fontFamily":"Georgia","body_fontWeight":"300","body_fontSize":"14px","body_lineHeightMode":"auto","body_lineHeight":"21px","header_fontFamily":"Georgia","header_fontSize":"32px"},"b1f1_features__mowl":{"b1f1_features_bg_color__mowl":"#ffffff","b1f1_features_bg_image__mowl":"none","b1f1_features_bg_opacity__mowl":"1","b1f1_features_color__mowl":"#27292b","b1f1_features_top__mowl":"30px","b1f1_features_bottom__mowl":"50px","b1f1_features_items_bg__mowl":"#ffffff","b1f1_features_items_icon_size__mowl":"65px","b1f1_features_items_color__mowl":"#27292b","b1f1_features_items_shadow__mowl":"none","b1f1_features_items_radius__mowl":"18px"}}},"layouts":{"header":false,"footer":false},"json":{"dom":{"tagName":"div","attrs":{"className":"body"},"children":[{"tagName":"div","attrs":{"className":"b1f1-features__mowl"},"mainClassName":"b1f1-features__mowl","children":[{"tagName":"div","attrs":{"className":"container"},"children":[{"tagName":"div","attrs":{"className":"row"},"children":[{"tagName":"div","attrs":{"className":"col-md-12"},"children":[{"tagName":"div","attrs":{"className":"features-header"},"children":[{"tagName":"div","attrs":{"className":"features-header__title"},"visibility":"b1f1_features_show_title__mowl","editor":true,"text":"<h2>Header block</h2>","blockCode":"b1f1_features__mowl","key":"0.0.0.0.0.0.0","depth":6},{"tagName":"div","attrs":{"className":"features-header__description"},"visibility":"b1f1_features_show_description__mowl","editor":true,"text":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium aut beatae commodi distinctio dolorum enim explicabo facilis iste labore laborum, minus molestiae, nihil odit praesentium quod voluptate voluptatibus! Similique, veniam?</p>","blockCode":"b1f1_features__mowl","key":"0.0.0.0.0.0.1","depth":6}],"blockCode":"b1f1_features__mowl","key":"0.0.0.0.0.0","depth":5}],"blockCode":"b1f1_features__mowl","key":"0.0.0.0.0","depth":4}],"blockCode":"b1f1_features__mowl","key":"0.0.0.0","depth":3},{"tagName":"div","attrs":{"className":"row"},"drag":true,"children":[{"tagName":"div","attrs":{"className":"","classNameCustom":"b1f1_features_column__mowl"},"focus":{"position":"center","options":{"drag":true,"copy":true,"delete":true}},"children":[{"tagName":"div","attrs":{"className":"features-item"},"children":[{"tagName":"div","attrs":{"className":"features-item__icon"},"children":[{"tagName":"div","attrs":{"className":"fab fa-apple"},"icons":true,"blockCode":"b1f1_features__mowl","key":"0.0.0.1.0.0.0.0","depth":7}],"blockCode":"b1f1_features__mowl","key":"0.0.0.1.0.0.0","depth":6},{"tagName":"div","attrs":{"className":"features-item__title"},"editor":true,"text":"<strong>Title</strong>","blockCode":"b1f1_features__mowl","key":"0.0.0.1.0.0.1","depth":6},{"tagName":"div","attrs":{"className":"features-item__description"},"editor":true,"text":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam aspernatur consectetur delectus dicta dolores est exercitationem facere impedit nulla pariatur porro quam quasi quisquam quo totam ullam, unde velit vero?</p>","blockCode":"b1f1_features__mowl","key":"0.0.0.1.0.0.2","depth":6}],"blockCode":"b1f1_features__mowl","key":"0.0.0.1.0.0","depth":5}],"blockCode":"b1f1_features__mowl","key":"0.0.0.1.0","depth":4}],"blockCode":"b1f1_features__mowl","key":"0.0.0.1","depth":3}],"blockCode":"b1f1_features__mowl","key":"0.0.0","depth":2}],"blockCode":"b1f1_features__mowl","key":"0.0","depth":1}],"key":"0","depth":0},"settingsBlock":{"b1f1_features__mowl":{"color":{"background":{"label":"Background","value":"b1f1_features_bg_color__mowl"},"text":{"label":"Text","value":"b1f1_features_color__mowl"}},"padding":{"top":{"label":null,"type":"rangeSlider","range":[0,300],"value":"b1f1_features_top__mowl"},"bottom":{"label":null,"type":"rangeSlider","range":[0,300],"value":"b1f1_features_bottom__mowl"}},"custom":[{"title":"Visibility","list":[{"label":"Show title","type":"switch","value":"b1f1_features_show_title__mowl"},{"label":"Show description","type":"switch","value":"b1f1_features_show_description__mowl"}]},{"title":"Items in row","list":[{"label":false,"type":"radioButton","typeData":"settings","options":[{"name":"1","value":"col-md-12"},{"name":"2","value":"col-md-6"},{"name":"3","value":"col-md-4"},{"name":"4","value":"col-md-3"}],"value":"b1f1_features_column__mowl"}]},{"title":"Items settings","list":[{"label":"Background","type":"colorpicker","value":"b1f1_features_items_bg__mowl"},{"label":"Text color","type":"colorpicker","value":"b1f1_features_items_color__mowl"},{"label":"Shadow","type":"shadow","value":"b1f1_features_items_shadow__mowl"},{"label":"Border-radius","type":"rangeSlider","range":[0,35],"value":"b1f1_features_items_radius__mowl"}]}]}},"settingsData":{"b1f1_features__mowl":{"b1f1_features_column__mowl":"col-md-3","b1f1_features_show_title__mowl":true,"b1f1_features_show_description__mowl":true}}}}';
        $data = json_decode($data,1);

        $form = new SavePageDraftForm();
        $form->setStore($this->store);

        if ($id) {
            $form->setPage(static::_getPage($id));
        }

        if (!$form->load($data) || !$form->save()) {
            if ($form->hasErrors()) {
                throw new FirstValidationErrorHttpException($form);
            } else {
                throw new BadRequestHttpException('Cannot save page!');
            }
        }

        return $form->getPage()->id;
    }

    /**
     * Save page
     * @param $id
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionPublish($id)
    {
        return $id;
    }

    /**
     * Return page by page id
     * @param $id
     * @return array|Pages|null
     * @throws NotFoundHttpException
     */
    private static function _getPage($id)
    {
        $page = Pages::find()->active()->where(['id' => $id])->one();

        if (!$page) {
            throw new NotFoundHttpException('Page not found!');
        }

        return $page;
    }
}