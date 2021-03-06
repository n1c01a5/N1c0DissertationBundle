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
                ->scalarNode('model_manager_name')->defaultNull()->end()

                ->arrayNode('form')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('dissertation')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_dissertation_dissertation')->end()
                                ->scalarNode('name')->defaultValue('n1c0_dissertation_dissertation')->end()
                            ->end()
                        ->end()
                        ->arrayNode('introduction')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_dissertation_introduction')->end()
                                ->scalarNode('name')->defaultValue('n1c0_dissertation_introduction')->end()
                            ->end()
                        ->end()
                        ->arrayNode('part')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_dissertation_part')->end()
                                ->scalarNode('name')->defaultValue('n1c0_dissertation_part')->end()
                            ->end()
                        ->end()
                        ->arrayNode('argument')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_dissertation_argument')->end()
                                ->scalarNode('name')->defaultValue('n1c0_dissertation_argument')->end()
                            ->end()
                        ->end()
                        ->arrayNode('conclusion')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_dissertation_conclusion')->end()
                                ->scalarNode('name')->defaultValue('n1c0_dissertation_conslucion')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('class')->isRequired()
                    ->children()
                        ->arrayNode('model')->isRequired()
                            ->children()
                                ->scalarNode('dissertation')->isRequired()->end()
                                ->scalarNode('introduction')->isRequired()->end()
                                ->scalarNode('part')->isRequired()->end()
                                ->scalarNode('argument')->isRequired()->end()
                                ->scalarNode('conclusion')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('acl')->end()

                ->arrayNode('acl_roles')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('dissertation')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('introduction')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('part')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('argument')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('conclusion')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('service')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('manager')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('dissertation')->cannotBeEmpty()->defaultValue('n1c0_dissertation.manager.dissertation.default')->end()
                                ->scalarNode('introduction')->cannotBeEmpty()->defaultValue('n1c0_dissertation.manager.introduction.default')->end()
                                ->scalarNode('part')->cannotBeEmpty()->defaultValue('n1c0_dissertation.manager.part.default')->end()
                                ->scalarNode('argument')->cannotBeEmpty()->defaultValue('n1c0_dissertation.manager.argument.default')->end()
                                ->scalarNode('conclusion')->cannotBeEmpty()->defaultValue('n1c0_dissertation.manager.conclusion.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('acl')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('dissertation')->cannotBeEmpty()->defaultValue('n1c0_dissertation.acl.dissertation.security')->end()
                                ->scalarNode('introduction')->cannotBeEmpty()->defaultValue('n1c0_dissertation.acl.introduction.security')->end()
                                ->scalarNode('part')->cannotBeEmpty()->defaultValue('n1c0_dissertation.acl.part.security')->end()
                                ->scalarNode('argument')->cannotBeEmpty()->defaultValue('n1c0_dissertation.acl.argument.security')->end()
                                ->scalarNode('conclusion')->cannotBeEmpty()->defaultValue('n1c0_dissertation.acl.conclusion.security')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_factory')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('dissertation')->cannotBeEmpty()->defaultValue('n1c0_dissertation.form_factory.dissertation.default')->end()
                                ->scalarNode('introduction')->cannotBeEmpty()->defaultValue('n1c0_dissertation.form_factory.introduction.default')->end()
                                ->scalarNode('part')->cannotBeEmpty()->defaultValue('n1c0_dissertation.form_factory.part.default')->end()
                                ->scalarNode('argument')->cannotBeEmpty()->defaultValue('n1c0_dissertation.form_factory.argument.default')->end()
                                ->scalarNode('conclusion')->cannotBeEmpty()->defaultValue('n1c0_dissertation.form_factory.conclusion.default')->end()

                            ->end()
                        ->end()
                        ->scalarNode('markup')->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
