<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'blockgroups',
    'title'       => 'Block Usergroups',
    'description' => array(
        'de' => 'Benutzergruppen für die Verwendundung von Gutscheinserien und/oder Rabatten sperren',
        'en' => 'Block usergroups for vouchers and/or discounts',
    ),
    'thumbnail' => 'thumbnail.jpg',
    'version'   => '1.1',
    'author'    => 'Rico Wunglück',
    'url'       => 'http://www.hundemineral.de',
    'email'     => 'kontakt@hundemineral.de',

    'extend'    => array(
        'discount_users'    => 'rwu/blockgroups/application/controllers/admin/rwu_blockgroups_discount_users',
        'oxdiscountlist'    => 'rwu/blockgroups/application/models/rwu_blockgroups_oxdiscountlist',
        'voucherserie_groups'    => 'rwu/blockgroups/application/controllers/admin/rwu_blockgroups_voucherserie_groups',
        'oxvoucher'         => 'rwu/blockgroups/application/models/rwu_blockgroups_oxvoucher',
        'oxvoucherserie'    => 'rwu/blockgroups/application/models/rwu_blockgroups_oxvoucherserie',
    ),
    'settings'  => array(),
    'files'     => array(
        'rwu_blockgroups_discount_groups_ajax'    => 'rwu/blockgroups/application/controllers/admin/rwu_blockgroups_discount_groups_ajax.php',
        'rwu_blockgroups_voucherserie_groups_ajax'    => 'rwu/blockgroups/application/controllers/admin/rwu_blockgroups_voucherserie_groups_ajax.php'
    ),
    'templates' => array(
        'rwu_discount_users.tpl'        => 'rwu/blockgroups/application/views/admin/tpl/rwu_discount_users.tpl',
        'rwu_discount_groups_block.tpl' => 'rwu/blockgroups/application/views/admin/tpl/popups/rwu_discount_groups_block.tpl',
        'rwu_voucherserie_groups.tpl'        => 'rwu/blockgroups/application/views/admin/tpl/rwu_voucherserie_groups.tpl',
        'rwu_voucherserie_groups_block.tpl' => 'rwu/blockgroups/application/views/admin/tpl/popups/rwu_voucherserie_groups_block.tpl'
    ),
    'blocks'    => array()
);