<?php
namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\store\Blocks;
use common\models\stores\StoreAdminAuth;
use sommerce\helpers\BlockHelper;
use Yii;
use yii\base\Model;
use yii\web\User;

/**
 * Class UpdateBlocksForm
 * @package sommerce\modules\admin\models\forms
 */
class UpdateBlocksForm extends Model {

    /**
     * @var
     */
    private $_user;

    /**
     * Array of blocks settings
     * @var array
     */
    private $_blocks = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['content'], 'safe']
        ];
    }

    /**
     * Set current user
     * @param User $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * Get current user
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Set block
     * @param array $blocks
     */
    public function setBlocks($blocks)
    {
        $this->_blocks = $blocks;
    }

    /**
     * Get blocks
     * @return array
     */
    public function getBlocks()
    {
        return $this->_blocks;
    }

    /**
     * Save block content
     * @return bool
     */
    public function save()
    {
        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        $blocksContent = $this->getBlocks();

        foreach ($blocksContent as $blockCode => $blockContent) {

            if (!in_array($blockCode, Blocks::getCodes())) {
                continue;
            }

            $blockModel = BlockHelper::getBlock($blockCode, true);

            if (!$blockModel) {
                return false;
            }

            $blockModel->setContent($blockContent);

            if (!$blockModel->save(false)) {
                return false;
            }

            ActivityLog::log($identity, ActivityLog::E_SETTINGS_BLOCKS_BLOCK_UPDATED, $blockModel->id, $blockModel->code);
        }

        return true;
    }
}