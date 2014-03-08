<?php

namespace N1c0\DissertationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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

        $container->setParameter('n1c0_dissertation.model_manager_name', $config['model_manager_name']);

        $container->setParameter('n1c0_dissertation.form.dissertation.type', $config['form']['dissertation']['type']);
        $container->setParameter('n1c0_dissertation.form.argument.type', $config['form']['argument']['type']);

        $container->setParameter('n1c0_dissertation.form.dissertation.name', $config['form']['dissertation']['name']);
        $container->setParameter('n1c0_dissertation.form.argument.name', $config['form']['argument']['name']);

        $container->setAlias('n1c0_dissertation.form_factory.dissertation', $config['service']['form_factory']['dissertation']);
        $container->setAlias('n1c0_dissertation.form_factory.argument', $config['service']['form_factory']['argument']);

        $container->setAlias('n1c0_dissertation.manager.dissertation', $config['service']['manager']['dissertation']);
        $container->setAlias('n1c0_dissertation.manager.argument', $config['service']['manager']['argument']);
    }

    protected function loadAcl(ContainerBuilder $container, array $config)
    {
        //$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        //$loader->load('acl.xml');

        foreach (array(1 => 'create', 'view', 'edit', 'delete') as $index => $perm) {
            $container->getDefinition('n1c0_dissertation.acl.dissertation.roles')->replaceArgument($index, $config['acl_roles']['dissertation'][$perm]);
        }

        $container->setAlias('n1c0_dissertation.acl.dissertation', $config['service']['acl']['dissertation']);
    }
}
