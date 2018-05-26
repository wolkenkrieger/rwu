<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';
 
/**
 * Module information
 */
$aModule = array(
    'id'          => 'mailextender',
    'title'       => 'Erweiterungen der originalen Mailklasse',
    'description' => array(
        'de' => 'Erweitert die originale Mailklasse und fügt einige nützliche Funktionen hinzu.',
        'en' => 'Extends the original mail class and adds some usefull functions.',
    ),
    'thumbnail' => '',
    'version'   => '0.2',
    'author'    => 'Rico Wunglück',
    'url'       => 'http://www.hundemineral.de',
    'email'     => 'kontakt@hundemineral.de',

    'extend'    => array(
        'oxemail'       => 'rwu/mailextender/core/rwu_mailextender_oxemail',
    ),
    'settings'          => array(),
    'files'             => array(),
    'blocks'            => array()
);
?>