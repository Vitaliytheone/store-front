<?php

namespace frontend\components\validators\link;


class VimeoChannel extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'vimeo.com/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/vimeo\.com\/([a-z0-9]+)(\/)?$/uis", $this->link))) {
            $this->addError('Invalid vimeo channel link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid vimeo channel link.');

            return false;
        }

        return true;
    }
}