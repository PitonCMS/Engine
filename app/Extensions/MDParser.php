<?php
/**
 * Piton Parsedown Extension
 *
 * Modified parser to render single image lines without the paragraph tags
 * https://gist.github.com/fxck/d65255218de3611df3cd
 */
namespace Piton\Extensions;

class MDParser extends \Parsedown
{
    /**
     * Markdown image definition regex
     *
     * @var string
     */
    private $markdownImage = "~^!\[.*?\]\(.*?\)$~";

    /**
     * {@inheritdoc}
     */
    protected function paragraph($Line)
    {
        // Override if MD image tag
        if (1 === preg_match($this->markdownImage, $Line['text'])) {
            return $this->inlineImage($Line);
        }

        return parent::paragraph($Line);
    }
}
