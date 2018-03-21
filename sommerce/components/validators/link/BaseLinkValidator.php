<?php
namespace sommerce\components\validators\link;

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
     * Validate method
     * @return mixed
     */
    abstract protected function validate();

    /**
     * Run validation
     * @param string $link
     * @return bool
     */
    public function run($link)
    {
        $this->errors = [];
        $this->link = $link;

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
     * @param $link
     * @return string|null
     */
    protected function checkUrl($link)
    {
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-us,en;q=0.5',
            'Accept-Encoding: gzip,deflate',
            'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Keep-Alive: 115',
            'Connection: keep-alive',
        ]);

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
        $result = curl_exec($ch);

        // System errors
        if (curl_errno($ch) != 0 && empty($result)) {
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        return $result;
    }
}