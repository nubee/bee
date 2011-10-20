<?php

/**
 * nfFilesystem provides basic utility to manipulate the file system.
 *
 * @package    bee
 * @subpackage system
 */
class nbFileSystem
{
  private static $instance;
  private $isVerbose = false;

  public static function getInstance()
  {
    if(!self::$instance)
      self::$instance = new nbFileSystem();

    return self::$instance;
  }
  
  public function setVerbose($verbose)
  {
    $this->isVerbose = $verbose;
  }

  /**
   * This method returns the filename.
   *
   * @param  string $filename  The filename
   * @return string Returns filename component of "filename".
   */
  public function getFileName($filename)
  {
    if(!is_file($filename))
      return '';

    return basename($filename);
  }

  /**
   * This method creates a directory.
   *
   * @param  string $path  The directory path
   * @param  bool $withParents
   *
   * To create parent folders, set "withParents" to true
   */
  public function mkdir($path, $recursive = false, $mode = 0777)
  {
    if(file_exists($path) && is_dir($path))
      return;

    $this->logLine('dir+: ' . $path);

//      throw new Exception('[nbFileSystem::mkdir] The path "'.$path.'" already exists');
    // mode is ignored in windows... but not in linux & co.
    if(!@mkdir($path, $mode, $recursive)) {
      throw new Exception('[nbFileSystem::mkdir] Error creating folder ' . $path);
    }
  }

  /**
   * This method removes a directory.
   *
   * @param  string $path  The directory path
   * @param  boolean $removeIfNotEmpty  Removes the directory content if not empty
   * @param  boolean $leaveEmptyFolder  Leaves the directory empty
   */
  public function rmdir($directory, $removeIfNotEmpty = false, $leaveEmptyFolder = false)
  {
    if(substr($directory, -1) == "/") {
      $directory = substr($directory, 0, -1);
    }

    if(!file_exists($directory) || !is_dir($directory)) {
      throw new Exception('[nbFileSystem::rmdir] Folder ' . $directory . ' does not exist');
    }
    elseif(!is_readable($directory)) {
      throw new Exception('[nbFileSystem::rmdir] Folder ' . $directory . ' is not readable');
    }
    else {
      $directoryHandle = opendir($directory);

      while($contents = readdir($directoryHandle)) {
        if($contents != '.' && $contents != '..') {
          if(!$removeIfNotEmpty)
            throw new Exception('[nbFileSystem::rmdir] Folder ' . $directory . ' is not empty');
          
          $path = $directory . "/" . $contents;

          if(is_dir($path)) {
            $this->rmdir($path, true, false);
          }
          else {
            unlink($path);
          }
        }
      }

      closedir($directoryHandle);

      if(!$leaveEmptyFolder) {
        if(!rmdir($directory))
          throw new Exception('[nbFileSystem::rmdir] Error deleting folder ' . $path);
      }
      
      return true;
    }
  }

  /**
   * This method creates empty file.
   *
   * @param string $path  The filename, including path
   */
  public function touch($path)
  {
    if(!@touch($path)) {
      throw new Exception('[nbFileSystem::touch] Error touching file ' . $path);
    }
  }

  /**
   * This method removes file.
   *
   * @param mixed $file  The filename, including path
   */
  public function delete($file)
  {
    if(!file_exists($file))
      throw new Exception('[nbFileSystem::delete] File does not exist: ' . $file);

    if(is_dir($file))
      throw new Exception('[nbFileSystem::delete] Can\'t delete file: ' . $file . ' (it\'s a directory)');

    if(!unlink($file))
      throw new Exception('[nbFileSystem::delete] Can\'t delete file: ' . $file);

    return true;
  }

  /**
   * This method copies a file.
   *
   * To overwrite existing files, set "overwrite" to true.
   *
   * @param string $source  The original filename
   * @param string $dest  The target filename
   * @param bool  $overwrite
   */
  public function copy($source, $destination, $overwrite = false, $checkMostRecent = false)
  {
    if(!file_exists($source))
      throw new Exception('[nbFileSystem::copy] Source file: ' . $destination . ' does not exist');

    // we create target_dir if needed
    if(!is_dir(dirname($destination))) {
      $this->mkdir(dirname($destination));
    }
    
    if(is_dir($destination)) {
      $destination .= '/' . basename($source);
    }

    $mostRecent = false;
    if(file_exists($destination) && $checkMostRecent) {
      $statTarget = stat($destination);
      $statOrigin = stat($source);
      $mostRecent = ($statOrigin['mtime'] > $statTarget['mtime']) ? true : false;
    }
    
    if(!file_exists($destination)) {
      $this->logLine('file+: ' . $destination);
      copy($source, $destination);
    }
    else if($overwrite) {
      if($checkMostRecent) {
        if($mostRecent) {
          $this->logLine('file+: ' . $destination . ' overwritten (source most recent)');
          copy($source, $destination);
        }
        else
          $this->logLine('file : ' . $destination . ' not copied');
      }
      else {
        $this->logLine('file+: ' . $destination . ' overwritten');
        copy($source, $destination);
      }
    }
    else
      throw new Exception('[nbFileSystem::copy] Can\'t overwrite file: ' . $destination);
  }

  /**
   * Mirrors a directory to another.
   *
   * @param string   $originDir  The origin directory
   * @param string   $targetDir  The target directory
   * @param sfFinder $finder     An sfFinder instance
   * @param array    $options    An array of options (see copy())
   */
  public function mirror($originDir, $targetDir, nbFileFinder $finder, $options = array())
  {
    $overwrite = isset($options['overwrite']) ? $options['overwrite'] : false;
    
    foreach($finder->relative()->in($originDir) as $file) {
      if(is_dir($originDir . DIRECTORY_SEPARATOR . $file)) {
        $this->mkdir($targetDir . DIRECTORY_SEPARATOR . $file);
      }
      else if(is_file($originDir . DIRECTORY_SEPARATOR . $file)) {
        $this->copy($originDir . DIRECTORY_SEPARATOR . $file, $targetDir . DIRECTORY_SEPARATOR . $file, $overwrite);
      }
      else if(is_link($originDir . DIRECTORY_SEPARATOR . $file)) {
        $this->symlink($originDir . DIRECTORY_SEPARATOR . $file, $targetDir . DIRECTORY_SEPARATOR . $file);
      }
      else {
        throw new Exception(sprintf('Unable to guess "%s" file type.', $file));
      }
    }
  }

  /**
   * This method move a file or a directory content.
   *
   * @param string $source  The filename or directoryname
   * @param string $dest  The target filename or directoryname
   */
  public static function move($source, $destination)
  {
    if(!@rename($source, $destination))
      throw new Exception('[nbFileSystem::moveDir] rename command failed');
  }

  /**
   * This method find-replace a string or more strings.
   *
   * @param string $search_string  The string or array of strings to search.
   * @param string $replace_string  The string or array of strings to replace.
   * @param $file
   */
  public static function replaceTokens($search_string, $replace_string, $file)
  {
    $fsr = new File_SearchReplace($search_string, $replace_string, $file, '', false);
    if(!$fsr)
      throw new Exception('[nbFileSystem::replaceTokens] replaceTokens command failed');
    $fsr->doSearch();
  }

  public static function chmod($file, $mode)
  {
    if(!chmod($file, $mode))
      throw new Exception('[nbFileSystem::chmod] chmod command failed');
  }

  public static function formatPermissions($file)
  {
    $perms = fileperms($file);

    if(($perms & 0xC000) == 0xC000) {
      // Socket
      $info = 's';
    }
    elseif(($perms & 0xA000) == 0xA000) {
      // Symbolic Link
      $info = 'l';
    }
    elseif(($perms & 0x8000) == 0x8000) {
      // Regular
      $info = '-';
    }
    elseif(($perms & 0x6000) == 0x6000) {
      // Block special
      $info = 'b';
    }
    elseif(($perms & 0x4000) == 0x4000) {
      // Directory
      $info = 'd';
    }
    elseif(($perms & 0x2000) == 0x2000) {
      // Character special
      $info = 'c';
    }
    elseif(($perms & 0x1000) == 0x1000) {
      // FIFO pipe
      $info = 'p';
    }
    else {
      // Unknown
      $info = 'u';
    }

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
        (($perms & 0x0800) ? 's' : 'x' ) :
        (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
        (($perms & 0x0400) ? 's' : 'x' ) :
        (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
        (($perms & 0x0200) ? 't' : 'x' ) :
        (($perms & 0x0200) ? 'T' : '-'));

    return $info;
  }

  /**
   * Logs a message in a section.
   *
   * @param string $section  The section name
   * @param string $message  The message
   * @param int    $size     The maximum size of a line
   */
  protected function logLine($message, $level = nbLogger::INFO)
  {
    if($this->isVerbose)
      nbLogger::getInstance()->logLine($message, $level);
  }

  public static function sanitizeDir($directory)
  {
    return preg_replace('/\/+$/', '', $directory);
  }

}