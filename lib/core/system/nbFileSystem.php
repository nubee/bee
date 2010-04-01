<?php

/**
 * nfFilesystem provides basic utility to manipulate the file system.
 *
 * @package    bee
 * @subpackage system
 */
class nbFileSystem
{
  /**
   * This method returns the filename.
   *
   * @param  string $filename  The filename
   * @return string Returns filename component of "filename".
   */
  public static function getFileName($filename)
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
  public static function mkdir($path, $withParents = false)
  {
    if(file_exists($path) && is_dir($path))
      return;
//      throw new Exception('[nbFileSystem::mkdir] The path "'.$path.'" already exists');
    // mode is ignored in windows... but not in linux & co.
    if(!mkdir($path, 0777, $withParents))
    {
      throw new Exception('[nbFileSystem::mkdir] error creating folder '.$path);
    }
  }

  /**
   * This method removes a directory.
   *
   * @param  string $path  The directory path
   * @param  bool $recursive
   *
   * To remove folder contents, set "recursive" to true
   */
  public static function rmdir($path, $recursive = false)
  {
    if(!file_exists($path))
      return;

    if($recursive) {
      $finder = nbFileFinder::create('any');
      $files = $finder->add('*')->remove('.')->remove('..')->in($path);
      foreach($files as $file)
        if(is_dir($file))
          self::rmdir($file,$recursive);
        else
          self::delete($file);
    }

    if(!rmdir($path)) {
      throw new Exception('[nbFileSystem::rmdir] error deleting folder '.$path);
    }
  }

  /**
   * This method creates empty file.
   *
   * @param string $path  The filename, including path
   */
  public static function touch($path)
  {
    if(!touch($path))
    {
      throw new Exception('[nbFileSystem::touch] error touching file '.$path);
    }
  }

  /**
   * This method removes file.
   *
   * @param mixed $file  The filename, including path
   */
  public static function delete($file)
  {
    if(!file_exists($file))
      return;
    if(is_dir($file))
      throw new Exception('[nbFileSystem::delete] can\'t delete folder');
    elseif(!unlink($file))
      throw new Exception('[nbFileSystem::delete] can\'t delete file');
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
  public static function copy($source, $dest = null, $overwrite = false)
  {
    if(file_exists($dest) && is_dir($dest)) {
      $dest .= '/' . self::getFileName($source);
    }

    if(file_exists($dest) && !$overwrite)
      throw new InvalidArgumentException('[nbFileSystem::copy] error copying ' . $source . ': destination file exists: ' . $dest);
    if(!copy($source, $dest))
      throw new Exception('[nbFileSystem::copy] error copying ' . $source . ' to ' . $dest);
  }

  /**
   * This method move a file or a directory content.
   *
   * @param string $source  The filename or directoryname
   * @param string $dest  The target filename or directoryname
   */
  public static function move($source, $destination)
  {
      if(!rename($source, $destination))
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

    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } else {
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
}