<?php
namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\store\Blocks;
use common\models\stores\StoreAdminAuth;
use Yii;
use yii\base\Model;
use yii\web\User;

/**
 * Class EditBlockForm
 * @package app\modules\superadmin\models\forms
 */
class EditBlockForm extends Model {

    public $content;

    /**
     * @var Blocks
     */
    private $_block;

    private $_user;

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
     * @param Blocks $block
     */
    public function setBlock($block)
    {
        $this->_block = $block;
    }

    /**
     * Save block content
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_block->setContent($this->content);

        if (!$this->_block->save(false)) {
            $this->addErrors($this->_block->getErrors());
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_BLOCKS_BLOCK_UPDATED, $this->_block->id, $this->_block->code);

        return true;
    }
}