<?php

namespace N1c0\DissertationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('n1c0_dissertation')
            ->children()
            
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()
                ->arrayNode('dissertation')->isRequired()
                    ->children()
                        ->scalarNode('class')->isRequired()->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
