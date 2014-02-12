<?php

namespace Nucleus\Bundle\ConsoleBundle\DependencyInjection;

use \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use \Symfony\Component\HttpKernel\DependencyInjection\Extension;
use \Symfony\Component\DependencyInjection\ContainerBuilder;

class NucleusConsoleExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Handles the knp_menu configuration.
     *
     * @param array            $configs   The configurations being loaded
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    public function getAlias()
    {
        return 'nucleus_console';
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'nucleus_core',
            array(
                'annotation_container_generators' => array(
                    'bound' => array(
                        'annotationClass' => 'Nucleus\Console\CommandLine',
                        'generatorClass' => 'Nucleus\Bundle\ConsoleBundle\DependencyInjection\CommandLineAnnotationContainerGenerator'
                    ),
                )
            )
        );
    }
}