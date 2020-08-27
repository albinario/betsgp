<?php
// Sets title of Event
$eventTitle = 'Bet SGP 2020';
$siteURL = 'http://betsgp.com/';

//Sets the time zone, returns a string:
function timeZone () {
  return 'Europe/Copenhagen';
}
date_default_timezone_set(timeZone());

//Sets the start date, returns a string:
function startDate($connect) {
  return getItem('startDate', 'gps', 'id', 1, $connect);
}

//Sets the start time, returns a string:
function startTime($connect) {
  return getItem('startTime', 'gps', 'id', 1, $connect);
}

//Checks current date, returns a string:
function dateNow() {
  return date('Y-m-d');
}

//Checks current time, returns a string:
function timeNow() {
  return date('H:i:s');
}
