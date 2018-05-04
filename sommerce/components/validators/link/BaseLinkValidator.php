<?php
namespace sommerce\components\validators\link;

use yii\helpers\ArrayHelper;

/**
 * Class BaseLinkValidator
 * @package sommerce\components\validators\link
 */
abstract class BaseLinkValidator {

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $name;

    /**
     * Validate method
     * @return mixed
     */
    abstract protected function validate();

    /**
     * Run validation
     * @param string $link
     * @param string $name
     * @return bool
     */
    public function run($link, $name)
    {
        $this->errors = [];
        $this->link = $link;
        $this->name = $name;

        return $this->validate();
    }

    /**
     * Add error validator
     * @param $error
     */
    protected function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Get errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get error
     * @return array
     */
    public function getError()
    {
        return !empty($this->errors[0]) ? $this->errors[0] : null;
    }

    /**
     * Get link
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Check link
     * @param string $link
     * @param array $options
     * @return string|null
     */
    protected function checkUrl($link, $options = [])
    {
        $proxy = null;

        $headers = [
            'User-Agent' => 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
            'Accept' => 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'Accept-Language: en-us,en;q=0.5',
            'Accept-Encoding' => 'Accept-Encoding: gzip,deflate',
            'Accept-Charset' => 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Keep-Alive' => 'Keep-Alive: 115',
            'Connection' => 'Connection: keep-alive',
        ];

        $headers = ArrayHelper::merge($headers, ArrayHelper::getValue($options, 'headers', []));

        if (!empty(PROXY_CONFIG['link_type']['ip']) && !empty(PROXY_CONFIG['link_type']['port'])) {
            $proxy = PROXY_CONFIG['link_type']['ip'] . ':' . PROXY_CONFIG['link_type']['port'];
        }

        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);

        if (isset($options['ssl']) && $options['ssl']) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if (!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array_values($headers));

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
        $result = curl_exec($ch);

        // System errors
        if (curl_errno($ch) != 0 && empty($result)) {
            $error = curl_error($ch);
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        return $result;
    }
}