<?php

namespace sommerce\components\validators\link;


class PeriscopeProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'periscope.tv/' . $this->link;
        }

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.periscope\.tv\/([a-z0-9\_]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid periscope profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid periscope profile link.');

            return false;
        }

        return true;
    }
}