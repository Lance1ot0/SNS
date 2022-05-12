<?php

function format_date($date) {
  $datetime = new DateTime($date);
  $datetime->setTimezone(new DateTimeZone('Europe/Paris'));

  return $datetime->format('j F \a\t H\hi');
}