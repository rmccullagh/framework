<?php namespace Como\Solace;

class ClassLoader
{
  
  public static function register() {

    /**
     * the /src directory
     */ 
    $path = __DIR__ . '/../../';
 
    spl_autoload_register(function ($classname) use($path) 
    {
            
      $classname = str_replace("\\", "//", $classname);
      $resolved  = $path . DIRECTORY_SEPARATOR . $classname . '.php';
      
      if(file_exists($resolved)) 
      {
        require_once $resolved;
        return true;

      } 
      else 
      {
        return false;
      }

    });

  }
}
