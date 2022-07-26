<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown;

use Highlight\Highlighter;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Util\Xml;

class FencedCodeRenderer implements BlockRendererInterface
{
    function __construct() {
        $this->hl = new Highlighter();
    }

    /**
     * @param AbstractBlock $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement|string
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof FencedCode)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $attrs = [];
        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = Xml::escape($value);
        }

        $content = $block->getStringContent();

        $language = $this->getLanguage($block->getInfoWords());
        $highlighted = false;
        if ($language) {
            $attrs['class'] = isset($attrs['class']) ? $attrs['class'] . ' ' : '';

            try {
                $highlighted = $this->hl->highlight($language, $content);
                $content = $highlighted->value;
                $attrs['class'] .= 'hljs ' . $highlighted->language;
            } catch (Exception $e) {
                $attrs['class'] .= 'language-' . $language;
            }
        }

        if (!$highlighted) {
            $content = Xml::escape($content);
        }

        return new HtmlElement(
            'pre',
            [],
            new HtmlElement('code', $attrs, $content)
        );
    }

    public function getLanguage($infoWords)
    {
        if (count($infoWords) === 0 || strlen($infoWords[0]) === 0) {
            return false;
        }

        return Xml::escape($infoWords[0], true);
    }
}
