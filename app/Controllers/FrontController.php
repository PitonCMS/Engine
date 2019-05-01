<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

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
     */
    public function showPage($args)
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pageSettingMapper = ($this->container->dataMapper)('PageSettingMapper');
        $pageElementMedia = ($this->container->dataMapper)('PageElementMediaMapper');

        if (isset($args['slug2'])) {
            // This request is for a collection
            $page = $pageMapper->findPublishedCollectionPageBySlug($args['slug1'], $args['slug2']);
        } else {
            // Get page data
            $page = $pageMapper->findPublishedPageBySlug($args['slug1']);
        }

        // Send 404 if not found
        if (empty($page)) {
            return $this->notFound();
        }

        // Get elements and assign to blocks
        $page->blocks = $this->buildElementsByBlock($pageElementMedia->findElementsByPageId($page->id));

        // Get page settings
        $page->settings = $this->buildPageSettings($pageSettingMapper->findPageSettings($page->id));

        return $this->render($page->template, $page);
    }

    /**
     * Submit Contact Message
     *
     * @param POST array
     */
    public function submitMessage()
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $email = $this->container->emailHandler;

        // Check honepot and if clean, then submit message
        if ('alt@example.com' === $this->request->getParsedBodyParam('alt-email')) {
            $message = $messageMapper->make();
            $message->name = $this->request->getParsedBodyParam('name');
            $message->email = $this->request->getParsedBodyParam('email');
            $message->message = $this->request->getParsedBodyParam('message');
            $messageMapper->save($message);

            // Send message to workflow email
            $siteName = empty($this->siteSettings['displayName']) ? 'PitonCMS' : $this->siteSettings['displayName'];
            $email->setTo($this->siteSettings['contactFormEmail'], '')
                    ->setSubject("New Contact Message to $siteName")
                    ->setMessage("From: {$message->email}\n\n{$message->message}")
                    ->send();
        }

        // Set the response type and return
        $r = $this->response->withHeader('Content-Type', 'application/json');
        return $r->write(json_encode(["response" => $this->siteSettings['contactFormAcknowledgement']]));
    }
}
