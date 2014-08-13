<?php namespace Como\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use ReflectionClass;
use BadMethodCallException;
use InvalidArgumentException;

class Log
{
  private static $instance = null;

  private static $name = 'default';

  private static $file = 'default.log';

  private static $path;

  public static function setPathName($name)
  {

    if(!is_string($name)) 
    {

      $trace = debug_backtrace()[0];
      throw new InvalidArgumentException(
        __METHOD__ . " excepts parameter 1 to be string, " . gettype($name) . " given -"
      );

    }


    if($name[strlen($name) - 1] != '/') 
    {
     
       $name = $name . '/';
    
    }   
    
    self::$path = $name; 

  }

  public static function getPathName()
  {
    if(! self::$path)
      return __DIR__ . '/../../../log/';
    return self::$path;

  }

  public static function setFileName($name)
  {
    if(!is_string($name)) {
      $trace = debug_backtrace()[0];
      throw new InvalidArgumentException(
        __METHOD__ . " excepts parameter 1 to be string, " . gettype($name) . " given -"
      );
    }
    self::$file = $name;

  }

  public static function getFileName()
  {
    return self::$file;

  }

  public static function getInstance()
  {

    if(self::$instance === null) {
      self::$instance = new Logger(self::$name);
      self::$instance->pushHandler(
        new StreamHandler(self::getPathName() . self::getFileName(), Logger::DEBUG)
      );
    }

    return self::$instance;

  }
	
	public static function getMonolog()
	{
		return self::$instance;
	}

  public static function __callStatic($method, array $arguments = array())
  {
    $class = new ReflectionClass('\Monolog\Logger');
    $has_method = $class->hasMethod($method);

    if(!$has_method) {
      throw new BadMethodCallException();
    }

     
    $back_trace = debug_backtrace();
    $back_trace = $back_trace[count($back_trace)-1];
    $caller     = implode("::", array($back_trace['class'], $back_trace['function']));

    
    array_push($arguments, array(
      $caller
    ));

    return call_user_func_array([self::getInstance(), $method], $arguments);

  }

}
