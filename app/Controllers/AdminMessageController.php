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
 * Piton Message Controller
 *
 * Manages contact messages
 */
class AdminMessageController extends AdminBaseController
{
    /**
     * Show Messages Page
     *
     * Loads message management page and list of new messages
     * @param void
     * @return Response
     */
    public function showMessages(): Response
    {
        $data['messages'] = $this->loadMessages();
        return $this->render('messages/messages.html', $data);
    }

    /**
     * Get Messages
     *
     * XHR Request
     * Returns filtered message list
     * @param void
     * @return Response
     */
    public function getMessages(): Response
    {
        try {
            $messages = $this->loadMessages();

            // Make string template
            $template =<<<HTML
            {% import "@admin/messages/_messageMacros.html" as messageMacro %}
            {% for m in messages %}
                {{ messageMacro.messageRow(m) }}
            {% endfor %}

            {{ pagination() }}
HTML;

            $status = "success";
            $text = $this->container->view->fetchFromString($template, ['messages' => $messages]);
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting messages: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Load Messages
     *
     * Get all messages using query string params
     * @param void
     * @return string
     * @uses GET params
     */
    protected function loadMessages(): array
    {
        // Get dependencies
        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $pagination = $this->container->adminPagePagination;

        $option = $this->request->getQueryParam('status', 'unread');

        // Get all messages and setup pagination
        $messages = $messageMapper->findMessages($option, $pagination->getLimit(), $pagination->getOffset()) ?? [];
        $pagination->setTotalResultsFound($messageMapper->foundRows() ?? 0);
        $pagination->setPagePath($this->container->router->pathFor('adminMessage'));
        $this->container->view->addExtension($pagination);

        return $messages;
    }

    /**
     * Toggle Status
     *
     * XHR Request
     * Toggles the message status (Read, Archive), and also Delete
     * @param void
     * @return Response
     * @uses POST
     */
    public function updateStatus(): Response
    {
        // Message control logic:
        // - New messages can be archived or set to read status
        // - Read messages can be archived or set to unread status
        // - Archived messages can set to read status
        // - Any message can be deleted

        try {
            // Get dependencies
            $messageMapper = ($this->container->dataMapper)('MessageMapper');
            $messageId = (int) $this->request->getParsedBodyParam('messageId');
            $controlRequest = $this->request->getParsedBodyParam('control');

            $message = $messageMapper->findById($messageId);
            if ($controlRequest === 'delete') {
                // Delete request
                $messageMapper->delete($message);
            } elseif ($controlRequest === 'archive') {
                // Archive request. Set to A if in Y|N status, otherwise unarchive by setting to Y
                $message->is_read = in_array($message->is_read, ['Y','N']) ? 'A' : 'Y';
                $messageMapper->save($message);
            } elseif ($controlRequest === 'read') {
                // Read toggle request. Set to Y if in A|N status, otherwise set to N
                $message->is_read = in_array($message->is_read, ['A','N']) ? 'Y' : 'N';
                $messageMapper->save($message);
            }

            $status = "success";
            $text = "Updated message";
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception updating message status: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Get New Message Count
     *
     * Gets count of messages with Undread status
     * @param void
     * @return Response
     */
    public function getNewMessageCount(): Response
    {
        try {
            $messageMapper = ($this->container->dataMapper)('MessageMapper');
            $count = $messageMapper->findUnreadCount();

            $status = "success";
            $text = ($count === 0) ? null : "$count";
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception updating message status: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
    }
}
