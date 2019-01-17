<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\store\Packages;
use common\models\store\Pages;
use common\models\store\Products;
use sommerce\controllers\CommonController;
use sommerce\modules\admin\models\search\PagesOldSearch;
use Yii;
use yii\helpers\BaseHtml;
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
     * @param null $id
     * @return array
     */
    public function actionGetPage($id = null)
    {
        $data = null;

        if ($id) {
            $data = static::_getPage($id)->json_dev;
        }

        return ['json' => $data];
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
            ->active()
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
            }
            
            $products[$productId]['packages'][] = [
                'id' => $item['pk_id'],
				'name' => BaseHtml::encode($item['pk_name']),
				'price'  => $item['pk_price'],
				'quantity' => $item['pk_quantity'],
            ];
        }

        return array_values($products);
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
            throw new NotFoundHttpException('Message!');
        }

        return $page;
    }
}