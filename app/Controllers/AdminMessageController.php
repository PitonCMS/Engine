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

/**
 * Piton Message Controller
 *
 * Manages contact messages
 */
class AdminMessageController extends AdminBaseController
{
    /**
     * Show All Messages
     *
     * Displays all messages in descending date order
     * @param array $args
     */
    public function showMessages($args)
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $data['messages'] = $messageMapper->findAllInDateOrder();
        $data['status'] = $args['status'];

        return $this->render('messages/messages.html', $data);
    }

    /**
     * Toggle Status
     *
     * Sets the read status to the opposite of the current status
     */
    public function toggleStatus()
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');

        $messageId = (int) $this->request->getParsedBodyParam('id');
        $message = $messageMapper->findById($messageId);
        if ($message->isRead === 'Y') {
            $messageMapper->markAsUnread($messageId);
        } else {
            $messageMapper->markAsRead($messageId);
        }

        // Set the response type
        if ($this->request->isXhr()) {
            $r = $this->response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(["status" => "success"]));
        }

        return $this->redirect('adminMessages');
    }

    /**
     * Delete Message
     */
    public function delete()
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

        return $this->redirect('adminMessages');
    }
}
