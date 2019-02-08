<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\components\exceptions\FirstValidationErrorHttpException;
use common\models\store\Packages;
use common\models\store\PageFiles;
use common\models\store\Pages;
use common\models\store\Products;
use sommerce\controllers\CommonController;
use sommerce\modules\admin\models\forms\SavePageForm;
use sommerce\modules\admin\models\search\PagesOldSearch;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
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
            'styles' => json_decode(ArrayHelper::getValue($pageFiles, PageFiles::NAME_STYLES), true),
            'layouts' => [
                'header' => json_decode(ArrayHelper::getValue($pageFiles, PageFiles::NAME_HEADER), true),
                'footer' => json_decode(ArrayHelper::getValue($pageFiles, PageFiles::NAME_FOOTER), true),
            ],
            'json' => $page->getJsonDraft(),
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
        $form = new SavePageForm();
        $form->setStore($this->store);
        $form->setIsDraft(true);

        if ($id) {
            $form->setPage(static::_getPage($id));
        }

        if (!$form->load(Yii::$app->request->post()) || !$form->save()) {
            if ($form->hasErrors()) {
                throw new FirstValidationErrorHttpException($form);
            } else {
                throw new BadRequestHttpException('Cannot save page!');
            }
        }

        return ['id' => $form->getPage()->id];
    }

    /**
     * Save page
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionPublish($id)
    {
        $form = new SavePageForm();
        $form->setStore($this->store);
        $form->setIsDraft(false);

        if ($id) {
            $form->setPage(static::_getPage($id));
        }

        if (!$form->load(Yii::$app->request->post()) || !$form->save()) {
            if ($form->hasErrors()) {
                throw new FirstValidationErrorHttpException($form);
            } else {
                throw new BadRequestHttpException('Cannot save page!');
            }
        }

        return ['id' => $form->getPage()->id];
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