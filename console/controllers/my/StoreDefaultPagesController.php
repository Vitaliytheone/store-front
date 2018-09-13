<?php

namespace console\controllers\my;


use yii\db\ActiveRecord;
use yii\db\Query;
use Yii;

class StoreDefaultPagesController extends CustomController
{
    /**
     * Set default pages
     */
    public function actionSetDefault()
    {
        $stores = (new Query())
            ->select('db_name')
            ->from(DB_STORES . '.stores')
            ->where('db_name IS NOT NULL')
            ->andWhere(['!=', 'db_name', ''])
            ->all();

        $template = $this->getTemplate();
        $titles = [];
        for ($i = 0; $i < count($template); $i++) {
            $titles[] = $template[$i]['title'];
        }

        foreach ($stores as $store) {
            for ($i = 0; $i < count($template); $i++) {
                $pageCheck = (new Query())
                    ->select('title')
                    ->from($store['db_name'] . '.pages')
                    ->where(['title' => $template[$i]['title']])
                    ->exists();

                if (!$pageCheck) {
                    Yii::$app->db->createCommand()
                        ->insert($store['db_name'].'.pages', [
                            'title' => $template[$i]['title'],
                            'visibility' => $template[$i]['visibility'],
                            'content' => $template[$i]['content'],
                            'seo_title' => $template[$i]['seo_title'],
                            'url' => $template[$i]['url'],
                            'template' => $template[$i]['template'],
                            'is_default' => $template[$i]['is_default'],
                        ])
                        ->execute();
                }
            }

            $notDefault = (new Query())
                ->select('*')
                ->from($store['db_name'] . '.pages')
                ->where(['title' => $titles])
                ->andWhere(['!=', 'is_default', 1])
                ->all();

            for ($i = 0; $i < count($notDefault); $i++) {
                Yii::$app->db->createCommand()
                    ->update($store['db_name'] . '.pages', ['is_default' => 1])
                    ->execute();
            }
        }
    }

    /**
     * Get pages template
     * @return array
     */
    private function getTemplate(): array
    {
        $template =  (new Query())
            ->select('*')
            ->from('store_template.pages')
            ->all();

        $return = [];
        foreach ($template as $page) {
            $return[] = $page;
        }

        return $return;
    }
}