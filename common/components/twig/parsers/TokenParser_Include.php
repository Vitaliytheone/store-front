<?php
namespace common\components\twig\parsers;

use Twig_TokenParser;
use Twig_Token;
use Twig_Node_Include;

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
        $expressionParser = $this->parser->getExpressionParser();

        $expr = $expressionParser->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        $node = new Twig_Node_Include($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());

        return $node;
    }

    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $ignoreMissing = false;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'ignore')) {
            $stream->expect(Twig_Token::NAME_TYPE, 'missing');

            $ignoreMissing = true;
        }

        $variables = null;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'only')) {
            $only = true;
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return array($variables, $only, $ignoreMissing);

    }

    public function getTag()
    {
        return 'include';
    }
}