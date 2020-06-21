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
     * Sets the read status to the opposite of the current status
     * @param void
     * @return Response
     */
    public function toggleStatus(): Response
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');

        $messageId = (int) $this->request->getParsedBodyParam('id');
        $message = $messageMapper->findById($messageId);
        if ($message->is_read === 'Y') {
            $messageMapper->markAsUnread($messageId);
        } else {
            $messageMapper->markAsRead($messageId);
        }

        // Set the response type
        if ($this->request->isXhr()) {
            $r = $this->response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(["status" => "success"]));
        }

        return $this->redirect('adminMessage');
    }

    /**
     * Delete Message
     * @param void
     * @return Response
     */
    public function delete(): Response
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');

        $message = $messageMapper->make();
        $message->id = (int) $this->request->getParsedBodyParam('id');
        $messageMapper->delete($message);

        // Set the response type
        if ($this->request->isXhr()) {
            $r = $this->response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(["status" => "success"]));
        }

        return $this->redirect('adminMessage');
    }
}
