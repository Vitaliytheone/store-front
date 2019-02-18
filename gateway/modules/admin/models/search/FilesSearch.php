<?php

namespace admin\models\search;

use common\models\gateway\Files;
use gateway\helpers\FilesHelper;
use Yii;
use yii\db\Query;

/**
 * Class FilesSearch
 * @package admin\models\searches
 */
class FilesSearch extends BaseSearch
{
    protected $_paymentMethods;

    /**
     * @return array
     */
    public function search()
    {
        $files = [
            Files::FILE_TYPE_LAYOUT => [],
            Files::FILE_TYPE_PAGE => [],
            Files::FILE_TYPE_SNIPPET => [],
            Files::FILE_TYPE_CSS => [],
            Files::FILE_TYPE_JS => [],
            Files::FILE_TYPE_IMAGE => [],
        ];

        foreach (FilesHelper::getFiles() as $type => $items) {
            $files[$type] = $items;
        }

        return $files;
    }
}