<?php

namespace common\components\cdn;

abstract class BaseCdn
{
    const MIME_JPEG = 'image/jpeg';
    const MIME_PNG  = 'image/png';
    const MIME_GIF  = 'image/gif';
    const MIME_ICO  = 'image/x-icon';
    const MIME_SVG  = 'image/svg+xml';

    const MESSAGE_BAD_CONFIG        = 'Bad CDN-api config!';
    const MESSAGE_FILE_NOT_FOUND    = 'Uploaded file does not found!';

    /**
     * Upload file to CDN
     * @param $filePath
     * @param $mime string  Uploaded file mime-type
     * @return string|integer CDN object ID
     */
    abstract public function uploadFromPath($filePath, $mime = null);

    /**
     * Get CDN object ID by CDN file `url`
     * @param $cdnUrl string CDN object `url`
     * @return string|integer
     */
    abstract public function getId($cdnUrl);

    /**
     * Get CDN object url by CDN object `id`
     * @param $cdnId string|bool CDN object `id`
     * @return string|bool
     */
    abstract public function getUrl($cdnId);

    /**
     * Delete CDN object by CDN object `id`
     * @param $cdnId string|integer CDN object id
     * @return mixed
     */
    abstract public function delete($cdnId);

    /**
     * Delete CDN object by CDN object `url`
     * @param $cdnUrl
     * @return mixed
     */
    public function deleteByUrl($cdnUrl)
    {
        $cdnId = $this->getId($cdnUrl);

        return $this->delete($cdnId);
    }
}