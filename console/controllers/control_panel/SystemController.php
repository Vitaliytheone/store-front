<?php

namespace console\controllers\control_panel;

use common\models\sommerces\StoreDomains;
use Yii;
use yii\helpers\Console;

/**
 * Class SystemController
 * @package console\controllers\my
 */
class SystemController extends CustomController
{
    public $start;

    public function options($actionID)
    {
        return ['start'];
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionChangeStoresDomains()
    {
        $oldDomains = '/sommerce.(team|net)/i';
        $newSommerceDomain = Yii::$app->params['sommerceDomain'];

        $sommerceDomain = StoreDomains::find()
            ->andWhere([
                'type' => StoreDomains::DOMAIN_TYPE_SOMMERCE,
            ])
            ->andFilterWhere([
                'OR',
                ['like', 'domain', 'sommerce.net'],
                ['like', 'domain', 'sommerce.team'],
            ])
            ->all();

        foreach ($sommerceDomain as $domain) {
            $newDomain = preg_replace($oldDomains, $newSommerceDomain, $domain->domain);

            $domain->domain = $newDomain;
            if ($domain->update(false)) {
                $this->stderr('Updated domain: ' . $domain->id . "\n", Console::FG_GREEN);
            } else {
                $this->stderr('Update error: ' . $domain->id . "\n", Console::FG_RED);
            }
        }
    }
}
