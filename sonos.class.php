<?php
/**
  * PHP class to control Sonos
  * Forked from http://www.github.com/DjMomo/sonos
  * Forked repo / updates from https://github.com/phil-lavin/sonos
  *
  * Available functions :
  * - Play() : play / lecture
  * - Pause() : pause
  * - Stop() : stop
  * - Next() : next track / titre suivant
  * - Previous() : previous track / titre précédent
  * - SeekTime(string) : seek to time xx:xx:xx / avancer-reculer à la position xx:xx:xx
  * - ChangeTrack(int) : change to track xx / aller au titre xx
  * - RestartTrack() : restart actual track / revenir au début du titre actuel
  * - RestartQueue() : restart queue / revenir au début de la liste actuelle
  * - GetVolume() : get volume level / récupérer le niveau sonore actuel
  * - SetVolume(int) : set volume level / régler le niveau sonore
  * - GetMute() : get mute status / connaitre l'état de la sourdine
  * - SetMute(bool) : active-disable mute / activer-désactiver la sourdine
  * - GetTransportInfo() : get status about player / connaitre l'état de la lecture
  * - GetMediaInfo() : get informations about media / connaitre des informations sur le média
  * - GetPositionInfo() : get some informations about track / connaitre des informations sur le titre
  * - AddURIToQueue(string,bool) : add a track to queue / ajouter un titre à la liste de lecture
  * - RemoveTrackFromQueue(int) : remove a track from Queue / supprimer un tritre de la liste de lecture
  * - RemoveAllTracksFromQueue() : remove all tracks from queue / vider la liste de lecture
  * - RefreshShareIndex() : refresh music library / rafraichit la bibliothèque musicale
  * - SetQueue(string) : load a track or radio in player / charge un titre ou une radio dans le lecteur
  * - PlayTTS(string message,string station,int volume,string lang) : play a text-to-speech message / lit un message texte
  *
  * Functions only available in the fork:
  * - static get_room_coordinator(string room_name) : Returns an instance of SonosPHPController representing the 'coordinator' of the specified room
  * - static detect(string ip,string port) : IP and port are optional. Returns an array of instances of SonosPHPController, one for each Sonos device found on the network
  * - get_coordinator() : Returns an instance of SonosPHPController representing the 'coordinator' of the room this device is in
  * - device_info() : Gets some info about this device as an array
  * - AddSpotifyToQueue(string spotify_id,bool next) : Adds the provided spotify ID to the queue either next or at the end
*/

class SonosPHPController
{
	protected $Sonos_IP;
	protected $_raw = [];

	/**
	* Constructeur
	* @param string Sonos IP adress
	* @param string Sonos port (optional)
	*/
	public function __construct($Sonos_IP,$Sonos_Port = '1400')
	{
		// On assigne les paramètres aux variables d'instance.
		$this->IP = $Sonos_IP;
		$this->PORT = $Sonos_Port;
	}

	protected function Upnp($url,$SOAP_service,$SOAP_action,$SOAP_arguments = '',$XML_filter = '')
	{
		$POST_xml = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">';
		$POST_xml .= '<s:Body>';
		$POST_xml .= '<u:'.$SOAP_action.' xmlns:u="'.$SOAP_service.'">';
		$POST_xml .= $SOAP_arguments;
		$POST_xml .= '</u:'.$SOAP_action.'>';
		$POST_xml .= '</s:Body>';
		$POST_xml .= '</s:Envelope>';

		$POST_url = $this->IP.":".$this->PORT.$url;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_URL, $POST_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "SOAPAction: ".$SOAP_service."#".$SOAP_action));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $POST_xml);
		$r = curl_exec($ch);
		curl_close($ch);

		if ($XML_filter != '')
			return $this->Filter($r,$XML_filter);
		else
			return $r;
	}

	protected function Filter($subject,$pattern)
	{
		preg_match('/\<'.$pattern.'\>(.+)\<\/'.$pattern.'\>/',$subject,$matches); ///'/\<'.$pattern.'\>(.+)\<\/'.$pattern.'\>/'
		return $matches[1];
	}

	/**
	* Play
	*/
	public function Play()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'Play';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID><Speed>1</Speed>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Pause
	*/
	public function Pause()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'Pause';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Stop
	*/
	public function Stop()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'Stop';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Next
	*/
	public function Next()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'Next';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Previous
	*/
	public function Previous()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'Previous';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Seek to position xx:xx:xx or track number x
	* @param string 'REL_TIME' for time position (xx:xx:xx) or 'TRACK_NR' for track in actual queue
	* @param string
	*/
	public function Seek($type,$position)
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'Seek';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID><Unit>'.$type.'</Unit><Target>'.$position.'</Target>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Seek to time xx:xx:xx
	*/
	public function SeekTime($time)
	{
		return $this->Seek("REL_TIME",$time);
	}

	/**
	* Change to track number
	*/
	public function ChangeTrack($number)
	{
		return $this->Seek("TRACK_NR",$number);
	}

	/**
	* Restart actual track
	*/
	public function RestartTrack()
	{
		return $this->Seek("REL_TIME","00:00:00");
	}

	/**
	* Restart actual queue
	*/
	public function RestartQueue()
	{
		return $this->Seek("TRACK_NR","1");
	}

	/**
	* Get volume value (0-100)
	*/
	public function GetVolume()
	{
		$url = '/MediaRenderer/RenderingControl/Control';
		$action = 'GetVolume';
		$service = 'urn:schemas-upnp-org:service:RenderingControl:1';
		$args = '<InstanceID>0</InstanceID><Channel>Master</Channel>';
		$filter = 'CurrentVolume';
		return $this->Upnp($url,$service,$action,$args,$filter);
	}

	/**
	* Set volume value (0-100)
	*/
	public function SetVolume($volume)
	{
		$url = '/MediaRenderer/RenderingControl/Control';
		$action = 'SetVolume';
		$service = 'urn:schemas-upnp-org:service:RenderingControl:1';
		$args = '<InstanceID>0</InstanceID><Channel>Master</Channel><DesiredVolume>'.$volume.'</DesiredVolume>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Get mute status
	*/
	public function GetMute()
	{
		$url = '/MediaRenderer/RenderingControl/Control';
		$action = 'GetMute';
		$service = 'urn:schemas-upnp-org:service:RenderingControl:1';
		$args = '<InstanceID>0</InstanceID><Channel>Master</Channel>';
		$filter = 'CurrentMute';
		return $this->Upnp($url,$service,$action,$args,$filter);
	}

	/**
	* Set mute
	* @param integer mute active=1
	*/
	public function SetMute($mute = 0)
	{
		$url = '/MediaRenderer/RenderingControl/Control';
		$action = 'SetMute';
		$service = 'urn:schemas-upnp-org:service:RenderingControl:1';
		$args = '<InstanceID>0</InstanceID><Channel>Master</Channel><DesiredMute>'.$mute.'</DesiredMute>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Get Transport Info : get status about player
	*/
	public function GetTransportInfo()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'GetTransportInfo';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		$filter = 'CurrentTransportState';
		return $this->Upnp($url,$service,$action,$args,$filter);
	}

	/**
	* Get Media Info : get informations about media
	*/
	public function GetMediaInfo()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'GetMediaInfo';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		$filter = 'CurrentURI';
		return $this->Upnp($url,$service,$action,$args,$filter);
	}

	/**
	* Get Position Info : get some informations about track
	*/
	public function GetPositionInfo()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'GetPositionInfo';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		$xml = $this->Upnp($url,$service,$action,$args);

		$data["TrackNumberInQueue"] = $this->Filter($xml,"Track");
		$data["TrackURI"] = $this->Filter($xml,"TrackURI");
		$data["TrackDuration"] = $this->Filter($xml,"TrackDuration");
		$data["RelTime"] = $this->Filter($xml,"RelTime");
		$TrackMetaData = $this->Filter($xml,"TrackMetaData");

		$xml = substr($xml, stripos($TrackMetaData, '&lt;'));
		$xml = substr($xml, 0, strrpos($xml, '&gt;') + 4);
		$xml = str_replace(array("&lt;", "&gt;", "&quot;", "&amp;", "%3a", "%2f", "%25"), array("<", ">", "\"", "&", ":", "/", "%"), $xml);

		$data["Title"] = $this->Filter($xml,"dc:title");	// Track Title
		$data["AlbumArtist"] = $this->Filter($xml,"r:albumArtist");		// Album Artist
		$data["Album"] = $this->Filter($xml,"upnp:album");		// Album Title
		$data["TitleArtist"] = $this->Filter($xml,"dc:creator");	// Track Artist

		return $data;
	}

	/**
	* Add URI to Queue
	* @param string track/radio URI
	* @param bool added next (=1) or end queue (=0)
	*/
	public function AddURIToQueue($URI,$next=0)
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'AddURIToQueue';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$next = (int)$next;
		$args = '<InstanceID>0</InstanceID><EnqueuedURI>'.$URI.'</EnqueuedURI><EnqueuedURIMetaData></EnqueuedURIMetaData><DesiredFirstTrackNumberEnqueued>0</DesiredFirstTrackNumberEnqueued><EnqueueAsNext>'.$next.'</EnqueueAsNext>';
		$filter = 'FirstTrackNumberEnqueued';
		return $this->Upnp($url,$service,$action,$args,$filter);
	}

	/**
	* Remove a track from Queue
	*
	*/
	public function RemoveTrackFromQueue($tracknumber)
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'RemoveTrackFromQueue';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID><ObjectID>Q:0/'.$tracknumber.'</ObjectID>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Clear Queue
	*
	*/
	public function RemoveAllTracksFromQueue()
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'RemoveAllTracksFromQueue';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Set Queue
	* @param string URI of new track
	*/
	public function SetQueue($URI)
	{
		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'SetAVTransportURI';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$args = '<InstanceID>0</InstanceID><CurrentURI>'.$URI.'</CurrentURI><CurrentURIMetaData></CurrentURIMetaData>';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Refresh music library
	*
	*/
	public function RefreshShareIndex()
	{
		$url = '/MediaServer/ContentDirectory/Control';
		$action = 'RefreshShareIndex';
		$service = 'urn:schemas-upnp-org:service:ContentDirectory:1';
		return $this->Upnp($url,$service,$action,$args);
	}

	/**
	* Split string in several strings
	*
	*/
	protected function CutString($string,$intmax)
	{
		$i = 0;
		while (strlen($string) > $intmax)
		{
			$string_cut = substr($string, 0, $intmax);
			$last_space = strrpos($string_cut, "+");
			$strings[$i] = substr($string, 0, $last_space);
			$string = substr($string, $last_space, strlen($string));
			$i++;
		}
		$strings[$i] = $string;
		return $strings;
	}

	/**
	* Convert Words (text) to Speech (MP3)
	*
	*/
	protected function TTSToMp3($words,$lang)
	{
		// Directory
		$folder = "audio/".$lang;

		// Replace the non-alphanumeric characters
		// The spaces in the sentence are replaced with the Plus symbol
		$words = urlencode($words);

		// Name of the MP3 file generated using the MD5 hash
		$file = md5($words);

		// If folder doesn't exists, create it
		if (!file_exists($folder))
			mkdir($folder, 0755, true);

		// Save the MP3 file in this folder with the .mp3 extension
		$file = $folder."/TTS-".$file.".mp3";

		// If the MP3 file exists, do not create a new request
		if (!file_exists($file))
		{
			// Google Translate API cannot handle strings > 100 characters
			$words = $this->CutString($words,100);

			ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0');
			$mp3 = "";
			for ($i = 0; $i < count($words); $i++)
				$mp3[$i] = file_get_contents('http://translate.google.com/translate_tts?q='.$words[$i].'&tl='.$lang);

			file_put_contents($file, $mp3);
		}
		return $file;
	}

	/**
	* Say song name via TTS message
	* @param string message
	* @param string radio name display on sonos controller
	* @param int volume
	* @param string language
	*/
	public function SongNameTTS($directory,$volume=0,$unmute=0,$lang='fr')
	{
		$ThisSong = "Cette chanson s'appelle ";
		$By = " de ";

		$actual['track'] = $this->GetPositionInfo();

		$SongName = $actual['track']['Title'];
		$Artist = $actual['track']['TitleArtist'];

		$message = $ThisSong . $SongName . $By . $Artist ;

		$this->PlayTTS($message,$directory,$volume,$unmute,$lang);

		return true;
	}

	/**
	* Play a TTS message
	* @param string message
	* @param string radio name display on sonos controller
	* @param int volume
	* @param string language
	*/
	public function PlayTTS($message,$directory,$volume=0,$unmute=0,$lang='fr')
	{
		$actual['track'] = $this->GetPositionInfo();
		$actual['volume'] = $this->GetVolume();
		$actual['mute'] = $this->GetMute();
		$actual['status'] = $this->GetTransportInfo();
		$this->Pause();

		if ($unmute == 1)
			$this->SetMute(0);
		if ($volume != 0)
			$this->SetVolume($volume);

		$file = 'x-file-cifs://'.$directory.'/'.$this->TTSToMp3($message,$lang);
		if (((stripos($actual['track']["TrackURI"],"x-file-cifs://")) != false) or ((stripos($actual['track']["TrackURI"],".mp3")) != false))
		{
			// It's a MP3 file
			$TrackNumber = $this->AddURIToQueue($file);
			$this->ChangeTrack($TrackNumber);
			$this->Play();
			sleep(2);
			while ($this->GetTransportInfo() == "PLAYING") {}
			$this->Pause();
			$this->SetVolume($actual['volume']);
			$this->SetMute($actual['mute']);
			$this->ChangeTrack($actual['track']["TrackNumberInQueue"]);
			$this->SeekTime($actual['track']["RelTime"]);
			$this->RemoveTrackFromQueue($TrackNumber);
		}
		else
		{
			//It's a radio / or TV (playbar) / or nothing
			$this->SetQueue($file);
			$this->Play();
			sleep(2);
			while ($this->GetTransportInfo() == "PLAYING") {}
			$this->Pause();
			$this->SetVolume($actual['volume']);
			$this->SetMute($actual['mute']);
			$this->SetQueue($actual['track']["TrackURI"]);
		}

		if (strcmp($actual['status'],"PLAYING") == 0)
			$this->Play();
		return true;
	}

	public function AddSpotifyToQueue($spotify_id, $next = false) {
		$rand = mt_rand(10000000, 99999999);

		$meta = '<DIDL-Lite xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:upnp="urn:schemas-upnp-org:metadata-1-0/upnp/" xmlns:r="urn:schemas-rinconnetworks-com:metadata-1-0/" xmlns="urn:schemas-upnp-org:metadata-1-0/DIDL-Lite/">
				<item id="'.$rand.'spotify%3atrack%3a'.$spotify_id.'" restricted="true">
					<dc:title></dc:title>
					<upnp:class>object.item.audioItem.musicTrack</upnp:class>
					<desc id="cdudn" nameSpace="urn:schemas-rinconnetworks-com:metadata-1-0/">SA_RINCON2311_X_#Svc2311-0-Token</desc>
				</item>
			</DIDL-Lite>';

		$meta = htmlentities($meta);

		$url = '/MediaRenderer/AVTransport/Control';
		$action = 'AddURIToQueue';
		$service = 'urn:schemas-upnp-org:service:AVTransport:1';
		$next = (int)$next;

		$args = "
			<InstanceID>0</InstanceID>
			<EnqueuedURI>x-sonos-spotify:spotify%3atrack%3a{$spotify_id}</EnqueuedURI>
			<EnqueuedURIMetaData>{$meta}</EnqueuedURIMetaData>
			<DesiredFirstTrackNumberEnqueued>0</DesiredFirstTrackNumberEnqueued>
			<EnqueueAsNext>{$next}</EnqueueAsNext>
		";

		$filter = 'FirstTrackNumberEnqueued';

		return $this->Upnp($url, $service, $action, $args, $filter);
	}

	public function device_info() {
		$xml = $this->_device_info_raw('/xml/device_description.xml');

		$out = [
			'friendlyName' => (string)$xml->device->friendlyName,
			'modelNumber' => (string)$xml->device->modelNumber,
			'modelName' => (string)$xml->device->modelName,
			'softwareVersion' => (string)$xml->device->softwareVersion,
			'hardwareVersion' => (string)$xml->device->hardwareVersion,
			'roomName' => (string)$xml->device->roomName,
		];

		return $out;
	}

	public function get_coordinator() {
		$topology = $this->_device_info_raw('/status/topology');

		$myself = null;
		$coordinators = [];

		// Loop players, build map of coordinators and find myself
		foreach ($topology->ZonePlayers->ZonePlayer as $player) {
			$player_data = $player->attributes();

			$ip = parse_url((string)$player_data->location)['host'];

			if ($ip == $this->IP) {
				$myself = $player_data;
			}

			if ((string)$player_data->coordinator == 'true') {
				$coordinators[(string)$player_data->group] = $ip;
			}
		}

		$coordinator = $coordinators[(string)$myself->group];

		return new static($coordinator);
	}

	protected function _device_info_raw($url) {
		$url = "http://{$this->IP}:{$this->PORT}{$url}";

		if (!isset($this->_raw[$url])) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);

			$this->_raw[$url] = simplexml_load_string($data);
		}

		return $this->_raw[$url];
	}

	public static function detect($ip = '239.255.255.250', $port = 1900) {
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_set_option($sock, getprotobyname('ip'), IP_MULTICAST_TTL, 2);

		$data = <<<DATA
M-SEARCH * HTTP/1.1
HOST: {$ip}:reservedSSDPport
MAN: ssdp:discover
MX: 1
ST: urn:schemas-upnp-org:device:ZonePlayer:1
DATA;

		socket_sendto($sock, $data, strlen($data), null, $ip, $port);

		// All passed by ref
		$read = [$sock];
		$write = $except = [];
		$name = $port = null;
		$tmp = '';

		// Read buffer
		$buff = '';

		// Loop until there's nothing more to read
		while (socket_select($read, $write, $except, 1) && $read) {
			socket_recvfrom($sock, $tmp, 2048, null, $name, $port);

			$buff .= $tmp;
		}

		// Parse buffer into devices
		$data = static::_parse_detection_replies($buff);

		// Make an array of myselfs
		$devices = [];

		foreach ($data as $datum) {
			$url = parse_url($datum['location']);

			$devices[] = new static($url['host'], $url['port']);
		}

		return $devices;
	}

	protected static function _parse_detection_replies($replies) {
		$out = [];

		// Loop each reply
		foreach (explode("\r\n\r\n", $replies) as $reply) {
			if ( ! $reply) {
				continue;
			}

			// New array entry
			$arr =& $out[];

			// Loop each line
			foreach (explode("\r\n", $reply) as $line) {
				// End of header name
				if (($colon = strpos($line, ':')) !== false) {
					$name = strtolower(substr($line, 0, $colon));
					$val = trim(substr($line, $colon + 1));

					$arr[$name] = $val;
				}
			}
		}

		return $out;
	}

	public static function get_room_coordinator($room_name) {
		// Detect devices. Sometimes takes a few goes.
		do {
			$devices = static::detect();

			if (!$devices) {
				sleep(1);
			}
		} while (!$devices);

		foreach ($devices as $device) {
			if ($device->device_info()['roomName'] == $room_name) {
				return $device->get_coordinator();
			}
		}

		return false;
	}
}
