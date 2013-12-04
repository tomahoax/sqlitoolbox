<?php

class SQLiGeneratedFilesManager {

    protected $logList = array();
    protected $mailList = array();

    public static function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    public static function isStdLog($logFile)
    {
        $stdLogs = array( 'error.log', 'warning.log', 'debug.log', 'notice.log', 'strict.log' );
        foreach ($stdLogs as $stdLog)
        {
            if (self::endsWith($logFile, $stdLog))
            {
                return true;
            }
        }
        return false;
    }

    public static function encodePath($patern, $existingPath)
    {
        preg_match($patern, $existingPath, $matches);
        $varDir = $matches['varDir'];
        $fileName = $matches['name'];
        $encodedPath = empty($varDir) ? $fileName : $varDir.'|'.$fileName;

        return $encodedPath;
    }

    public static function decodeMailPath( $encodedPath )
    {
        $mailfile = explode('|', $encodedPath );
        $existingPath = 'var/log/mail/'.$mailfile[0].'.mail' ;

        return $existingPath;
    }

    public static function decodeLogPath( $encodedPath )
    {
        // nb: this dir is calculated the same way as ezlog does
        $logfile = explode('|', $encodedPath );
        $existingPath = count($logfile) === 2 ? 'var/'.$logfile[0].'/log/'.$logfile[1].'.log' : 'var/log/'.$logfile[0].'.log' ;

        return $existingPath;
    }

    protected function scanDirs( $mask = '/log/*.log', $rootFolder = 'var')
    {
        $dirs = array();

        foreach( scandir( 'var' ) as $dir )
        {
            if ($dir == '..')
                continue;

            $dir = ($dir === '.') ? $rootFolder : $rootFolder.'/'.$dir;

            if (is_dir($dir))
            {
                $pattern = $dir.$mask;
                $logs = glob($pattern);

                if (!empty($logs))
                {
                    $dirs[$dir] = $logs;
                }
            }
        }

        return $dirs;
    }

    public function listLogs()
    {
        $dirs = $this->scanDirs('/log/*.log');
        $this->logList = array();

        foreach ($dirs as $dir => $logs) {
            foreach( $logs as $level => $logfile )
            {
                if ( file_exists( $logfile ) )
                {
                    $count = 1;
                    $size = filesize( $logfile );
                    $modified = filemtime( $logfile );
                    $enhanced = self::isStdLog($logfile);
                    $link = self::encodePath('/^var\/((?<varDir>.+)\/)?log\/(?<name>.+)\.log$/i', $logfile);

                    // *** parse rotated log files, if found ***
                    $data = array();
                    for( $i = eZdebug::maxLogrotateFiles(); $i > 0; $i-- )
                    {
                        $archivelog = $logfile.".$i";
                        if ( file_exists( $archivelog ) )
                        {
                            $data = self::asum( $data, self::parseLog( $archivelog ) );
                            $size += filesize( $archivelog );
                            $count++;
                        }
                    }

                    $this->logList[$logfile] = array( 'path' => $logfile, 'count' => $count, 'size' => $size, 'modified' => $modified, 'link' => $link, 'enhanced' => $enhanced );
                }
            }
        }

        return self::sortData($this->logList, 'modified', SORT_DESC);
    }

    public function listMails()
    {
        $dirs = $this->scanDirs('/log/mail/*.mail');

        foreach ($dirs as $dir => $mails)
        {
            foreach ($mails as $mailFile )
            {
                $size = filesize( $mailFile );
                $generationTime = filemtime( $mailFile );
                $link = self::encodePath('/^var\/((?<varDir>.+)\/)?log\/mail\/(?<name>.+)\.mail/i', $mailFile);

                // Handle mail headers
                $parser = new ezcMailParser();
                $messageSet = new ezcMailFileSet( array( $mailFile ) );
                $mail = array_shift($parser->parseMail( $messageSet ));
                $mailFrom = $mail->from;
                $mailTo = array_shift($mail->to);
                $mailSubject = $mail->subject;

                $this->mailList[$mailFile] = array( 'path' => $mailFile, 'size' => $size, 'generation_time' => $generationTime, 'link' => $link, 'mail_subject' => $mailSubject,'mail_to' => (string) $mailTo, 'mail_from' => (string) $mailFrom );
            }
        }

        return self::sortData($this->mailList, 'generation_time', SORT_DESC);
    }

    //@TODO Etendre la méthode afin de pouvoir passer plusieurs colonnes pour le tri (une seule actuellement)
    static public function sortData($array, $sortColumn, $sortOrder)
    {
        foreach ($array as $key => $row) {
            ${$sortColumn}[$key]  = $row[$sortColumn];
        }

        array_multisort(${$sortColumn}, $sortOrder, $array);

        return $array;
    }

    /**
     * Method gathered from ezLogsGrapher class orignally for ggsysinfo
     * Returns an array where indexes are timestamps, and values are the number of log events found
     * @author G. Giunta
     * @param $scale the time interval used to average (default: 1 minute)
     * @return array
     */

    /**
     * Method gathered from ezLogsGrapher class orignally for ggsysinfo
     */

    static function asum( $a1, $a2 )
    {
        foreach ( $a2 as $key => $val )
        {
            if ( isset( $a1[$key] ) )
            {
                $a1[$key] = $a1[$key] + $val;
            }
            else
            {
                $a1[$key] = $val;
            }
        }
        return $a1;
    }

    static function parseLog( $logfile, $scale=60, $exclude_regexp='#\] Timing Point: #' )
    {
        if ( !file_exists( $logfile ) )
        {
            return array();
        }
        $file =  file( $logfile );
        $data = array();
        foreach ( $file as $line )
        {
            if ( strlen( $line ) > 22 && substr( $line, 0, 2 ) == '[ ' && substr( $line, 22, 2) == ' ]' )
            {
                if ( !preg_match( $exclude_regexp, $line ) )
                {
                    $time = strtotime( substr( $line, 2, 20 ) );
                    if ( $time > 0 )
                    {

                        $time = $time - ( $time % $scale );
                        if( !isset( $data[$time] ) )
                        {
                            $data[$time] = 1;
                        }
                        else
                        {
                            $data[$time]++;
                        }
                    }
                }
            }
        }
        return $data;
    }
}