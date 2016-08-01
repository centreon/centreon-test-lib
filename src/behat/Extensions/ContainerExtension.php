<?php
/**
 * Copyright 2016 Centreon
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Centreon\Test\Behat\Extensions;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Container extension for Behat.
 */
class ContainerExtension implements ExtensionInterface
{
    const CONTAINER_ID = 'container';

    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition(
            'Centreon\Test\Behat\Initializer\ContainerInitializer',
            array(
                '%container.parameters%'
            )
        );
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('container.container_initializer', $definition);
        $container->setParameter('container.parameters', $config);
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('web')->defaultValue('mon-web-dev.yml')->end()
                ->scalarNode('web_fresh')->defaultValue('mon-web-fresh-dev.yml')->end()
                ->scalarNode('lm')->defaultValue('mon-lm-dev.yml')->end()
                ->scalarNode('ppe')->defaultValue('mon-ppe-dev.yml')->end()
                ->scalarNode('ppe1')->defaultValue('mon-ppe1-dev.yml')->end()
                ->scalarNode('ppm')->defaultValue('mon-ppm-dev.yml')->end()
                ->scalarNode('middleware')->defaultValue('mon-middleware-dev.yml')->end()
                ->scalarNode('kb')->defaultValue('mon-kb-dev.yml')->end()
                ->scalarNode('bam')->defaultValue('des-bam-dev.yml')->end()
            ->end()
        ->end();
    }

    public function getConfigKey()
    {
        return self::CONTAINER_ID;
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function process(ContainerBuilder $container)
    {
    }
}

?>
