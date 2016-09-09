# Centreon Test Lib #

## Rationale ##

Centreon Web uses acceptance tests to ensure its software quality.
Testing is done with the help of Behat and this project contains
Behat-compliant classes used in many Centreon projects.

With this classes, PHP developers can mimic the interaction between
end-users and the application. The layers are a follow and should
explain more clearly of classes of Centreon Test Lib heavily differs
from standard Centreon classes.

| Layer           | Language           | Description                                 |
|-----------------|--------------------|---------------------------------------------|
| Acceptance Test | PHP                | This is where acceptance tests are written and where classes from this project comes in handy. These acceptance tests are run by Behat. |
| Behat           | PHP                | Behat run acceptance tests and provides reports. |
| PhantomJS       | C++ but irrelevant | PhantomJS is a headless browser, optimal for testing purposes. |
| Centreon        | PHP (web UI)       | A classical Centreon interface, with which monitoring is just plain fun. |

## Class naming ##

There should be one class per Centreon page. That is, as soon as you browse to a new
page of Centreon, a new class should be used to manipulate this page. These classes
should be named after the intent of the page, not after the menu from which they were
accessed. For exemple the service creation/edition page is named
ServiceConfigurationPage. The backup configuration page in the Administration menu
is named BackupConfigurationPage.

| Menu                                                        | Class name                           |
|-------------------------------------------------------------|--------------------------------------|
| Monitoring -> Downtimes -> Add                              | DowntimeConfigurationPage            |
| Configuration -> Hosts -> Hosts -> Add / Edit               | HostConfigurationPage                |
| Configuration -> Hosts -> Templates                         | HostTemplateConfigurationListingPage |
| Configuration -> Hosts -> Templates -> Add / Edit           | HostTemplateConfigurationPage        |
| Configuration -> Services -> Services by host -> Add / Edit | ServiceConfigurationPage             |
| Configuration -> Services -> Meta Services -> Add / Edit    | MetaServiceConfigurationPage         |
| Configuration -> Users -> Contacts / Users                  | ContactConfigurationListingPage      |
| Configuration -> Users -> Contacts / Users -> Add / Edit    | ContactConfigurationPage             |
| Configuration -> Commands -> Checks                         | CommandConfigurationListingPage      |
| Configuration -> Commands -> Checks -> Add / Edit           | CommandConfigurationPage             |
| Configuration -> Commands -> Notifications                  | CommandConfigurationListingPage      |
| Configuration -> Commands -> Notifications -> Add / Edit    | CommandConfigurationPage             |
| Configuration -> Commands -> Discovery                      | CommandConfigurationListingPage      |
| Configuration -> Commands -> Discovery -> Add / Edit        | CommandConfigurationPage             |
| Configuration -> Commands -> Miscellaneous                  | CommandConfigurationListingPage      |
| Configuration -> Commands -> Miscellaneous -> Add / Edit    | CommandConfigurationPage             |
| Administration -> Parameters -> Backup                      | BackupConfigurationPage              |

## Class methods ##

As a rule of thumb methods should be kept short and perform as few actions as
possible. It is always possible to add helper functions that perform more heavy
processing but they should be *really* helpful.

### Constructor ###

The constructor of a page must let users choose whether they wish to navigate
to the requested page or not. The rationale behind this is that the same class
should be used when navigating directly to a specific page or when instantiating
the class after the page was already loaded (link clicked in another page for
example).

In most simple cases, navigation will be controlled by a single boolean
argument. For example here is the constructor of the BackupConfigurationPage.

```php
public function __construct($context, $visit = true)
```

When pages are more specific (they apply to a single service for example),
the constructor should allow navigation to the page anyway. Here is the
constructor of the ServiceMonitoringDetailsPage.

```php
public function __construct($context, $host = '', $service = '')
```

In all cases, the constructor should check for page validity by using
isPageValid(). It should throw if the page is not valid.

### isPageValid() ###

This method should check for the validity of the current page relative to
the class. It should return a boolean indicating whether or not the
current page can be manipulated by this class.

## Common interfaces ##

### Page ###

```php
interface Page
{
    public function isPageValid();
}
```

### ConfigurationPage ###

```php
interface ConfigurationPage extends Page
{
    public function getProperties();
    public function setProperties($properties);
    public function save();
}
```

### ListingPage ###

```php
interface ListingPage extends Page
{
    public function getEntries();
}
```
