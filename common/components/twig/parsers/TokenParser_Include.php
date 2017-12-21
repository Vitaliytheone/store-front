<?php
namespace common\components\twig\parsers;

use Twig_TokenParser;
use Twig_Token;
use Twig_Error_Syntax;

/**
 * Includes a template.
 *
 * <pre>
 *   {% include 'header.html' %}
 *     Body
 *   {% include 'footer.html' %}
 * </pre>
 */
class TokenParser_Include extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        throw new Twig_Error_Syntax('Unknown "include" tag');
    }

    protected function parseArguments()
    {
        return array();
    }

    public function getTag()
    {
        return 'include';
    }
}