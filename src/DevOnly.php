<?php

namespace Eimanavicius\WordPress;

use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Script\Event;

class DevOnly
{

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
}
