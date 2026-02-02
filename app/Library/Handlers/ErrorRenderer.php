<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

/**
 * Piton Error HTML Renderer
 *
 * Displays PitonCMS HTML Error page on Exceptions and Throwables that are not managed otherwise
 */
class ErrorRenderer implements ErrorRendererInterface
{
    /**
     * Invoke
     *
     * When called renders the HTML response.
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @return string
     */
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $title = '<h1>Oh No! Something went wrong...<h1>';
        $subTitle = '<p class="lead">A website error has occurred. <a href="/">Home</a></p>';
        $html = '';

        if ($displayErrorDetails) {
            $html .= '<h2>Details</h2>';
            $html .= $this->renderHtmlError($exception);
        } else {
            $subTitle = '<p class="lead">It wasn\'t anything you did. Try going to the <a href="/">home</a> page to start over.</p>';
        }

        // Note: Remember to escape '%' used in CSS with another '%' (as in '%%') so sprintf() doesn't get confused
        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><title>" .
            "Piton Application Error</title><style>body{margin:0;padding:30px;font:14px / 1.5 Helvetica," .
            " Arial, Verdana, sans-serif; background-color:hsl(0, 0%%, 94%%);}h1{margin:0;font-size:48px;" .
            "font-weight:normal;line-height:48px;}strong{display:inline-block;width:75px;} .lead{ font-size:18px;}" .
            " .navbar{ position:static; top:0; right:0; left:0; background-color:#336699; color:#ffffff; " .
            "font-size:22.5; padding:.75rem; padding-left:30px; margin-top:-30px; margin-left:-30px; " .
            "margin-right:-30px; margin-bottom:20px;}</style></head><body><div class=\"navbar\">PitonCMS " .
            "</div>%s %s %s</body></html>",
            $title,
            $subTitle,
            $html
        );

        return $output;
    }

    /**
     * Render Exception Stack Trace as HTML.
     *
     * @param Throwable $exception
     * @return string
     */
    private function renderHtmlError(Throwable $exception): string
    {
        $html = sprintf('<div><strong>Type:</strong> %s</div>', get_class($exception));

        if (($code = $exception->getCode())) {
            $html .= sprintf('<div><strong>Code:</strong> %s</div>', $code);
        }

        if (($message = $exception->getMessage())) {
            $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlspecialchars($message, ENT_QUOTES));
        }

        if (($file = $exception->getFile())) {
            $html .= sprintf('<div><strong>File:</strong> %s</div>', $file);
        }

        if (($line = $exception->getLine())) {
            $html .= sprintf('<div><strong>Line:</strong> %s</div>', $line);
        }

        if (($trace = $exception->getTraceAsString())) {
            $html .= '<h2>Trace</h2>';
            $html .= sprintf('<pre>%s</pre>', htmlspecialchars($trace, ENT_QUOTES));
        }

        return $html;
    }
}
