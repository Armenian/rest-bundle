<?php

declare(strict_types=1);

namespace DMP\RestBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RestExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->loadBodyConverter($config, $loader, $container);
    }

    /**
     * @throws Exception
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('prepend.yaml');
    }

    /**
     * @throws Exception
     */
    private function loadBodyConverter(array $config, YamlFileLoader $loader, ContainerBuilder $container): void
    {
        if (!$this->isConfigEnabled($container, $config['body_converter'])) {
            return;
        }

        $loader->load('request_body_converter.yaml');

        if (!empty($config['body_converter']['validation_errors_argument'])) {

            $container->getDefinition('dmp_rest.converter.request_body')
                ->replaceArgument('$validationErrorsArgument', $config['body_converter']['validation_errors_argument']);
        }
    }

    /**
     * @throws Exception
     */
    private function loadQueryConverter(array $config, YamlFileLoader $loader, ContainerBuilder $container): void
    {
        if (!$this->isConfigEnabled($container, $config['query_converter'])) {
            return;
        }

        $loader->load('request_query_converter.yaml');

        if (!empty($config['query_converter']['validation_errors_argument'])) {
            $container->getDefinition('dmp_rest.converter.request_query')
                ->replaceArgument('$validationErrorsArgument', $config['query_converter']['validation_errors_argument']);
        }
    }

    public function getAlias(): string
    {
        return 'dmp_rest';
    }
}
