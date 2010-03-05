<?php

/**
 * nfFilesystem provides basic utility to manipulate the file system.
 *
 * @package    bee
 * @subpackage system
 */
class nbFileSystem
{
  public static function getFileName($filename)
  {
    if(!is_file($filename))
      return '';
    
    return basename($filename);
  }

  /**
   * Creates a directory.
   *
   * @param  string $path  The directory path
   * @param  bool $withParents
   *
   * @return bool true if the directory has been created, false otherwise
   * if withParent is true, this method creates parent folders
   */
  public static function mkdir($path, $withParents = false)
  {
    if(file_exists($path))
      throw new Exception('[nbFileSystem::mkdir] The path "'.$path.'" already exists');
    else
    if(!mkdir($path, null, $withParents))
    {
      throw new Exception('[nbFileSystem::mkdir] error creating folder '.$path);
    }
  }

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

  public static function touch($path)
  {
    if(!touch($path))
    {
      throw new Exception('[nbFileSystem::touch] error touching file '.$path);
    }
  }

  public static function delete($file)
  {
    if(!file_exists($file))
      return;
    if(is_dir($file))
      throw new Exception('[nbFileSystem::delete] can\'t delete folder');
    unlink($file);
  }

  public static function copy($source, $dest = null, $overwrite = false)
  {
    if(file_exists($dest) && is_dir($dest))
      $dest .= '/'.self::getFileName($source);

    if(file_exists($dest) && !$overwrite)
      throw new InvalidArgumentException('[nbFileSystem::copy] destination file exists');
    if(!copy($source, $dest))
      throw new Exception('[nbFileSystem::copy] copy command failed');
  }

  public static function move($source, $destination)
  {
      if(!rename($source, $destination))
        throw new Exception('[nbFileSystem::moveDir] rename command failed');
  }
  
}