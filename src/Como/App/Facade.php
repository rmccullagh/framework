<?php namespace Como\App;

use ReflectionClass;

abstract class Facade
{
  /*
   * @var array
   * This is a key value store for the real
   * objects
   */
  private static $implementer_cache = [];

  private static function getInstance($name)
  {

    if(! isset(self::$implementer_cache[$name]))
    {
      self::$implementer_cache[$name] = new $name();

    }
    
    return self::$implementer_cache[$name];
    
  }

  public static function __callStatic($method, array $arguments = array())
  {
    
    $class_name = static::getRealImplementerName();
    
    $reflection = new ReflectionClass($class_name);
    
    if($reflection->hasMethod($method))
    {
      return call_user_func_array([self::getInstance($class_name), $method], $arguments);

    }
    else
    {
      throw new BadMethodCallException('
        call to undefined method '.$className.'::'.$method.' ' 
      );  

    }

  }

}


