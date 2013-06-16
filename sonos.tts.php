<?php
// Exemple de l'utilisation de la fonction Text-to-speech
require("sonos.class.php");
$IP_sonos_1 = "192.168.1.11"; // A adapter avec l'adresse IP du Sonos à contrôler
$directory = "www/sonos";		// Indiquer ici le dossier partagé contenant les scrips PHP sonos (et qui contiendra le sous-dossier audio)

$volume = 0;
$force_unmute = 0;
 
if (isset($_GET['force_unmute'])) $force_unmute = $_GET['force_unmute']; // Force la désactivation de la sourdine. Optionnel
if (isset($_GET['volume'])) $volume = $_GET['volume']; // Niveau sonore. Optionnel.
$message = $_GET['message']; // Message à diffuser
 
//Instanciation de la classe
$sonos_1 = new SonosPHPController($IP_sonos_1);
$sonos_1->PlayTTS($message,$directory,$volume,$force_unmute); //Lecture du message
?>