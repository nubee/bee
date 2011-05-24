<?php
class nbFileSystemUtils
{
    public static function mkdir($directory, $force = false)
    {
        $logger = nbLogger::getInstance();
        if($force){
            $logger->logLine('mkdir: removing the directory content' , nbLogger::INFO);
            try {
                nbFileSystem::rmdir($directory, true);
                $logger->logLine('mkdir: done' , nbLogger::INFO);
            }
            catch (Exception $e) {
                $logger->logLine('mkdir: error removing directory' , nbLogger::INFO);
                throw ($e);
            }
        }
        $logger->logLine('Creating folder '. $directory, nbLogger::COMMENT);
        try {
          nbFileSystem::mkdir($directory,true);
        }
        catch (Exception $e) {
          $logger->logLine('mkdir: the folder already exists ... skipping' , nbLogger::INFO);
        }
    }

}