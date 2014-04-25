Eventbrite Connector
====================

Eventbrite Connector is a simple and easy to use PHP Library for integration with Eventbrite API v3.

## Installation ##
Just download the library and require the autoload.php file:

    <?php require_once 'somewhereoutinspace/eventbrite_connector/autoload.php'; ?>

Very well! I'm proud of you.

## The Connector ##

> Consider that the EventBriteConnector class, provides all the necessary methods to deal with **"Personal Tokens"** or **"OAuth token flow"** access methods provided by Eventbrite, but the implementation logic is demanded to you. 

Now that you've required the autoloader you can instantiate the Connector:
> For this example i'm in the **"Personal Tokens"** magic world.

    <?php $eb = new EventBriteConnector('CLIENT_SECRET', 'CLIENT_ID', 'OAUTH_TOKEN'); ?>
    
Congratulations, this is your firs Connector! Take care of him.

## Has someone said "Entities"? ##
To send requests to Eventbrite API, the Connector must know of what are we talking about:

 - Events
 - Users
 - Orders
 - ... (other)
 
Each of these points is an EventBriteEntity type, identified by its own EntityAPIType, that defines the base path for its specific REST call (eg: *events* for Event, *users* for User, etc...).
> Some informations:
> - $entity_id = 'me' by default for EventBriteUser.
> - You can load entity info, a specific entity property, or filter property results with condition criteria using the **load()** function.
> - Each entity type can redefine the load() function.
> - You can add/remove multiple entities to the Connector and jump from one to another as you wish.

That's all about Entities.
*- Benjamin Beauford "Bubba" Blue -*

## E.B. phone home ##
So, now that you have understood the Entities concept and you have a Connector, you are ready to use both of them for data retrieving.
You can use your Connector in a "*standard*" way or in a more "*fluent*" one. 

#### The Standard way ####

    <?php 
    $conditions = array('status' => 'live', 'order_by' => 'start_desc');
    
    $user = $eb->addEntity(new EventBriteUser())
        ->load()
        ->load('organizers')
        ->load('owned_events', $conditions);
        
    $event = $eb->addEntity(new EventBriteEvent(123456789))
        ->load()
        ->load('attendees')
        ->load('attendees/398989');
        
    // And now, to get the loaded data... just call the getData() method!
    $me = $user->getData('me');
    $organizers = $user->getData('organizers');
    $owned_events = $user->getData($user->buildDataKey('owned_events', $conditions));
    
    $data = array(
    'ME' => $me,
        'ORGANIZERS' => $organizers,
    'OWNED_EVENTS' => $owned_events
    );
    
    // Or if you are Fast & Furious, just call the getData method without params. 
    $fnf_data = $user->getData();

#### "Don't stop me now!" - AKA The Fluent way ####

    <?php
    $eb->addEntity(new EventBriteUser())
        ->load()
        ->load('organizers')
        ->load('owned_events', array('status' => 'live', 'order_by' => 'start_desc'))
        ->getConnector()
        ->addEntity(new EventBriteEvent(123456789))
        ->load()
        ->load('attendees')
        ->load('attendees/398989');
        
    $data = array(
    'user' => $eb->getEntity('users', 'me')->getData(),
        'event' => $eb->getEntity('events', 11197613363)->getData()
    );

Well, for more informations, just take a look to the code, and you'll see things you humans wouldn't believe.