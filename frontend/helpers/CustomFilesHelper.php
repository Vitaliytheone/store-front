<?php

namespace frontend\helpers;

use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use DirectoryIterator;
use yii\helpers\FileHelper;
use frontend\modules\admin\models\search\ThemesSearch;
use yii\base\Exception;

class CustomFilesHelper extends FileHelper
{
    /**
     * Return directory folders and files tree
     * @param string $dir
     * @param string $dir2 === dir
     * @param string $regex
     * @param bool $ignoreEmpty
     * @return array
     * @throws Exception
     */
    static function dirTree($dir, $dir2, $regex = '', $ignoreEmpty = false)
    {
        if (!file_exists($dir)) {
            throw new Exception($dir . ' Does not exist!');
        }

        if (is_file($dir)) {
            throw new Exception('It is file! Must be directory! ' . $dir);
        }

        $dirIterator = new DirectoryIterator((string)$dir);

        $dirs = array();
        $files = array();

        foreach ($dirIterator as $node) {

            $name= $node->getFilename();

            if ($node->isDir() && !$node->isDot()) {
                $tree = static::dirTree($node->getPathname(), $dir2, $regex, $ignoreEmpty);
                if (!$ignoreEmpty || count($tree)) {
                    $dirs[$name] = [
                        'path' => $node->getPath(),
                        'files' => $tree,
                    ];
                }
            } elseif ($node->isFile()) {
                if ('' == $regex || preg_match($regex, $name)) {
                    $files[$name] = [
                        'path_name' => $node->getPathname(),
                        'path' => $node->getPath(),
                        'extension' => $node->getExtension(),
                        'path_relative_name' => static::getFileRelativePath($dir2, $node->getPathname()),
                    ];
                }
            }
        }

        return array_merge($dirs, $files);
    }


    /**
     * Return file relative path to $currentPath
     * @param $fullPath
     * @param $currentPath
     * @return string
     */
    public static function getFileRelativePath($fullPath, $currentPath)
    {
        $relativePath = ltrim(str_replace($fullPath, '', $currentPath), '/');
        return $relativePath;
    }

}