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

        $container->setParameter('n1c0_dissertation.model.dissertation.class', $config['class']['model']['dissertation']);

        $container->setParameter('n1c0_dissertation.model_manager_name', $config['model_manager_name']);

        $container->setAlias('n1c0_dissertation.manager.dissertation', $config['service']['manager']['dissertation']);
    }
}
