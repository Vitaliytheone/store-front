<?php

namespace sommerce\components\validators\link;


class LinkedinProfile extends BaseLinkValidator
{
    public function validate()
    {
        if (FALSE === strpos($this->link, '/')) {
            $this->link = 'linkedin.com/' . $this->link;
        }

        $this->link = preg_replace("/[a-z]+\.linkedin/i", "linkedin", $this->link);

        $this->link = "https://www." . parse_url($this->link, PHP_URL_HOST) . parse_url($this->link, PHP_URL_PATH);

        $content = null;

        if (!(preg_match("/https\:\/\/www\.linkedin\.com\/in\/([^\/]+)(\/)?$/i", $this->link))) {
            $this->addError('Invalid LinkedIn profile link.');

            return false;
        } /*else if (!($content = $this->checkUrl($this->link))) {
            $this->addError('Invalid LinkedIn profile link.');
        }*/

        return true;
    }
}