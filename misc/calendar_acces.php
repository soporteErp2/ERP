<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

$client = login(); /// crea la conexion

////////////////// EJEMPLOS DE USO DE LAS FUNCIONES////////////////////////


creaEvento($client, 'New Years Party',
    'Ring in the new year with Kim and I',
    'Our house',
    '2006-12-31', '22:00', '2007-01-01', '03:00', '-08' );


listado($client);
////////////////////////////////////////////////////////////////////////////////////

function login (){
	$user ="mail@plataforma.com.co"; 	/////
	$pass = "";							/////
	$domain= "plataforma.com.co";		/////
	$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar
	return $client = Zend_Gdata_ClientLogin::getHttpClient($user,$pass,$service);
	//echo $salida =$client->getAuthSubToken();
	//echo $salida =$client->getAuthSubPrivateKeyId();
	//$salida= $client->getClientLoginToken();
}

function listado($client)
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $eventFeed = $gdataCal->getCalendarEventFeed();
  echo "<ul>\n";
  foreach ($eventFeed as $event) {
    echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
    echo "\t\t<ul>\n";
    foreach ($event->when as $when) {
      echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
    }
    echo "\t\t</ul>\n";
    echo "\t</li>\n";
  }
  echo "</ul>\n";
}

function listadoByDateRange($client, $startDate='2007-05-01',
                                   $endDate='2007-08-01')
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $query = $gdataCal->newEventQuery();
  $query->setUser('default');
  $query->setVisibility('private');
  $query->setProjection('full');
  $query->setOrderby('starttime');
  $query->setStartMin($startDate);
  $query->setStartMax($endDate);
  $eventFeed = $gdataCal->getCalendarEventFeed($query);
  echo "<ul>\n";
  foreach ($eventFeed as $event) {
    echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
    echo "\t\t<ul>\n";
    foreach ($event->when as $when) {
      echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
    }
    echo "\t\t</ul>\n";
    echo "\t</li>\n";
  }
  echo "</ul>\n";
}

function listadoByFullTextQuery($client, $fullTextQuery='tennis')
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $query = $gdataCal->newEventQuery();
  $query->setUser('default');
  $query->setVisibility('private');
  $query->setProjection('full');
  $query->setQuery($fullTextQuery);
  $eventFeed = $gdataCal->getCalendarEventFeed($query);
  echo "<ul>\n";
  foreach ($eventFeed as $event) {
    echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
    echo "\t\t<ul>\n";
    foreach ($event->when as $when) {
      echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
      echo "\t\t</ul>\n";
      echo "\t</li>\n";
    }
  }
  echo ">/ul<\n";
}

function setReminder($client, $eventId, $minutes=15)
{
  $gc = new Zend_Gdata_Calendar($client);
  $method = "alert";
  if ($event = getEvent($client, $eventId)) {
    $times = $event->when;
    foreach ($times as $when) {
        $reminder = $gc->newReminder();
        $reminder->setMinutes($minutes);
        $reminder->setMethod($method);
        $when->reminder = array($reminder);
    }
    $eventNew = $event->save();
    return $eventNew;
  } else {
    return null;
  }
}

function creaEvento ($client, $title = 'Tennis with Beth',
    $desc='Meet for a quick lesson', $where = 'On the courts',
    $startDate = '2008-01-20', $startTime = '10:00',
    $endDate = '2008-01-20', $endTime = '11:00', $tzOffset = '-08')
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $newEvent = $gdataCal->newEventEntry();

  $newEvent->title = $gdataCal->newTitle($title);
  $newEvent->where = array($gdataCal->newWhere($where));
  $newEvent->content = $gdataCal->newContent("$desc");

  $when = $gdataCal->newWhen();
  $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
  $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
  $newEvent->when = array($when);

  // Upload the event to the calendar server
  // A copy of the event as it is recorded on the server is returned
  $createdEvent = $gdataCal->insertEvent($newEvent);
  return $createdEvent->id->text;
}
?>