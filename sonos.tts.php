<?php
// Code from https://github.com/DjMomo/sonos
// Exemple de l'utilisation de la fonction Text-to-speech
require("sonos.class.php");
$directory = "192.168.99.105/sonos";		// Indiquer ici le dossier partage contenant les scrips PHP sonos (et qui contiendra le sous-dossier audio)
$ttsengine = 'acapela';						// Indiquer ici le moteur vocal a utiliser (google ou acapela)
$lang = 'fr';

$volume = 30;	// De 0 a 100 %
$force_unmute = 1;

$IP_sonos = "192.168.100.74"; // A adapter avec l'adresse IP du Sonos a controler


if (isset($_GET['force_unmute'])) $force_unmute = $_GET['force_unmute']; // Force la desactivation de la sourdine. Optionnel
if (isset($_GET['volume'])) $volume = $_GET['volume']; // Niveau sonore. Optionnel.
$message = $_GET['message']; // Message a diffuser

// Instanciation de la classe
$sonos = new SonosPHPController($IP_sonos);
$sonos->PlayTTS($message,$directory,$volume,$force_unmute,$lang,$ttsengine); // Lecture du message
?>
