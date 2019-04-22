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
 * Piton Message Controller
 *
 * Manages contact messages
 */
class AdminMessageController extends AdminBaseController
{
    /**
     * Show All Messages
     *
     */
    public function showMessages()
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');

        $messages = $messageMapper->find();
        var_dump($messages);
    }
}
