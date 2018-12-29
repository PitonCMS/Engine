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
 * Piton Admin Controller
 */
class AdminController extends AdminBaseController
{
    /**
     * Admin Home Page
     *
     */
    public function home()
    {
        return $this->render('home.html');
    }
}
