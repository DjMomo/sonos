sonos-PHP-class
=================

A PHP class to control Sonos products

Forked from https://github.com/DjMomo/sonos
Fork / updates found at https://github.com/phil-lavin/sonos

New features of this fork
=========================

Code Refactors
--------------

* Replace all private with protected so the class can be reasonably extended
* Fix the redundant $next param in AddToQueue
* Tidy up inconsistent whitespace

Methods
-------

* static get_room_coordinator(string room_name) : Returns an instance of SonosPHPController representing the 'coordinator' of the specified room
* static detect(string ip,string port) : IP and port are optional. Returns an array of instances of SonosPHPController, one for each Sonos device found on the network
* get_coordinator() : Returns an instance of SonosPHPController representing the 'coordinator' of the room this device is in
* device_info() : Gets some info about this device as an array
* AddSpotifyToQueue(string spotify_id,bool next) : Adds the provided spotify ID to the queue either next or at the end

Original Changelog
==================

2013-06-08 - V1.0 - Initial version on Github
2013-06-16 - Bugs fixes and new features :
	- Say name song	
	- TTS messages can be greater than 100 car.

==========

Configuration :

-- None --

==========

How to use :

See sonos.php or sonos.tts.php
And http://www.planete-domotique.com/blog/2013/06/10/une-classe-php-pour-piloter-ses-sonos-avec-leedomus/
