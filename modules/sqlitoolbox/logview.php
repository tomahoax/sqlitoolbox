<?php
/**
 * Concanate and display the content of a splitted log file
 *
 * @author Yannick Olympio
 * @copyright (C) SQLi 2013
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 */

$errormsg = 'File not found';
$data = array();
$logname = '';

$logfile = SQLiGeneratedFilesManager::decodeLogPath($Params['logfile']);

if ( file_exists( $logfile ) )
{
    $errormsg = '';

    $mdate = gmdate( 'D, d M Y H:i:s', filemtime( $logfile ) ) . ' GMT';
    header( 'Content-Type: text/plain' );
    header( "Last-Modified: $mdate" );

    for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
    {
        $archivelog = $logfile.".$i";
        if ( file_exists( $archivelog ) )
        {
            readfile( $archivelog );
        }
    }

    readfile( $logfile );
    $mdate = gmdate( 'D, d M Y H:i:s', filemtime( $logfile ) ) . ' GMT';

    eZExecution::cleanExit();

    // *** parse rotated log files, if found ***
    for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
    {
        $archivelog = $logfile.".$i";
        if ( file_exists( $archivelog ) )
        {
            $data = array_merge( $data, ezLogsGrapher::splitLog( $archivelog ) );
        }
    }

    // *** Parse log file ***
    $data = array_reverse( array_merge( $data, ezLogsGrapher::splitLog( $logfile ) ) );
    $mdate = gmdate( 'D, d M Y H:i:s', filemtime( $logfile ) ) . ' GMT';
    header( "Last-Modified: $mdate" );
}
else
{
    header("HTTP/1.0 404 $errormsg");
}


?>