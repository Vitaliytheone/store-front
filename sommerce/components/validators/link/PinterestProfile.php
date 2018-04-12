<?php

namespace sommerce\components\validators\link;


class PinterestProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'pinterest.com/' . $this->link;
        }

        $this->link = parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $domainFirst = [
            'www\.',
            'in\.',
            'pl\.',
            'ru\.',
            'tr\.',
            'it\.',
            'nl\.',
            'fi\.',
            'ro\.',
            'au\.',
            'fr\.',
            'es\.',
            'za\.',
            'id\.',
        ];

        $domainFirst = "(" . implode(")|(", $domainFirst) . ")";

        if (preg_match("/^" . $domainFirst . "/", $this->link)) {
            $this->link = "https://" . $this->link;
        } else {
            $this->link = "https://www." . $this->link;
        }

        $content = null;

        $domainZero = [
            'com',
            'co\.uk',
            'pt',
            'fr',
            'ca',
            'com\.au',
        ];

        $domainZero = "(" . implode(")|(", $domainZero) . ")";

        if (!(preg_match("/https\:\/\/(" . $domainFirst . ")pinterest\.(" . $domainZero . ")\/([a-z0-9\.\_-]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid pinterest profile link.');

            return false;
        } else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid pinterest profile link.');

            return false;
        }

        return true;
    }
}