<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Slim\Http\Response;
use Throwable;

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

        // Get and set page settings key-value pairs
        $page->setDataKeyValues($dataStoreMapper->findPageSettings($page->id));

        return $this->render("{$page->template}.html", $page);
    }

    /**
     * Submit Contact Message
     *
     * XHR Request
     * @param void
     * @return Response
     * @uses POST
     */
    public function submitMessage(): Response
    {
        try {
            $messageMapper = ($this->container->dataMapper)('MessageMapper');
            $email = $this->container->emailHandler;

            // Check honepot and if clean, then save message
            if ('alt@example.com' === $this->request->getParsedBodyParam('alt-email')) {
                $message = $messageMapper->make();
                $message->name = $this->request->getParsedBodyParam('name');
                $message->email = $this->request->getParsedBodyParam('email');
                $message->message = $this->request->getParsedBodyParam('message');
                $message->context = $this->request->getParsedBodyParam('context', 'Unknown Page');
                $messageMapper->save($message);

                // Send message to workflow email
                if (!empty($this->settings['site']['contactFormEmail'])) {
                    // Only send email if a contact form email setting is saved
                    $siteName = $this->settings['site']['displayName'] ?? 'PitonCMS';
                    $email->setTo($this->settings['site']['contactFormEmail'], '')
                        ->setSubject("New Contact Message to $siteName")
                        ->setMessage("{$message->name}\n{$message->email}\n{$message->context}\n\n{$message->message}")
                        ->send();
                }
            }
            $status = "success";
        } catch (Throwable $th) {
            $status = "error";
            $this->container->logger->alert("PitonCMS: Exception submitting contact message " . $th->getMessage());
        }

        // Set the response type and return
        $text = $this->settings['site']['contactFormAcknowledgement'] ?? "Thank You";

        return $this->xhrResponse($status, $text);
    }
}
