<?php

/**
 * Copyright 2016-2021 Centreon
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

/**
 * Container extension for Behat.
 */
class ContainerExtension implements ExtensionInterface
{
    const CONTAINER_ID = 'container';

    public function load(ContainerBuilder $container, array $config): void
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

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('log_directory')->defaultValue(sys_get_temp_dir())->end()
                ->scalarNode('docker_compose_web')->defaultValue('docker-compose.yml')->end()
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
