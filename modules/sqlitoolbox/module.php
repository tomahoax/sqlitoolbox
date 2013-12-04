<?php
/**
 * SQLi Toolbox module descriptor
 * @copyright Copyright (C) 2013 - SQLi. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Yannick Olympio
 * @version @@@VERSION@@@
 * @package sqlitoolbox
 */

$Module = array( 'name' => 'SQLi Toolbox' );

$ViewList = array();

$ViewList['loglist'] = array(
	'script'					=>	'loglist.php',
	'params'					=> 	array(),
	'unordered_params'			=> 	array(),
	'single_post_actions'		=> 	array(),
	'post_action_parameters'	=> 	array(),
	'default_navigation_part'	=> 'sqlitoolboxnavigationpart',
	'functions'					=> array( 'loglist' )
);

$ViewList['logview'] = array(
    'script' => 'logview.php',
    'default_navigation_part' => 'sqlitoolboxnavigationpart',
    'params' => array( 'logfile' ),
    'name' => 'Log view',
    'functions' => array( 'logview' ),
    'hidden' => true );

$ViewList['maillist'] = array(
    'script'					=>	'maillist.php',
    'params'					=> 	array(),
    'unordered_params'			=> 	array(),
    'single_post_actions'		=> 	array(),
    'post_action_parameters'	=> 	array(),
    'default_navigation_part'	=> 'sqlitoolboxnavigationpart',
    'functions'					=> array( 'maillist' )
);

$ViewList['mailview'] = array(
    'script' => 'mailview.php',
    'default_navigation_part' => 'sqlitoolboxnavigationpart',
    'params' => array( 'mailfile' ),
    'name' => 'Mail view',
    'functions' => array( 'mailview' ),
    'hidden' => true );

$ViewList['contentclassexport'] = array(
    'script' => 'contentclassexport.php',
    'default_navigation_part' => 'sqlitoolboxnavigationpart',
    'params' => array( 'classid' ),
    'name' => 'Content c view',
    'functions' => array( 'contentclassexport' ),
    'hidden' => true );

$ViewList['rolesexport'] = array(
		'script' => 'rolesexport.php',
		'default_navigation_part' => 'sqlitoolboxnavigationpart',
		'params' => array( 'roleid' ),
		'name' => 'Roles view',
		'functions' => array( 'rolesexport' ),
		'hidden' => true );

$FunctionList['loglist'] = array();
$FunctionList['logview'] = array();
$FunctionList['maillist'] = array();
$FunctionList['mailview'] = array();
$FunctionList['contentclassexport'] = array();
$FunctionList['rolesexport'] = array();
