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

/**
  *
  * The code of his class is based on class MonitoringServicesPage
  */
class MonitoringHostsPage extends \Centreon\Test\Behat\Page
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
      * Put max host display in hosts list to $limit
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
      * Wait host(s) list page
      */
    public function waitForHostListPage()
    {
        $this->ctx->spin(
            function($context) {
                return $context->getSession()->getPage()->has('named', array('id_or_name', 'host_search'));
            }
        );
    }

    public function listHosts()
    {
        // Go to : Monitoring > Status Details > Hosts
        $this->ctx->visit('/main.php?p=20202');

        $this->waitForHostListPage();
    }

    /**
      * Check if the host is acknowledged or not
      *
      * @param string hostname Hostname to check.
      * @return bool
      */
    public function isHostAcknowledged($hostname)
    {
        // As we cannot use the page because of incompatibilites between
        // XSLT and PhantomJS, we will read the value directly in
        // database.
        $query = 'SELECT acknowledged FROM hosts WHERE name=:name';
        $stmt = $this->ctx->getStorageDatabase()->prepare($query);
        $stmt->execute(array(':name' => $hostname));
        $res = $stmt->fetch();
        if ($res === FALSE) {
            throw new \Exception('Cannot find host ' . $hostname . ' in database.');
        }
        return $res['acknowledged'];
    }

    /**
      * Check if the host is in downtime or not
      *
      * @param string hostname Hostname to check.
      * @return bool
      */
    public function isHostInDowntime($hostname)
    {
        // Prepare (filter by hostname)
        $this->doActionOn($hostname);

        $page = $this->ctx->getSession()->getPage();
        $table = $page->find('css', '.ListTable');

        $linesWithACK = $table->findAll('xpath', "//img[contains(@name, 'popupForDowntime')]/../../..");

        if (count($linesWithACK)) {
            return true;
        }
        return false;

    }

    /**
      * Generic function for running an action on a host
      *
      * @param string hostname Hostname to select.
      * @param string action_label Label of action to select. Action by default is do no action.
      */
    public function doActionOn($hostname, $action_label = 'More actions...')
    {
        // Go to : Monitoring > Status Details > Hosts
        $this->listHosts();

        // Set the filter "Host Status" to "All" for display all hosts. Need to do this first !
        $page = $this->ctx->getSession()->getPage();
        $page->selectFieldOption('statusHost', "All");

        // Filter the host(s) list displayed by "Host" name
        $this->ctx->assertFind('named', array('id', 'host_search'))->setValue(trim($hostname));

        // Minor : Put max host display in hosts list to 100
        $this->setPageLimitTo("100");

        // Check the line about the host (hostname) -> check all for check the only line
        $checkbox = $this->ctx->assertFind('named', array('id_or_name', 'checkall'));
        $this->checkCheckbox($checkbox);

        // Select the action (by action label)
        $page->selectFieldOption('o1', $action_label);

        $this->waitForHostListPage();
    }

    /**
      * Acknowledge a host
      *
      * @param string hostname hostname to select.
      * @param string comment Comment about the acknowledge to add.
      * @param bool isSticky
      * @param bool doNotify
      * @param bool isPersistent
      * @param bool doAckServicesAttached Check the checkbox "Acknowledge services attached to hosts"
      * @param bool doForceCheck Check the checkbox "Force active checks"
      * @param string url
      */
    public function addAcknowledgementOnHost(
        $hostname,
        $comment,
        $isSticky,
        $doNotify,
        $isPersistent,
        $doAckServicesAttached,
        $doForceCheck,
        $url
    ) {
        // The code below cannot work right now in the context of the
        // hosts monitoring page as PhantomJS does not support XSLT.
        // As a workaround will we use direct Ajax call to add the
        // acknowledgement.

        $sessionId = $this->ctx->getSession()->getDriver()->getCookie('PHPSESSID');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $sessionId);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            array(
                'cmd' => 72,
                'comment' => $comment,
                'sticky' => ($isSticky ? 'true' : 'false'),
                'persistent' => ($isPersistent ? 'true' : 'false'),
                'notify' => $doNotify,
                'ackhostservice' => ($doAckServicesAttached ? 'true' : 'false'),
                'force_check' => ($doForceCheck ? 'true' : 'false'),
                'author' => 'admin',
                'resources' =>  json_encode([$hostname])
            ));
        curl_exec($ch);
        $this->listHosts();
    }

    /**
      * Disacknowledge on a host
      *
      * @param string hostname Hostname to select.
      */
    public function disacknowledgeOnHost($hostname)
    {
        $this->doActionOn($hostname, 'Hosts : Disacknowledge');
    }

    /**
      * Enable notification on a host
      *
      * @param string hostname Hostname to select.
      */
    public function enableNotificationOnHost($hostname)
    {
        $this->doActionOn($hostname, 'Hosts : Enable Notification');
    }

    /**
      * Disable notification on a host
      *
      * @param string hostname Hostname to select.
      */
    public function disableNotificationOnHost($hostname)
    {
        $this->doActionOn($hostname, 'Hosts : Disable Notification');
    }

    /**
      * Enable check on a host
      *
      * @param string hostname Hostname to select.
      */
    public function enableCheckOnHost($hostname)
    {
        $this->doActionOn($hostname, 'Hosts : Enable Check');
    }

    /**
      * Disable check on a host
      *
      * @param string hostname Hostname to select.
      */
    public function disableCheckOnHost($hostname)
    {
        $this->doActionOn($hostname, 'Hosts : Disable Check');
    }

    /**
      * Downtime a host
      *
      * @param string hostname Host name to select
      * @param bool isDurationFixed The duration is fixed or not.
      * @param string startTimeDate
      * @param string startTimeTime
      * @param string endTimeDate
      * @param string end_time_time
      * @param string duration Desired duration.
      * @param string duration_scale Unit of the duration.
      * @param string comment Comment to associate on the downtime
      * @param bool setDowntimesOnServicesAttached
      */
    public function addDowntimeOnHost($hostname, $isDurationFixed, $startTimeDate, $startTimeTime, $endTimeDate, $end_time_time, $duration, $duration_scale, $comment, $setDowntimesOnServicesAttached)
    {

        // Prepare the downtime of the host
        $this->doActionOn($hostname, 'Hosts : Set Downtime');

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
            $this->checkCheckbox($durationFixedCheckbox);
        } else {
            // Need uncheck duration checkbox before change duration text field
            $this->uncheckCheckbox($durationFixedCheckbox);

            // Configure text field "Duration"
            $this->ctx->assertFindIn($popinDowntime, 'named', array('id', 'duration'))->setValue($duration);

            // Configure dropdown field unity of "Duration"
            $popinDowntime->selectFieldOption('duration_scale', $duration_scale);
        }

        // Configure text in mandatory field "Comment"
        $this->ctx->assertFindIn($popinDowntime, 'named', array('id_or_name', 'comment'))->setValue($comment);

        // Configure "Set downtimes on services attached to hosts"
        $downtimesOnServicesAttachedCheckbox = $this->ctx->assertFindIn($popinDowntime, 'named', array('id', 'downtimehostservice'));

        if ($setDowntimesOnServicesAttached) {
            $this->checkCheckbox($downtimesOnServicesAttachedCheckbox);
        } else {
            $this->uncheckCheckbox($downtimesOnServicesAttachedCheckbox);
        }

        // Submit pop-in form with submit button "Set downtime"
        $this->ctx->assertFindButtonIn($popinDowntime, 'Set downtime')->click();

        // Page is refresh (by submit), need to wait
        $this->waitForHostListPage();

    }

    /**
      * Get the status of a host
      *
      * @param string hostName Host name to select
      */
    public function getStatus($hostName)
    {
        // We cannot direct web-interface search right now (XSLT is not
        // supported by PhantomJS). So we will look for the host status
        // in database.
        $query = 'SELECT state FROM hosts WHERE name=:name';
        $stmt = $this->ctx->getStorageDatabase()->prepare($query);
        $stmt->execute(array(':name' => $hostName));
        $res = $stmt->fetch();
        if ($res === FALSE) {
            throw new \Exception('Cannot get status of host ' . $hostName);
        }
        switch ($res['state']) {
        case 0:
            $status = 'UP';
            break ;
        case 1:
            $status = 'DOWN';
            break ;
        case 2:
            $status = 'UNREACHABLE';
            break ;
        default:
            $status = 'UNKNOWN';
        }
        return $status;
    }

}
