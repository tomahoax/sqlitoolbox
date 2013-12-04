<?php

//@TODO Permetre de selectionner la colonne et le sens pour le tri depuis le BO

$errormsg = '';
$logList = array();
$tpl = eZTemplate::factory();

$lister = new SQLiGeneratedFilesManager();
$logList = $lister->listLogs();

$tpl->setVariable( 'logList', $logList );
$Result['left_menu'] = 'design:sqlitoolbox/parts/leftmenu.tpl';
$Result['content'] = $tpl->fetch( 'design:sqlitoolbox/loglist.tpl' );