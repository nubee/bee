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

  public static function rmdir($path)
  {
    if(!file_exists($path))
      return;

    if(!rmdir($path))
    {
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
      $dest .= '/'.basename($source);

    if(file_exists($dest) && !$overwrite)
      throw new InvalidArgumentException('[nbFileSystem::copy] destination file exists');
    if(!copy($source, $dest))
      throw new InvalidArgumentException('[nbFileSystem::copy] destination file exists');
  }

  public static function recursiveDeleteDir($path)
  {
    if(!file_exists($path))
      return;

    if(!is_dir($path))
      self::delete($path);
    
    else
      {
        $str = glob(rtrim($path,'/').'/*');
        foreach($str as $index => $p)
          self::recursiveDeleteDir($p);
      }

    return self::rmdir($path);
  }

  public static function moveDir($source, $destination)
  {
    if(!file_exists($source))
      throw new InvalidArgumentException('[nbFileSystem::moveDir] source dir doesn\'t exist');

    if(!file_exists($destination))
      throw new InvalidArgumentException('[nbFileSystem::moveDir] destination dir doesn\'t exist');

    if(!is_dir($source))
      throw new InvalidArgumentException('[nbFileSystem::moveDir] doesn\'t remove file');
    
    else
      {
        self::mkdir($destination . "/" . basename($source));
        self::rmdir($source);
      }
  }
  
}