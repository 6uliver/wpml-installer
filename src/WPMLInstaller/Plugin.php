<?php

namespace WPMLInstaller;

use Composer\Composer;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Dotenv\Dotenv;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    const WPML_PACKAGE_NAME = "wpml/multilingual-cms";
    const WPML_BASE_URL = "https://wpml.org/";
    const WPML_DOWNLOAD_ID = "6088";
    const USER_ID_VARIABLE = "WPML_USER_ID";
    const SUBSCRIPTION_KEY_VARIABLE = "WPML_SUBSCRIPTION_KEY";

    private $dotenv;

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        if (file_exists(getcwd().DIRECTORY_SEPARATOR.'.env')) {
            $this->dotenv = new Dotenv(getcwd());
            $this->dotenv->load();
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::PRE_PACKAGE_INSTALL => 'updateDistUrl',
            PackageEvents::PRE_PACKAGE_UPDATE => 'updateDistUrl'
        ];
    }

    protected static function getPackage(OperationInterface $operation)
    {
        if ($operation->getJobType() === 'update') {
            return $operation->getTargetPackage();
        } else {
            return $operation->getPackage();
        }
    }

    public function updateDistUrl(PackageEvent $event)
    {
        $package = self::getPackage($event->getOperation());

        if ($package->getName() === self::WPML_PACKAGE_NAME) {
            $package->setDistUrl($this->getDistUrl($package));
        }
    }

    protected function getUserId()
    {
        return getenv(self::USER_ID_VARIABLE);
    }

    protected function getSubscriptionKey()
    {
        return getenv(self::SUBSCRIPTION_KEY_VARIABLE);
    }

    protected function getDistUrl(PackageInterface $package)
    {
        return self::WPML_BASE_URL .
            '?download=' . self::WPML_DOWNLOAD_ID .
            '&user_id=' . $this->getUserId() .
            '&subscription_key=' . $this->getSubscriptionKey() .
            '&version=' . $package->getPrettyVersion();
    }
}
