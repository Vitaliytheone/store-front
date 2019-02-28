<?php

namespace sommerce\components\twig\parsers;

use common\models\sommerce\PageFiles;
use sommerce\helpers\PageFilesHelper;
use Twig_Error_Syntax;
use Twig_Node_Expression_Constant;
use Twig_Node_Include;
use Twig_Token;
use Twig_TokenParser;
use yii\helpers\ArrayHelper;

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

        $expression = $node->getNode('expr');
        if (empty($expression) || !($expression instanceof Twig_Node_Expression_Constant)) {
            throw new Twig_Error_Syntax('Unknown "include" tag');
        }

        $view = $expression->getAttribute('value');
        $files = [];

        foreach((array)ArrayHelper::getValue(PageFilesHelper::getFilesGroupByType(), PageFiles::FILE_TYPE_TWIG, []) as $file) {
            $files['/snippets/' . $file['file_name']] = $file['content'];
        }

        if (empty($view) || empty($files[$view])) {
            throw new Twig_Error_Syntax($view . ' file is not supported');
        }

        $expression->setAttribute('value', $view);

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