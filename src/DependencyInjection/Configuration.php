<?php

declare(strict_types=1);

namespace DMP\RestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dmp_rest');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('pagination')->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('limit')->defaultValue(100)->end()
                        ->integerNode('maxLimit')->defaultValue(200)->end()
                    ->end()
                ->end()
                ->arrayNode('body_converter')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('validate')
                            ->defaultFalse()
                            ->beforeNormalization()
                                ->ifTrue()
                                ->then(function ($value) {
                                    if (!class_exists(OptionsResolver::class)) {
                                        throw new InvalidConfigurationException("'body_converter.validate: true' requires OptionsResolver component installation ( composer require symfony/options-resolver )");
                                    }
                                    return $value;
                                })
                            ->end()
                        ->end()
                        ->scalarNode('validation_errors_argument')->defaultValue('validationErrors')->end()
                    ->end()
                ->end()
                ->arrayNode('query_converter')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('validate')
                            ->defaultFalse()
                            ->beforeNormalization()
                                ->ifTrue()
                                ->then(function ($value) {
                                    if (!class_exists(OptionsResolver::class)) {
                                        throw new InvalidConfigurationException("'body_converter.validate: true' requires OptionsResolver component installation ( composer require symfony/options-resolver )");
                                    }
                                    return $value;
                                })
                            ->end()
                        ->end()
                        ->scalarNode('validation_errors_argument')->defaultValue('validationErrors')->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        $this->addVersioningSection($rootNode);

        return $treeBuilder;
    }

    private function addVersioningSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
        ->children()
            ->arrayNode('versioning')
                ->canBeEnabled()
                ->children()
                    ->scalarNode('default_version')->defaultNull()->end()
                    ->arrayNode('resolvers')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('query')
                                ->canBeDisabled()
                                ->children()
                                    ->scalarNode('parameter_name')->defaultValue('version')->end()
                                ->end()
                            ->end()
                            ->arrayNode('custom_header')
                                ->canBeDisabled()
                                ->children()
                                    ->scalarNode('header_name')->defaultValue('X-Accept-Version')->end()
                                ->end()
                            ->end()
                            ->arrayNode('media_type')
                                ->canBeDisabled()
                                ->children()
                                    ->scalarNode('regex')->defaultValue('/(v|version)=(?P<version>[0-9\.]+)/')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('guessing_order')
                        ->defaultValue(['query', 'custom_header', 'media_type'])
                        ->validate()
                            ->ifTrue(function ($v) {
                                foreach ($v as $resolver) {
                                    if (!in_array($resolver, ['query', 'custom_header', 'media_type'])) {
                                        return true;
                                    }
                                }
                            })
                            ->thenInvalid('Versioning guessing order can only contain "query", "custom_header", "media_type".')
                        ->end()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
