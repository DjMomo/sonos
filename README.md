sonos-PHP-class
=================

A PHP class to control Sonos products

Methods
-------

* static get_room_coordinator(string room_name) : Returns an instance of SonosPHPController representing the 'coordinator' of the specified room
* static detect(string ip,string port) : IP and port are optional. Returns an array of instances of SonosPHPController, one for each Sonos device found on the network
* get_coordinator() : Returns an instance of SonosPHPController representing the 'coordinator' of the room this device is in
* device_info() : Gets some info about this device as an array
* AddSpotifyToQueue(string spotify_id,bool next) : Adds the provided spotify ID to the queue either next or at the end
* Play() : play
* Pause() : pause
* Stop() : stop
* Next() : next track
* Previous() : previous track
* SeekTime(string) : seek to time xx:xx:xx
* ChangeTrack(int) : change to track xx
* RestartTrack() : restart actual track
* RestartQueue() : restart queue
* GetVolume() : get volume level
* SetVolume(int) : set volume level
* GetMute() : get mute status
* SetMute(bool) : active-disable mute
* GetTransportInfo() : get status about player
* GetMediaInfo() : get informations about media
* GetPositionInfo() : get some informations about track
* AddURIToQueue(string,bool) : add a track to queue
* RemoveTrackFromQueue(int) : remove a track from Queue
* RemoveAllTracksFromQueue() : remove all tracks from queue
* RefreshShareIndex() : refresh music library
* SetQueue(string) : load a track or radio in player
* PlayTTS(string message,string station,int volume,string lang) : play a text-to-speech message

How to use
==========

See sonos.php or sonos.tts.php
And http://www.planete-domotique.com/blog/2013/06/10/une-classe-php-pour-piloter-ses-sonos-avec-leedomus/

License
=======
This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/.
