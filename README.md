Eventbrite Connector
====================

Eventbrite Connector is a simple and easy to use PHP Library for integration
with Eventbrite API v3.

## Installation ##
Require the library via composer: `composer require bmeme/eventbrite_connector`

Install it by running `composer install`

And then add the autoloader file to your script:
```php
<?php require_once 'vendor/autoload.php'; ?>
```
And it's done! Congratulations!

## The Connector ##

> Consider that the Connector class, provides all the necessary methods to deal
with **"Personal Tokens"** or **"OAuth token flow"** access methods provided by
Eventbrite, but the implementation logic is demanded to you. 

Now that you've required the autoloader you can instantiate the Connector:
> For this example i'm in the **"Personal Tokens"** magic world.

    <?php $eb = new Connector('CLIENT_SECRET', 'CLIENT_ID', 'OAUTH_TOKEN'); ?>
    
Congratulations, this is your firs Connector! Take care of him.

## Has someone said "Entities"? ##
To send requests to Eventbrite API, the Connector must know of what are we
talking about:

 - Events
 - Users
 - Orders
 - ... (other)
 
Each of these points is an Eventbrite Entity type, identified by its own
EntityApiType, that defines the base path for its specific REST call
(eg: *events* for Event, *users* for User, etc...).

#### Entity C.R.U.D.: ####

###### Create ######

Is it possible to *create* new entities* via the static method 
**create()** for each "creatable" Entity class:
```php
<?php
/** @var \EventBriteConnector\Entity\Event $event */
$event = Event::create($eb, [
  'event.name.html' => 'My new awesome event',
  'event.start.utc' => '2016-10-12T14:00:00Z',
  'event.start.timezone' => 'Europe/Rome',
  'event.end.utc' => '2016-10-17T00:00:00Z',
  'event.end.timezone' => 'Europe/Rome',
  'event.currency' => 'EUR',
]);
?>
```
> \* Creatable entities are: *Event, Organizer, Series, Venues, Webhooks*.

In order to create *entity properties** you can use the Entity **saveProperty()**
method:

```php
<?php
/** @var \EventBriteConnector\Entity\Event $event */
$event->saveProperty('ticket_classes', [
  'ticket_class.name' => 'Early bird ticket',
  'ticket_class.description' => 'Special offer!',
  'ticket_class.quantity_total' => 10,
  'ticket_class.free' => TRUE
]);
?>
```
> \* Entity properties are all those endpoints defined with the following schema:
`/<entity_api_type>/:id/<property_name>/` Eg: `POST /events/:id/ticket_classes/`

###### Read ######

Entities can be *fetched* by using the Connector **fetch()** method:
```php
<?php $event = $eb->fetch('event', 1234567890); ?>
```

You can load a specific entity property, or filter property results by condition
criteria using the Entity **load()** method.
```php
<?php
$eb->fetch('user')
  ->load('organizers')
  ->load('owned_events', ['status' => 'live', 'order_by' => 'start_desc']);
?>
```
*Note: Is it possible to access last loaded data by using the Entity method 
**getActiveData()***

###### Update ######
To *update* an entity use the Entity **update()** method with an array of values
to be changed as argument.

```php
<?php
/** @var \EventBriteConnector\Entity\Event $event */
$event->update([
  "name" => [
    'html' => 'Api updated event',
  ]
]);
?>
```

###### Delete ######

Entities can be *deleted* by using the Entity **delete()** method:
```php
<?php 
/** @var \EventBriteConnector\Entity\Event $event */
$event->delete();
?>
```

### Media ###
You can use the **Media** Entity to **upload()** your files to Eventbrite.
```php
<?php
/** @var \EventBriteConnector\Entity\Media $media */
$media = new Media();
$media->setConnector($eb);
$media->upload('~/Pictures/test-image.jpg', Media::IMAGE_EVENT_LOGO, [
  'crop_mask.top_left.x' => 0,
  'crop_mask.top_left.y' => 0,
  'crop_mask.width' => 300,
  'crop_mask.height' => 200,
]);
?>
```
*Note: Crop mask parameter is optional.*

## E.B. phone home ##
So, now that you have understood the Entities concept and you have a Connector,
you are ready to use both of them for data retrieving.
You can use your Connector in a "*standard*" way or in a more "*fluent*" one. 

#### The Standard way ####
```php
<?php 

$conditions = array('status' => 'live', 'order_by' => 'start_desc');

$user = $eb->fetch('user')
  ->load('organizers')
  ->load('owned_events', $conditions);
    
$event = $eb->fetch('event', 123456789)
  ->load('attendees');
    
// And now just call the getData() method!
$me = $user->getData('me');
$organizers = $user->getData('organizers');
$key = $user->buildDataKey('owned_events', $conditions);
$owned_events = $user->getData($key);

$data = array(
  'ME' => $me,
  'ORGANIZERS' => $organizers,
  'OWNED_EVENTS' => $owned_events,
  'EVENT' => $event->getData(123456789),
  'ATTENDEES' => $event->getData('attendees'),
);

// Or if you are Fast & Furious, just call the getData method without params. 
$fnf_data = $user->getData();
?>
```

#### "Don't stop me now!" - AKA The Fluent way ####
```php
<?php 

$conditions = array('status' => 'live', 'order_by' => 'start_desc');

$eb->fetch('user')
  ->load('organizers')
  ->load('owned_events', $conditions)
  ->getConnector()
  ->fetch('event', 123456789)
  ->load('attendees');
    
$data = array(
  'user' => $eb->getEntity('user', 'me')->getData(),
  'event' => $eb->getEntity('event', 123456789)->getData(),
);
?>
```
