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

namespace Centreon\Test\Behat\Monitoring;

class MonitoringServicesPage
{
    private $ctx;
    private $properties = array(
        'host' => array(
            'td:nth-child(3)'
        ),
        'service' => array(
            'td:nth-child(4)'
        ),
        'status' => array(
            'td:nth-child(7)'
        ),
        'duration' => array(
            'td:nth-child(8)'
        ),
        'last_check' => array(
            'td:nth-child(9)'
        ),
        'tries' => array(
            'td:nth-child(10)'
        ),
        'status_information' => array(
            'td:nth-child(11)'
        )
    );
    
    public function __construct($context)
    {
        $this->ctx = $context;
    }
    
    /**
     * Set Service Status Filter to All
     */
    public function setFilterByAllService()
    {
        $this->listServices();
        $this->ctx->selectInList('select#statusService', 'All');
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
        $this->ctx->spin(
            function($context) {
                return $context->getSession()->getPage()->has('named', array('id_or_name', 'host_search'));
            }
        );
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
        // As we cannot use the page because of incompatibilites between
        // XSLT and PhantomJS, we will read the value directly in
        // database.
        $query = 'SELECT s.acknowledged AS acknowledged '
            . 'FROM services AS s '
            . 'LEFT JOIN hosts AS h ON s.host_id = h.host_id '
            . 'WHERE h.name=:hostname '
            . 'AND s.description=:description ';
        $stmt = $this->ctx->getStorageDatabase()->prepare($query);
        $stmt->execute(array(':hostname' => $hostname, ':description' => $servicename));
        $res = $stmt->fetch();
        if ($res === FALSE) {
            throw new \Exception('Cannot find service ' . $servicename . ' of host ' . $hostname . ' in database.');
        }
        return $res['acknowledged'];
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
        // The code below cannot work right now in the context of the
        // hosts monitoring page as PhantomJS does not support XSLT.
        // As a workaround will we use direct Ajax call to add the
        // acknowledgement.
        $this->ctx->visit(
            'include/monitoring/external_cmd/cmdPopup.php?cmd=70&comment='
            . $comment . '&sticky=' . ($isSticky ? 'true' : 'false')
            . '&persistent=' . ($isPersistent ? 'true' : 'false')
            . '&notify=' . $doNotify
            . '&ackhostservice=0&force_check=' . ($doForceCheck ? 'true' : 'false')
            . '&author=admin&select[' . $hostname . '%3B' . $service . ']=1');
        $this->listServices();
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
        $this->ctx->spin(
            function($context) {
                return $context->getSession()->getPage()->has('named', array('id', 'popupDowntime'));
            }
        );

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

    /**
      * Get the status of a service
      *
      * @param string hostName Host name to select
     *  @param string serviceDescription Service description to select
      */
    public function getStatus($hostName, $serviceDescription)
    {
        // We cannot direct web-interface search right now (XSLT is not
        // supported by PhantomJS). So we will look for the host status
        // in database.
        $query = 'SELECT s.state AS state FROM services AS s LEFT JOIN hosts AS h ON s.host_id=h.host_id WHERE h.name=:hostname AND s.description=:description';
        $stmt = $this->ctx->getStorageDatabase()->prepare($query);
        $stmt->execute(array(':hostname' => $hostName, ':description' => $serviceDescription));
        $res = $stmt->fetch();
        if ($res === FALSE) {
            throw new \Exception('Cannot get status of service ' . $serviceDescription . ' of host ' . $hostName);
        }
        switch ($res['state']) {
        case 0:
            $status = 'OK';
            break ;
        case 1:
            $status = 'WARNING';
            break ;
        case 2:
            $status = 'CRITICAL';
            break ;
        default:
            $status = 'UNKNOWN';
        }
        return $status;
    }
    
    /**
     * Get the Value of a field from a given host and service.
     * 
     * @param string $host
     * @param string $servicename
     * @param string $propertyfield , The value of the field to retrieve
     * @return string
     * @throws Exception
     */
    public function getPropertyFromAHostAndService($host, $servicename, $propertyfield)
    {   
        $this->setFilterByAllService();
        $this->ctx->assertFind('css','input#host_search')->setValue($host);
        $this->ctx->assertFind('css','input#input_search')->setValue($servicename);
        if (!array_key_exists($propertyfield, $this->properties)) {
            throw new Exception($propertyfield . ' property does not exist, please verify the name');
        }       
        $locator = $this->properties[$propertyfield];
        $propertyLocator = $locator[0];
        $table = $this->ctx->assertFind('css', 'table.ListTable');

        return $this->ctx->assertFindIn($table, 'css', 'tr#trStatus '. $propertyLocator)->getText();   
    }
    
}
