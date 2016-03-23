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
 * Database extension for behat
 */
class DatabaseExtension implements ExtensionInterface
{
    const DATABASE_ID = 'database';
    
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition(
            'Centreon\Test\Behat\Initializer\DatabaseInitializer',
            array(
                '%database.parameters%'
            )
        );
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('database.container_initializer', $definition);
        
        $container->setParameter('database.parameters', $config);
    }
    
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultNull()->end()
                ->scalarNode('host')->defaultNull()->end()
                ->scalarNode('port')->defaultNull()->end()
                ->scalarNode('username')->defaultNull()->end()
                ->scalarNode('password')->defaultNull()->end()
                ->scalarNode('dbname')->defaultNull()->end()
            ->end()
        ->end();
    }
    
    public function getConfigKey()
    {
        return self::DATABASE_ID;
    }
    
    public function initialize(ExtensionManager $extensionManager)
    {
    }
    
    public function process(ContainerBuilder $container)
    {
    }
}