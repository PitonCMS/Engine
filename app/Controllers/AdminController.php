<?php
/**
 * Admin Controller
 */
namespace Piton\Controllers;

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
