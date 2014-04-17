<?php

/**
 * This file is part of the N1c0DissertationBundle package.
 *
 * (c) 
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace N1c0\DissertationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class n1c0DissertationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (array_key_exists('acl', $config)) {
            $this->loadAcl($container, $config);
        }

        $container->setParameter('n1c0_dissertation.model.dissertation.class', $config['class']['model']['dissertation']);
        $container->setParameter('n1c0_dissertation.model.argument.class', $config['class']['model']['argument']);
        $container->setParameter('n1c0_dissertation.model.introduction.class', $config['class']['model']['introduction']);

        $container->setParameter('n1c0_dissertation.model_manager_name', $config['model_manager_name']);

        $container->setParameter('n1c0_dissertation.form.dissertation.type', $config['form']['dissertation']['type']);
        $container->setParameter('n1c0_dissertation.form.argument.type', $config['form']['argument']['type']);
        $container->setParameter('n1c0_dissertation.form.introduction.type', $config['form']['introduction']['type']);

        $container->setParameter('n1c0_dissertation.form.dissertation.name', $config['form']['dissertation']['name']);
        $container->setParameter('n1c0_dissertation.form.argument.name', $config['form']['argument']['name']);
        $container->setParameter('n1c0_dissertation.form.introduction.name', $config['form']['introduction']['name']);

        $container->setAlias('n1c0_dissertation.form_factory.dissertation', $config['service']['form_factory']['dissertation']);
        $container->setAlias('n1c0_dissertation.form_factory.argument', $config['service']['form_factory']['argument']);
        $container->setAlias('n1c0_dissertation.form_factory.introduction', $config['service']['form_factory']['introduction']);

        $container->setAlias('n1c0_dissertation.manager.dissertation', $config['service']['manager']['dissertation']);
        $container->setAlias('n1c0_dissertation.manager.argument', $config['service']['manager']['argument']);
        $container->setAlias('n1c0_dissertation.manager.introduction', $config['service']['manager']['introduction']);

        // Add a condition if markup so...
        $container->setAlias('n1c0_dissertation.markup', new Alias($config['service']['markup'], false));
    }

    protected function loadAcl(ContainerBuilder $container, array $config)
    {
        //$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        //$loader->load('acl.xml');

        foreach (array(1 => 'create', 'view', 'edit', 'delete') as $index => $perm) {
            $container->getDefinition('n1c0_dissertation.acl.dissertation.roles')->replaceArgument($index, $config['acl_roles']['dissertation'][$perm]);
            $container->getDefinition('n1c0_dissertation.acl.argument.roles')->replaceArgument($index, $config['acl_roles']['argument'][$perm]);
            $container->getDefinition('n1c0_dissertation.acl.introduction.roles')->replaceArgument($index, $config['acl_roles']['introduction'][$perm]);
        }

        $container->setAlias('n1c0_dissertation.acl.dissertation', $config['service']['acl']['dissertation']);
        $container->setAlias('n1c0_dissertation.acl.argument', $config['service']['acl']['argument']);
        $container->setAlias('n1c0_dissertation.acl.introduction', $config['service']['acl']['introduction']);
    }
}
