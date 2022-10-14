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
        $this->loadQueryConverter($config, $loader, $container);
        $this->loadVersioning($config, $loader, $container);
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

    /**
     * @throws Exception
     */
    private function loadVersioning(array $config, YamlFileLoader $loader, ContainerBuilder $container): void
    {
        if (!empty($config['versioning']['enabled'])) {
            $loader->load('versioning.yaml');

            $versionListener = $container->getDefinition('dmp_rest.versioning.listener');
            $versionListener->replaceArgument(1, $config['versioning']['default_version']);

            $resolvers = [];
            if ($config['versioning']['resolvers']['query']['enabled']) {
                $resolvers['query'] = $container->getDefinition('dmp_rest.versioning.query_parameter_resolver');
                $resolvers['query']->replaceArgument(0, $config['versioning']['resolvers']['query']['parameter_name']);
            }
            if ($config['versioning']['resolvers']['custom_header']['enabled']) {
                $resolvers['custom_header'] = $container->getDefinition('dmp_rest.versioning.header_resolver');
                $resolvers['custom_header']->replaceArgument(0, $config['versioning']['resolvers']['custom_header']['header_name']);
            }
            if ($config['versioning']['resolvers']['media_type']['enabled']) {
                $resolvers['media_type'] = $container->getDefinition('dmp_rest.versioning.media_type_resolver');
                $resolvers['media_type']->replaceArgument(0, $config['versioning']['resolvers']['media_type']['regex']);
            }

            $chainResolver = $container->getDefinition('dmp_rest.versioning.chain_resolver');
            foreach ($config['versioning']['guessing_order'] as $resolver) {
                if (isset($resolvers[$resolver])) {
                    $chainResolver->addMethodCall('addResolver', [$resolvers[$resolver]]);
                }
            }
        }
    }

    public function getAlias(): string
    {
        return 'dmp_rest';
    }
}
