<?php namespace Como\Routing;

use IteratorAggregate;
use ArrayIterator;
use Closure;
use Como\Http\RequestType as HTTP;

/**
 * This is a singelton.
 * It holds routing information
 * Application::get pushes items onto the particular stack
 * Need it to be a singelton because we need to be able to
 * add multiple items to the same container
 */

class Collection implements IteratorAggregate
{
  private static $instance = null;
 
  /*
   * A store to hold GET http requests
   * and POST requests
   * access index 0 for get, 1 for post
   */ 
  private static $requests = array(
    HTTP::GET   => array(),
    HTTP::POST  => array()
  );
  
  private function __construct() 
  {
  } 
  
  public static function getInstance()
  {
    if(is_null(self::$instance)) 
    {
      self::$instance = new self();
    }
    return self::$instance;
  } 
  
  public function getIterator()
  {
    return new ArrayIterator(self::$requests);

  } 
  public function all()
  {
    return self::$requests;

  }  
 
  public function push($type, $uri, Closure $callback)
  {
    switch($type)
    {
      case (HTTP::GET):
      array_push(self::$requests[HTTP::GET], array(
        "uri"       => $uri,
        "callback"  => $callback
      ));
      break;
      case (HTTP::POST):
      array_push(self::$requests[HTTP::POST], array(
        "uri"       => $uri,
        "callback"  => $callback
      ));
      break;
    } 

  }  
}
