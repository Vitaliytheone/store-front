<?php

namespace sommerce\modules\admin\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;

/**
 * Class IntegrationSettingsForm
 * @package sommerce\modules\admin\widgets
 */
class IntegrationSettingsForm extends Widget
{
    /** @var array */
    public $settingsForm;

    /** @var array */
    public $options = [];

    private const TYPE_TEXTAREA = 'textarea';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function run()
    {
        if (!is_array($this->settingsForm) || empty($this->settingsForm)) {
            return '';
        }

        return $this->getForm();
    }

    /**
     * Get form
     * @return string
     */
    private function getForm(): string
    {
        $form = '';
        foreach ($this->settingsForm as $key => $value) {
            switch ($value['type']) {
                case static::TYPE_TEXTAREA:
                    $label = Html::label(Yii::t('admin', $value['label']), 'settings-custom__header');

                    $options = $this->options[$key] ?? '';
                    $input = Html::textarea('options[' . $value['name'] . ']', $options, [
                        'class' => 'form-control m-input',
                        'id' => 'settings-custom__header',
                        'rows' => '14',
                    ]);

                    $form .= Html::tag('div', $label . $input, [
                        'class' => 'form-group m-form__group',
                    ]);
                    break;
            }
        }

        return $form;
    }
}
