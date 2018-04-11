<?php
namespace tests\models;

use common\models\store\Packages;
use common\models\stores\Stores;
use sommerce\models\forms\AddToCartForm;
use Yii;
use Codeception\Test\Unit;
use yii\helpers\ArrayHelper;

/**
 * Class LinkTypeTest
 * @package tests\models
 */
class LinkTypeTest extends Unit
{
    /**
     * Registration test
     */
    public function testLinkTypeValidation()
    {
        Yii::$app->store->setInstance(Stores::find()->one());

        $linkByTypes = require_once(Yii::getAlias('@sommerce/tests/_data/link_types.php'));

        $model = new AddToCartForm();

        foreach ($linkByTypes as $linkType => $links) {
            $valid = ArrayHelper::getValue($links, 'valid');
            $invalid = ArrayHelper::getValue($links, 'invalid');

            $model->setPackage(new Packages([
                'link_type' => $linkType
            ]));

            foreach ($valid as $link) {

                $model->link = $link;

                $result = $model->validate();

                $this->assertTrue($result, 'Failed link ' . $link . ' validation in ' . $linkType . ' link type');
            }

            foreach ($invalid as $link) {

                $model->link = $link;

                $result = $model->validate();

                $this->assertFalse($result);
            }
        }
    }
}
