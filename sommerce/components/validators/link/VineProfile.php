<?php

namespace sommerce\components\validators\link;


class VineProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'vine.co/' . $this->link;
        }

        $this->link = "https://" . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/vine\.co\/([a-z0-9]+)(\/)?$/uis", $this->link))
            && !(preg_match("/https\:\/\/vine\.co\/u\/([0-9]+)(\/)?$/uis", $this->link))) {
            $this->addError('Invalid vine profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid vine profile link.');

            return false;
        }

        return true;
    }
}