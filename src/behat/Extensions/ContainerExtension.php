<?php
/**
 * Copyright 2016-2017 Centreon
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
                ->scalarNode('log_directory')->defaultValue(sys_get_temp_dir())->end()
                ->scalarNode('web')->defaultValue('mon-web-dev.yml')->end()
                ->scalarNode('web_fresh')->defaultValue('mon-web-fresh-dev.yml')->end()
                ->scalarNode('web_widgets')->defaultValue('mon-web-widgets-dev.yml')->end()
                ->scalarNode('web_squid_simple')->defaultValue('mon-web-squid-simple-dev.yml')->end()
                ->scalarNode('web_squid_basic_auth')->defaultValue('mon-web-squid-basic-auth-dev.yml')->end()
                ->scalarNode('web_kb')->defaultValue('mon-web-kb-dev.yml')->end()
                ->scalarNode('web_openldap')->defaultValue('mon-web-openldap-dev.yml')->end()
                ->scalarNode('awie')->defaultValue('mon-awie-dev.yml')->end()
                ->scalarNode('ppm_squid_simple')->defaultValue('mon-ppm-squid-simple-dev.yml')->end()
                ->scalarNode('ppm_squid_basic_auth')->defaultValue('mon-ppm-squid-basic-auth-dev.yml')->end()
                ->scalarNode('lm_squid_simple')->defaultValue('mon-lm-squid-simple-dev.yml')->end()
                ->scalarNode('lm_squid_basic_auth')->defaultValue('mon-lm-squid-basic-auth-dev.yml')->end()
                ->scalarNode('web_influxdb')->defaultValue('mon-web-influxdb-dev.yml')->end()
                ->scalarNode('lm')->defaultValue('mon-lm-dev.yml')->end()
                ->scalarNode('poller-display')->defaultValue('mon-poller-display-dev.yml')->end()
                ->scalarNode('ppe')->defaultValue('mon-ppe-dev.yml')->end()
                ->scalarNode('ppm')->defaultValue('mon-ppm-dev.yml')->end()
                ->scalarNode('ppm_autodisco')->defaultValue('mon-ppm-autodisco-dev.yml')->end()
                ->scalarNode('ppm1')->defaultValue('mon-ppm1-dev.yml')->end()
                ->scalarNode('automation')->defaultValue('mon-automation-dev.yml')->end()
                ->scalarNode('middleware')->defaultValue('mon-middleware-dev.yml')->end()
                ->scalarNode('bam')->defaultValue('des-bam-dev.yml')->end()
                ->scalarNode('hub')->defaultValue('hub-dev.yml')->end()
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
