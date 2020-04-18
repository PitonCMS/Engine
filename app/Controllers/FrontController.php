<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Slim\Http\Response;

/**
 * Piton Front End Controller
 *
 */
class FrontController extends FrontBaseController
{
    /**
     * Show Page
     *
     * Displays page matching URL slug, or throws 404 Not Found
     * @param array $args Array of URL parameters, expecting 'slug1', 'slug2'
     * @return Response
     */
    public function showPage(array $args): Response
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $dataStoreMapper = ($this->container->dataMapper)('DataStoreMapper');
        $pageElement = ($this->container->dataMapper)('PageElementMapper');

        if (isset($args['slug2'])) {
            // This request is for a collection detail page
            $page = $pageMapper->findPublishedCollectionPageBySlug($args['slug1'], $args['slug2']);
        } else {
            // This request is for a page
            $page = $pageMapper->findPublishedPageBySlug($args['slug1']);
        }

        // Send 404 if not found
        if (empty($page)) {
            return $this->notFound();
        }

        // Get and set block elements
        $page->setBlockElements($pageElement->findElementsByPageId($page->id));

        // Get and set page data key-value pairs
        $page->setDataKeyValues($dataStoreMapper->findPageSettings($page->id));

        return $this->render($page->template, $page);
    }

    /**
     * Submit Contact Message
     *
     * Expects POST array
     * @param void
     * @return Response
     */
    public function submitMessage(): Response
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $email = $this->container->emailHandler;

        // Check honepot and if clean, then save message
        if ('alt@example.com' === $this->request->getParsedBodyParam('alt-email')) {
            $message = $messageMapper->make();
            $message->name = $this->request->getParsedBodyParam('name');
            $message->email = $this->request->getParsedBodyParam('email');
            $message->message = $this->request->getParsedBodyParam('message');
            $messageMapper->save($message);

            // Send message to workflow email
            if ($this->siteSettings['contactFormEmail']) {
                // Only send email if a contact form email setting is saved
                $siteName = empty($this->siteSettings['displayName']) ? 'PitonCMS' : $this->siteSettings['displayName'];
                $email->setTo($this->siteSettings['contactFormEmail'], '')
                    ->setSubject("New Contact Message to $siteName")
                    ->setMessage("{$message->name}\n{$message->email}\n\n{$message->message}")
                    ->send();
            }
        }

        // Set the response type and return
        $responseText = $this->siteSettings['contactFormAcknowledgement'] ?? "Thank You";
        $r = $this->response->withHeader('Content-Type', 'application/json');
        return $r->write(json_encode(["status" => "success", "response" => $responseText]));
    }
}
