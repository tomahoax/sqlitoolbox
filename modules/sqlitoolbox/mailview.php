<?php

$errormsg = 'File not found';
$data = array();
$mailfile = SQLiGeneratedFilesManager::decodeMailPath($Params['mailfile']);

if ( file_exists( $mailfile ) )
{
    // Handle mail headers
    $parser = new ezcMailParser();
    $messageSet = new ezcMailFileSet( array( $mailfile ) );
    $mail = array_shift($parser->parseMail( $messageSet ));
    $content_type = $mail->getHeader('content-type');

    header("Content-Type: $content_type");
    readfile( $mailfile );
    eZExecution::cleanExit();
}