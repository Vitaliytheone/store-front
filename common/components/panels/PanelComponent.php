<?php
namespace common\components\panels;

use common\models\panels\PanelDomains;
use common\models\panels\Project;
use Yii;
use yii\base\Component;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class PanelComponent
 * @package common\components\stores
 */
class PanelComponent extends Component
{
    /**
     * @var Project|null|boolean
     */
    private static $_instance = false;

    /**
     * @var string - current panel domain
     */
    public $domain;

    /**
     * @var array
     */
    public $customFieldsByRoute = [];

    /**
     * @var array
     */
    public $customParamsByRoute = [];

    /**
     * @var array|string|null
     */
    protected $_queryFields;

    /**
     * @var array
     */
    protected $_languages;

    public function init()
    {
        $this->filter();
        $this->getInstance();
        parent::init();
    }

    /**
     * Get current store model
     * @return Project
     */
    public function getInstance()
    {
        if (false === static::$_instance) {
            static::$_instance = null;
            $attributes = [];

            $domain = $this->getDomain();

            if (!empty(Yii::$app->params['panelId'])) {
                $attributes = [
                    'panel_id' => Yii::$app->params['panelId']
                ];
            } else if ($domain) {
                $attributes['domain'] = (string)$domain;
            }

            if (!empty($attributes)) {
                $project = $this->findPanelByDomain($attributes);

                if ($project) {
                    $this->setInstance($project);
                }
            }
        }

        return static::$_instance;
    }

    /**
     * Get domain
     * @return null|string
     */
    public function getDomain()
    {
        $domain = null;
        if ($this->domain) {
            $domain = $this->domain;
            $domain = preg_replace('/^www\./i', '', $domain);
        } else if (php_sapi_name() != "cli") {
            $domain = $_SERVER['HTTP_HOST'];
            $domain = preg_replace('/^www\./i', '', $domain);
        }

        return $domain;
    }

    /**
     * Set instance
     * @param Project $project
     */
    public function setInstance($project)
    {
        static::$_instance = $project;
        $this->initDb();
    }

    /**
     * @param array|string $fields
     */
    public function setQueryFields($fields)
    {
        static::$_instance = false;
        $this->_queryFields = $fields;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setParam($name, $value)
    {
        if (!property_exists($this, $name)) {
            return;
        }
        $this->$name = $value;
    }

    /**
     * Filter query with custom actions
     */
    public function filter()
    {
        $url = null;

        if (!empty($_SERVER["REQUEST_URI"])) {
            $url = rtrim((string)(((array)explode( '?', $_SERVER["REQUEST_URI"]))[0]), '/');
        }

        if (!empty($this->customFieldsByRoute[$url])) {
            $this->setQueryFields($this->customFieldsByRoute[$url]);
        }

        if (!empty($this->customParamsByRoute[$url])) {
            foreach ($this->customParamsByRoute[$url] as $name) {
                $value = Yii::$app->request->post($name, Yii::$app->request->get($name));
                if (!empty($value) && is_string($value)) {
                    $this->setParam($name, $value);
                }
            }
        }
    }


    /**
     * Init db
     */
    public function initDb()
    {
        Yii::$app->panelDb->close();
        Yii::$app->panelDb->dsn = 'mysql:host=' . DB_CONFIG[0]['host'] . ';dbname=' . ArrayHelper::getValue($this->getInstance(), 'db');
    }

    /**
     * Get panel id
     * @return mixed
     */
    public function getId()
    {
        return ArrayHelper::getValue($this->getInstance(), 'id');
    }

    /**
     * Find panel by domain
     * @param array $attributes
     * @return null|Project
     */
    private function findPanelByDomain($attributes)
    {
        $domain = ArrayHelper::getValue($attributes, 'domain');
        $panelId = ArrayHelper::getValue($attributes, 'panel_id');

        $query = null;

        if ($panelId) {
            $query = Project::find()
                ->andWhere([
                    'id' => $panelId
                ]);
        } else if (!empty($domain)) {
            $query = Project::find()
                ->select('project.*')
                ->leftJoin(PanelDomains::tableName(), 'panel_domains.panel_id = project.id')
                ->andWhere([
                    'panel_domains.domain' => $domain
                ]);
        }

        if (empty($query)) {
            return null;
        }

        if ($this->_queryFields) {
            $query->select($this->_queryFields);
        }

        $panel = $query ? $query->limit(1)->one() : null;

        if (empty($panel)) {
            return null;
        }

        if (!in_array($panel->act, [
            Project::STATUS_ACTIVE,
            Project::STATUS_FROZEN
        ]) || empty($panel->db)) {
            return null;
        }

        return $panel;
    }
}