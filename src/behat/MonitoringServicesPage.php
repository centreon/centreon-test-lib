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
namespace Centreon\Test\Behat;

class MonitoringServicesPage
{
    private $ctx;

    public function __construct($context)
    {
        $this->ctx = $context;
    }

    /**
      * Set the filter hostname
      *
      * @param string hostname Hostame to select.
      */
    public function setFilterbyHostname($hostname) {
        $this->ctx->assertFind('named', array('id', 'host_search'))->setValue(trim($hostname));
    }

    /**
     * Set the filter service
     *
     * @param string servicename Service to select.
     */
    public function setFilterbyService($servicename) {
        $this->ctx->assertFind('named', array('id', 'input_search'))->setValue(trim($servicename));
    }

    /**
      * Put max service display in services list to $limit
      *
      * @param string limit The value of limit in page limit dropdown
      */
    public function setPageLimitTo($limit)
    {
        $page = $this->ctx->getSession()->getPage();

        $toolbar_pagelimit = $page->find('css', '.Toolbar_pagelimit');
        $toolbar_pagelimit->selectFieldOption('l', $limit);
    }

    /**
      * Wait service list page
      */
    public function waitForServiceListPage()
    {
        $this->ctx->spin(function($context) {
            return $context->getSession()->getPage()->has('named', array('id_or_name', 'host_search'));
        });
    }

    public function listServices()
    {
        // Go to : Monitoring > Status Details > Services
        $this->ctx->visit('/main.php?p=20201');

        $this->waitForServiceListPage();
    }

    /**
      * Check if the service is acknowledged or not
      *
      * @param string hostname Hostname to check.
      * @param string servicename Service to check.
      * @return bool 
      */
    public function isServiceAcknowledged($hostname, $servicename)
    {
        // Prepare (filter by hostname and service name)
        $this->doActionOn($hostname, $servicename);

        $page = $this->ctx->getSession()->getPage();
        $table = $page->find('css', '.ListTable');

        $linesWithACK = $table->findAll('xpath', "//img[contains(@name, 'popupForAck')]/../../..");

        if (count($linesWithACK)) {
            return true;
        }
        return false;
    }

    /**
      * Check if the service is in downtime or not
      * 
      * @param string hostname Hostname to check.
      * @param string servicename Service to check.
      * @return bool 
      */
    public function isServiceInDowntime($hostname, $servicename)
    {
        // Prepare (filter by hostname and service name)
        $this->doActionOn($hostname, $servicename);

        $page = $this->ctx->getSession()->getPage();
        $table = $page->find('css', '.ListTable');

        $linesWithACK = $table->findAll('xpath', "//img[contains(@name, 'popupForDowntime')]/../../..");

        if (count($linesWithACK)) {
            return true;
        }
        return false;

    }

    /**
      * Generic function for running an action on a hostname or on a service
      *
      * @param string hostname Hostname to select.
      * @param string servicename Service to select.
      * @param string action_label Label of action to select. Action by default is do no action.
      */
    public function doActionOn($hostname, $servicename, $action_label = 'More actions...')
    {
        // Go to : Monitoring > Status Details > Services
        $this->listServices();

        // Set the filter "Service Status" to "All" for display all services. Need to do this first !
        $page = $this->ctx->getSession()->getPage();
        $page->selectFieldOption('statusService', "All");

        // Filter the services list displayed by "Host" name
        $this->ctx->assertFind('named', array('id', 'host_search'))->setValue(trim($hostname));

        // Filter the services list displayed by "Service" name
        $this->ctx->assertFind('named', array('id', 'input_search'))->setValue(trim($servicename));

        // Minor : Put max service display in services list to 100
        $this->setPageLimitTo("100");

        // Check the line about the service (hostname, service) -> check all for check the only line
        $this->ctx->assertFind('named', array('id_or_name', 'checkall'))->check();

        // Select the action (by action label)
        $page->selectFieldOption('o1', $action_label);

        $this->waitForServiceListPage();
    }

    /**
      * Schedule immediate check on a service
      *
      * @param string hostname Hostname to select.
      * @param string service Service to select.
      */
    public function scheduleImmediateCheckOnService($hostname, $service)
    {
        $this->doActionOn($hostname, $service, 'Schedule immediate check');
    }

    /**
     * Schedule immediate check (forced) on a service
     *
     * @param string hostname Hostname to select.
     * @param string service Service to select.
     */
    public function scheduleImmediateCheckForcedOnService($hostname, $service)
    {
        $this->doActionOn($hostname, $service, 'Schedule immediate check (Forced)');
    }

    /**
      * Acknowledge a service
      *
      * @param string hostname hostname to select.
      * @param string service Service to select.
      * @param string comment Comment about the acknowledge to add.
      * @param bool isSticky
      * @param bool doNotify
      * @param bool isPersistent
      * @param bool doForceCheck
      */
    public function addAcknowledgementOnService($hostname, $service, $comment, $isSticky, $doNotify, $isPersistent, $doForceCheck)
    {
        // Check the mandatory value "Comment"
        if (empty($comment)) {
            throw new \Exception("Comment is a mandatory field, need to be not empty.");
        }

        $this->doActionOn($hostname, $service, 'Services : Acknowledge');

        // When I have a pop-in "Set downtimes"
        $this->ctx->spin(function($context) {
            return $context->getSession()->getPage()->has('named', array('id_or_name', 'popupDowntime'));
        });

        // Search in pop-in "Acknowledge problems"
        $popinACK = $this->ctx->assertFind('named', array('id', 'popupAcknowledgement'));

        // Configure the checkbox "Sticky" field
        $stickyCheckbox = $this->ctx->assertFindIn($popinACK, 'named', array('id', 'sticky'));

        if ($isSticky) {
            $stickyCheckbox->check();
        } else {
            $stickyCheckbox->uncheck();
        }

        // Configure the checkbox "Notify" field
        $notifyCheckbox = $this->ctx->assertFindIn($popinACK, 'named', array('id', 'notify'));

        if ($doNotify) {
            $notifyCheckbox->check();
        } else {
            $notifyCheckbox->uncheck();
        }

        // Configure the checkbox "Persistent" field
        $persistentCheckbox = $this->ctx->assertFindIn($popinACK, 'named', array('id', 'persistent'));

        if ($isPersistent) {
            $persistentCheckbox->check();
        } else {
            $persistentCheckbox->uncheck();
        }

        // Configure the (mandatory) field "Comment" textarea
        $this->ctx->assertFindIn($popinACK, 'named', array('id', 'popupComment'))->setValue($comment);

        // Configure the checkbox "Force active checks" field
        $forceCheckCheckbox = $this->ctx->assertFindIn($popinACK, 'named', array('id', 'force_check'));

        if ($doForceCheck) {
            $forceCheckCheckbox->check();
        } else {
            $forceCheckCheckbox->uncheck();
        }

        // Submit pop-in form with submit button "Acknowledge selected problems"
        $this->assertFindButtonIn($popinACK, 'Acknowledge selected problems')->click();

    }

    /**
      * Disacknowledge on a service
      *
      * @param string hostname Hostname to select.
      * @param string service Service to select.
      */
    public function disacknowledgeOnService($hostname, $service)
    {
        $this->doActionOn($hostname, $service, 'Services : Disacknowledge');
    }

    /**
      * Enable notification on a service
      *
      * @param string hostname Hostname to select.
      * @param string service Service to select.
      */
    public function enableNotificationOnService($hostname, $service)
    {
        $this->doActionOn($hostname, $service, 'Services : Enable Notification');
    }

    /**
      * Disable notification on a service
      *
      * @param string hostname Hostname to select.
      * @param string service Service to select.
      */
    public function disableNotificationOnService($hostname, $service)
    {
        $this->doActionOn($hostname, $service, 'Services : Disable Notification');
    }

    /**
      * Enable check on a service
      *
      * @param string hostname Hostname to select.
      * @param string service Service to select.
      */
    public function enableCheckOnService($hostname, $service)
    {
        $this->doActionOn($hostname, $service, 'Services : Enable Check');
    }

    /**
      * Disable check on a service
      *
      * @param string hostname Hostname to select.
      * @param string service Service to select.
      */
    public function disableCheckOnService($hostname, $service)
    {
        $this->doActionOn($hostname, $service, 'Services : Disable Check');
    }

    /**
      * Downtime a service
      *
      * @param string hostname Host name to select
      * @param string servicename Service name to select
      * @param bool isDurationFixed The duration is fixed or not.
      * @param string startTimeDate 
      * @param string startTimeTime
      * @param string endTimeDate 
      * @param string end_time_time 
      * @param string duration Desired duration.
      * @param string duration_scale Unit of the duration.
      * @param string comment Comment to associate on the downtime
      */
    public function addDowntimeOnService($hostname, $servicename, $isDurationFixed, $startTimeDate, $startTimeTime, $endTimeDate, $end_time_time, $duration, $duration_scale, $comment)
    {

        // Prepare the downtime of the service (of the hostname) 
        $this->doActionOn($hostname, $servicename, 'Services : Set Downtime');

        // When I have a pop-in "Set downtimes"
        $this->ctx->spin(function($context) {
            return $context->getSession()->getPage()->has('named', array('id', 'popupDowntime'));
        });

        // Search in pop-in only
        $popinDowntime = $this->ctx->assertFind('named', array('id', 'popupDowntime'));

        /* Configure "Start Time" line */
      
        // Configure first field of "Start Time" line, format : 05/19/2016
        $this->ctx->assertFindIn($popinDowntime, 'named', array('id_or_name', 'start'))->setValue($startTimeDate);

        // Configure second field of "Start Time" line, format : 10:37
        $this->ctx->assertFindIn($popinDowntime, 'named', array('id_or_name', 'start_time'))->setValue($startTimeTime);

        /* Configure the "End Time" line */

        // Configure first field of "Start Time" line, format : 05/19/2017
        $this->ctx->assertFindIn($popinDowntime, 'named', array('id_or_name', 'end'))->setValue($endTimeDate);

        // Configure second field of "Start Time" line, format : 21:37
        $this->ctx->assertFindIn($popinDowntime, 'named', array('id_or_name', 'end_time'))->setValue($end_time_time);

        $durationFixedCheckbox = $this->ctx->assertFindIn($popinDowntime, 'named', array('id', 'fixed'));

        if ($isDurationFixed) {
            // Configure checkbox "Fixed"
            $durationFixedCheckbox->check();
        } else {
            // Need uncheck duration checkbox before change duration text field
            $durationFixedCheckbox->uncheck();

            // Configure text field "Duration"
            $this->ctx->assertFindIn($popinDowntime, 'named', array('id', 'duration'))->setValue($duration);

            // Configure dropdown field unity of "Duration"
            $popinDowntime->selectFieldOption('duration_scale', $duration_scale);
        }

        // Configure text in mandatory field "Comment"
        $this->ctx->assertFindIn($popinDowntime, 'named', array('id_or_name', 'comment'))->setValue($comment);

        // Submit pop-in form with submit button "Set downtime"
        $this->assertFindButtonIn($popinDowntime, 'Set downtime')->click();

        // Page is refresh (by submit), need to wait
        $this->waitForServiceListPage();

    }
    
}
