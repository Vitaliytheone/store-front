<?php

namespace common\models\panels;

use Yii;
use yii\base\ExitException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use \common\models\panels\queries\LetsencryptSslQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%letsencrypt_ssl}}".
 *
 * @property int $id
 * @property string $domain
 * @property string $file_contents
 * @property int $expired_at
 * @property int $updated_at
 * @property int $created_at
 */
class LetsencryptSsl extends ActiveRecord
{
    const SSL_FILE_CA = 'ca.cer';
    const SSL_FILE_FULLCHAIN = 'fullchain.cer';
    const SSL_FILE_CSR = '{domain}.csr';
    const SSL_FILE_CER = '{domain}.cer';
    const SSL_FILE_KEY = '{domain}.key';

    const SSL_FILE_CSR_CONF = '{domain}.csr.conf';
    const SSL_FILE_DOMAIN_CONF = '{domain}.conf';

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.letsencrypt_ssl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_contents'], 'string'],
            [['expired_at', 'updated_at', 'created_at'], 'integer'],
            [['domain'], 'string', 'max' => 300],
            [['domain'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'domain' => Yii::t('app', 'Domain'),
            'file_contents' => Yii::t('app', 'File Contents'),
            'expired_at' => Yii::t('app', 'Expired At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return LetsencryptSslQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LetsencryptSslQuery(get_called_class());
    }

    /**
     * Return SSL file names list
     * @param $domain
     * @return array
     */
    public static function sslFileNames($domain)
    {
        $files =  [
            self::SSL_FILE_CA,
            self::SSL_FILE_FULLCHAIN,
            self::SSL_FILE_CSR,
            self::SSL_FILE_CER,
            self::SSL_FILE_KEY,
            self::SSL_FILE_CSR_CONF,
            self::SSL_FILE_DOMAIN_CONF,
        ];

        $domainPlaceholder = '{domain}';

        foreach ($files as &$file) {

            if (strpos($file, $domainPlaceholder) === false) {
                continue;
            }

            $file = str_replace($domainPlaceholder, trim($domain), $file);
        }

        return $files;
    }

    /**
     * Get SSL files content
     * @return mixed
     */
    public function getFileContents()
    {
        return json_decode($this->file_contents, true);
    }

    /**
     * Set SSL files content
     * @param array $fileContents
     */
    public function setFileContents(array $fileContents)
    {
        $this->file_contents = json_encode($fileContents);
    }

    /**
     * Prepare filename for class constant uses
     * @param string $fileName
     */
    private function _prepareFilename(string &$fileName)
    {
        $sslFiles = static::sslFileNames($this->domain);

        $index = array_search($fileName, $sslFiles);

        if ($index !== false) {
            $fileName = $sslFiles[$index];
        }
    }

    /**
     * Get SSL file content
     * @param $fileName string
     * @return null|string
     */
    public function getFileContent(string $fileName)
    {
        $this->_prepareFilename($fileName);

        $files = $this->getFileContents();
        $files = is_array($files) ? $files : [];

        return ArrayHelper::getValue($files, $fileName, null);
    }

    /**
     * Set SSL file content
     * @param $fileName string file name
     * @param $fileContent string
     */
    public function setFileContent(string $fileName, string $fileContent)
    {
        $this->_prepareFilename($fileName);

        $files = $this->getFileContents();
        $files = is_array($files) ? $files : [];

        $this->setFileContents(array_merge($files, [$fileName => $fileContent]));
    }
}
