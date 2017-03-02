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

namespace Centreon\Test\Behat\Administration;

class BackupConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const BACKUP_TYPE_DUMP = 0;
    const BACKUP_TYPE_LVM = 1;

    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;
    const DAY_SUNDAY = 0;

    protected $context;

    private static $properties = array(
        'enabled' => array(
            'radio',
            'input[name="backup_enabled[backup_enabled]"]'
        ),
        'backup_directory' => array(
            'text',
            'input[name="backup_backup_directory"]'
        ),
        'temp_directory' => array(
            'text',
            'input[name="backup_tmp_directory"]'
        ),
        'backup_centreon_db' => array(
            'checkbox',
            'input[name="backup_database_centreon"]'
        ),
        'backup_centreon_storage_db' => array(
            'checkbox',
            'input[name="backup_database_centreon_storage"]'
        ),
        'backup_type' => array(
            'radio',
            'input[name="backup_database_type[backup_database_type]"]'
        ),
        'full_backup_days' => array(
            'custom',
            'FullBackupDays'
        ),
        'partial_backup_days' => array(
            'custom',
            'PartialBackupDays'
        ),
        'backup_retention' => array(
            'text',
            'input[name="backup_retention"]'
        ),
        'backup_configuration_files' => array(
            'checkbox',
            'input[name="backup_configuration_files"]'
        ),
        'mysql_configuration_file' => array(
            'text',
            'input[name="backup_mysql_conf"]'
        ),
        'zend_configuration_file' => array(
            'text',
            'input[name="backup_zend_conf"]'
        )
    );

    /**
     *  Navigate to and/or check that we are on the backup
     *  configuration page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to the backup configuration
     *                   page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50165&o=backup');
        }

        // Check that page is valid for this class.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that the current page is matching this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="backup_enabled[backup_enabled]"]');
    }

    /**
     *  Get backup configuration properties.
     *
     *  @return An array of the backup configuration properties.
     */
    public function getProperties()
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }

    /**
     *  Set backup configuration properties.
     *
     *  @param $properties  Backup configuration properties.
     */
    public function setProperties($properties)
    {
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown backup property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = self::$properties[$property][0];
            $propertyLocator = self::$properties[$property][1];

            // Set property with its value.
            switch ($propertyType) {
                case 'checkbox':
                    if ($value) {
                        $this->context->assertFind('css', $propertyLocator)->check();
                    } else {
                        $this->context->assertFind('css', $propertyLocator)->uncheck();
                    }
                    break ;
                case 'custom':
                    $method = 'set' . $propertyLocator;
                    $this->$method($value);
                    break ;
                case 'radio':
                    $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                    break ;
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break ;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType .
                        ' found while configuring backup property ' .
                        $property . '.'
                    );
            }
        }
    }

    /**
     *  Save backup configuration form.
     */
    public function save()
    {
        $this->context->assertFindButton('submitC')->click();
    }

    /**
     *  Set full backup days.
     *
     *  @param $days  Array of days.
     */
    public function setFullBackupDays($days)
    {
        for ($i = 0; $i < 7; ++$i) {
            $this->context->assertFind('css', 'input[name="backup_database_full[' . $i . ']"]')->uncheck();
        }
        foreach ($days as $day) {
            $this->context->assertFind('css', 'input[name="backup_database_full[' . $day . ']"]')->check();
        }
    }

    /**
     *  Set partial backup days.
     *
     *  @param $days  Array of days.
     */
    public function setPartialBackupDays($days)
    {
        for ($i = 0; $i < 7; ++$i) {
            $this->context->assertFind('css', 'input[name="backup_database_partial[' . $i . ']"]')->uncheck();
        }
        foreach ($days as $day) {
            $this->context->assertFind('css', 'input[name="backup_database_partial[' . $day . ']"]')->check();
        }
    }
}
