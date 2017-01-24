<?php

declare(strict_types=1);

namespace AdminPanel\Symfony\AdminBundle\Twig\TokenParser;

use AdminPanel\Symfony\AdminBundle\Twig\Node\DataGridThemeNode;

class DataGridThemeTokenParser extends \Twig_TokenParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Twig_Token $token)
    {
        $stream = $this->parser->getStream();
        $dataGrid = $this->parser->getExpressionParser()->parseExpression();
        $theme = $this->parser->getExpressionParser()->parseExpression();
        $vars = new \Twig_Node_Expression_Array([], $stream->getCurrent()->getLine());

        if ($this->parser->getStream()->test(\Twig_Token::NAME_TYPE, 'with')) {
            $this->parser->getStream()->next();

            if ($this->parser->getStream()->test(\Twig_Token::PUNCTUATION_TYPE)) {
                $vars = $this->parser->getExpressionParser()->parseExpression();
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new DataGridThemeNode($dataGrid, $theme, $vars, $token->getLine(), $this->getTag());
    }

    /**
     * {@inheritDoc}
     */
    public function getTag()
    {
        return 'datagrid_theme';
    }
}
