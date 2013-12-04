<?php

$errormsg = '';
$mailList = array();

$lister = new SQLiGeneratedFilesManager();
$mailList = $lister->listMails();
$tpl = eZTemplate::factory();

//@TODO Implementer un système de pagination
//@TODO Permetre de selectionner la colonne et le sens pour le tri depuis le BO

$tpl->setVariable( 'mailList', $mailList );
$Result['left_menu'] = 'design:sqlitoolbox/parts/leftmenu.tpl';
$Result['content'] = $tpl->fetch( 'design:sqlitoolbox/maillist.tpl' );