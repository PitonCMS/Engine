<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Slim\Http\Response;
use Throwable;
use Exception;

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
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');

        if (isset($args['slug2'])) {
            // This request is for a collection detail page
            $page = $pageMapper->findPublishedCollectionPageBySlug($args['slug1'], $args['slug2']);
        } else {
            // This request is for a page
            $page = $pageMapper->findPublishedPageBySlug($args['slug1']);
        }

        // Return 404 if not found
        if (empty($page)) {
            return $this->notFound();
        }

        // Get page elements
        $elements = $pageElementMapper->findElementsByPageId($page->id) ?? [];

        // Get and set page and element settings
        $settings = $dataStoreMapper->findPageAndElementSettingsByPageId($page->id) ?? [];
        $page->setPageSettings($settings);
        array_walk($elements, function ($el) use ($settings) {
            $el->setElementSettings($settings);
        });

        // Set elements in blocks
        $page->setBlockElements($elements);

        // Increment page view_count
        $pageMapper->incrementPageViewCount($page->id);

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
        // Get dependencies
        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $messageDataMapper = ($this->container->dataMapper)('MessageDataMapper');
        $definition = $this->container->jsonDefinitionHandler;
        $email = $this->container->emailHandler;

        // Get response message and status
        $status = "success";
        $text = $this->settings['site']['contactFormAcknowledgement'] ?? "Thank You";

        try {
            $this->container->logger->debug('Trying to send contact email');

            // Check honepot before saving message
            if ('alt@example.com' !== $this->request->getParsedBodyParam('alt-email')) {
                throw new Exception('Honeypot found a fly');
            }

            $this->container->logger->debug('...Passed honeypot test');

            // Check if there is a message to save
            if (empty($this->request->getParsedBodyParam('email'))) {
                throw new Exception('Empty message submitted');
            }

            $this->container->logger->debug('...Passed empty message test');

            // Check that we have the minimum number of message characters
            $minLength = $this->container->get('settings')['site']['minMessageLength'] ?? 1;
            if (mb_strlen($this->request->getParsedBodyParam('message')) < (int) $minLength) {
                throw new Exception('Message less than minimum length');
            }

            $this->container->logger->debug('...Passed message length test');
            $this->container->logger->debug('...Saving message to DB...');

            // Save message
            $message = $messageMapper->make();
            $message->name = $this->request->getParsedBodyParam('name');
            $message->email = $this->request->getParsedBodyParam('email');
            $message->message = $this->request->getParsedBodyParam('message');
            $message->context = $this->request->getParsedBodyParam('context', 'Unknown');
            $message = $messageMapper->save($message);

            $this->container->logger->debug('...Saved message to DB');

            // Check if there are custom contact fields to save
            $contactInputsDefinition = $definition->getContactInputs();

            if ($contactInputsDefinition) {
                $appendMessageText = "\n";
                $this->container->logger->debug('...Appending extra contact fields');

                // Go through defined contact custom fields and match to POST array
                foreach ($contactInputsDefinition as $field) {
                    // Check if there is matching input to save
                    if (!$this->request->getParsedBodyParam($field->key)) {
                        continue;
                    }

                    // Create message text to append to email
                    $appendMessageText .= "\n" . $field->name . ": " . $this->request->getParsedBodyParam($field->key);

                    // Save to data store
                    $dataStore = $messageDataMapper->make();
                    $dataStore->message_id = $message->id;
                    $dataStore->data_key = $field->key;
                    $dataStore->data_value = $this->request->getParsedBodyParam($field->key);
                    $messageDataMapper->save($dataStore);
                }
            }

            $this->container->logger->debug('...Ready to send email');

            // Send message to workflow email if an email address has been saved to settings
            if (!empty($this->settings['site']['contactFormEmail'])) {
                $this->container->logger->debug('...Building Mailer Message');
                $siteName = $this->settings['site']['siteName'] ?? 'PitonCMS';

                $messageText = "{$message->name}\n{$message->email}\n{$message->context}\n\n{$message->message}";

                if (isset($appendMessageText)) {
                    $messageText .= $appendMessageText;
                }

                $this->container->logger->debug('...Setting mail fields and sending');

                $email->setReplyTo($message->email)
                        ->setTo($this->settings['site']['contactFormEmail'], '')
                        ->setSubject("New {$message->context} inquiry from $siteName")
                        ->setMessage($messageText)
                        ->send();
            } else {
                $this->container->logger->error('...No contactFormEmail saved. Will not send email');
            }

            $this->container->logger->debug('...End send email');

        } catch (Throwable $th) {
            // Log issue
            $this->container->logger->error("PitonCMS: Exception submitting contact message: " . $th->getMessage());
            $status = "error";
            $text = 'There was an error submitting your message.';
        }

        // Send the response
        return $this->xhrResponse($status, $text);
    }
}
