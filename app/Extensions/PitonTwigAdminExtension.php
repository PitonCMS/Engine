<?php
/**
 * Custom Piton Admin Extensions for Twig
 *
 * Has Twig functions and filters to be used on the admin site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
namespace Piton\Extensions;

use Interop\Container\ContainerInterface;

class PitonTwigAdminExtension extends PitonTwigBaseExtension
{

    /**
     * Constructor
     *
     * @param obj Interop\Container\ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Register Custom Filters
     */
    public function getFilters()
    {
        return array_merge(parent::getFilters(), [
            // Custom filters
        ]);
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions()
    {
        return array_merge(parent::getFunctions(), [
            new \Twig_SimpleFunction('adminSomething', array($this, 'adminSomething')),
        ]);
    }

    /**
     * Get Path for Named Route
     *
     * @param none
     * @return string
     */
    public function adminSomething()
    {
        return 'Admin';
    }
}
