<?php

namespace Eimanavicius\WordPress;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class DevOnly implements PluginInterface, EventSubscriberInterface
{

    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * @param \Composer\Script\Event $event
     */
    public static function register_wpcs(Event $event)
    {
        if ($event->isDevMode()) {
            $composer = $event->getComposer();
            self::execute_command(
                $composer->getEventDispatcher(),
                'phpcs',
                ['--config-set', 'installed_paths', self::resolve_wpcs_path($composer->getConfig())]
            );
        }
    }

    /**
     * @param EventDispatcher $dispatcher
     * @param string $command
     * @param array $arguments
     */
    private static function execute_command(EventDispatcher $dispatcher, $command, array $arguments)
    {
        $dispatcher->addListener('__exec_devonly', $command);
        $dispatcher->dispatchScript('__exec_devonly', true, $arguments);
    }

    /**
     * @param Config $config
     *
     * @return string
     */
    private static function resolve_wpcs_path(Config $config)
    {
        return $config->get('vendor-dir') . '/wp-coding-standards/wpcs';
    }

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
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
            ScriptEvents::POST_UPDATE_CMD => 'register_wpcs',
            ScriptEvents::POST_INSTALL_CMD => 'register_wpcs',
        ];
    }
}
