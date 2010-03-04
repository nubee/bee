<?php

class nbFileSystem
{
  public static function getFileName($filename)
  {
    if(!is_file($filename))
      return '';
    
    return basename($filename);
  }

  public static function mkdir($path, $withParents = false)
  {
    if(file_exists($path))
      throw new Exception('[nbFileSystem::mkdir] The path "'.$path.'" already exists');
    else
    if(! mkdir($path, null, $withParents))
    {
      throw new Exception('[nbFileSystem::mkdir] error creating folder '.$path);
    }
  }

  public static function rmdir($path)
  {
    if(!file_exists($path))
      return;

    if(! rmdir($path))
    {
      throw new Exception('[nbFileSystem::rmdir] error deleting folder '.$path);
    }
  }

  public static function touch($path)
  {
    if(! touch($path))
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

//  public static function copy($src, $destination)
//  {
//    if(!file_exists($src))
//      throw new Exception('[nbFileSystem::copy] source file doesn\'t exist');
//  }
}