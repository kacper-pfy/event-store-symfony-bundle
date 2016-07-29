<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/event-store-symfony-bundle for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/event-store-symfony-bundle/blob/master/LICENSE.md New BSD License
 */

declare (strict_types = 1);

namespace Prooph\Bundle\EventStore\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class PluginsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('prooph_event_store.stores')) {
            return;
        }

        $stores = $container->getParameter('prooph_event_store.stores');

        foreach ($stores as $name => $store) {
            $plugins = $container->findTaggedServiceIds(sprintf('prooph_event_store.%s.plugin', $name));

            foreach ($plugins as $id => $args) {
                $definition = $container->findDefinition($id);
                $definition->addMethodCall('setUp', [new Reference($store)]);
            }
        }
    }
}
