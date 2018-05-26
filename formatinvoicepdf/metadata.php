<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'formatinvoicepdf',
    'title'       => 'Formatting the Invoice PDF',
    'description' => array(
        'de' => 'Eigene Formatierungen der PDF-Rechnung.',
        'en' => 'Own formattings of the invoice pdf.',
    ),
    'thumbnail' => 'thumbnail.jpg',
    'version'   => '2.1b',
    'author'    => 'Rico Wunglück',
    'url'       => 'http://www.hundemineral.de',
    'email'     => 'kontakt@hundemineral.de',

    'extend'    => array(
        'oxorder'	=> 'rwu/formatinvoicepdf/models/rwu_formatinvoicepdf_oxorder',
		'oxpdf'		=> 'rwu/formatinvoicepdf/core/rwu_formatinvoicepdf_oxpdf',					
    ),
    'settings'  => array(),
    'files'     => array(
        'rwu_formatinvoicepdf_invoicepdfarticlesummary'  => 'rwu/formatinvoicepdf/models/rwu_formatinvoicepdf_invoicepdfarticlesummary.php',
		'rwu_formatinvoicepdf_paymentinstructions' => 'rwu/formatinvoicepdf/models/rwu_formatinvoicepdf_paymentinstructions.php'
    ),
    'templates' => array(),
    'blocks'    => array()
);