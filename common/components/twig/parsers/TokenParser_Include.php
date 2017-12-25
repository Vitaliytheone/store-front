<?php
namespace common\components\twig\parsers;

use Yii;
use Twig_TokenParser;
use Twig_Token;
use Twig_Node_Include;
use Twig_Node_Expression_Constant;
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
    protected $twigOptions;

    public function __construct($twigOptions = [])
    {
        $this->twigOptions = $twigOptions;
    }

    public function parse(Twig_Token $token)
    {
        $expressionParser = $this->parser->getExpressionParser();

        $expr = $expressionParser->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        $node = new Twig_Node_Include($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());

        if (empty($this->twigOptions['availableIncludes'])) {
            return $node;
        }

        $expression = $node->getNode('expr');
        if (empty($expression) || !($expression instanceof Twig_Node_Expression_Constant)) {
            throw new Twig_Error_Syntax('Unknown "include" tag');
        }

        $view = $expression->getAttribute('value');

        if (empty($view) || !in_array($view, $this->twigOptions['availableIncludes'])) {
            throw new Twig_Error_Syntax('Unknown "include" tag');
        }

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