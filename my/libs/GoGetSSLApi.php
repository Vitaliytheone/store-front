<?php
namespace my\libs;

use Yii;

require_once(Yii::getAlias('@libs/GoGetSSL/GoGetSSLApi.php'));

use GoGetSSLApi as BaseGoGetSSLApi;

class GoGetSSLApi extends BaseGoGetSSLApi {
    /**
     * @var string - last url to api query
     */
    protected $lastUrl;

    /**
     * @var string - last sent data to api
     */
    protected $lastData;

    /**
     * @var string - result from api query
     */
    protected $lastResult;

    protected function call($uri, $getData = array(), $postData = array(), $forcePost = false, $isFile = false)
    {
        $this->lastUrl  = $this->URL . $uri;

        if(!empty($getData)) {
            foreach($getData as $key => $value) {
                $this->lastUrl .= (strpos($this->lastUrl, '?') !== false ? '&' : '?')
                    . urlencode($key) . '=' . rawurlencode($value);
            }
        }

        $post = (!empty($postData) || $forcePost);
        $c = curl_init($this->lastUrl);
        if($post) {
            curl_setopt($c, CURLOPT_POST, true);
        }

        if(!empty($postData)) {
            $this->lastData = $isFile ? $postData : http_build_query($postData);
            curl_setopt($c, CURLOPT_POSTFIELDS, $this->lastData);
        }

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        $this->lastResult = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        $this->lastStatus   = $status;
        $this->lastResponse = json_decode($this->lastResult, true);
        return $this->lastResponse;
    }

    public function getLastUrl()
    {
        return $this->lastUrl;
    }

    public function getLastData()
    {
        return $this->lastData;
    }

    public function getLastResult()
    {
        return $this->lastResult;
    }
}