<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Handlers;

use Piton\Session\SessionHandler;
use Piton\Interfaces\SessionInterface;

/**
 * Piton Session Class
 *
 * To use a different session manager class, implement Piton\Interfaces\SessionInterface
 * and override the sessionHandler dependency in the container.
 */
class Session extends SessionHandler implements SessionInterface
{

}
