<?php namespace Como\Routing;

use ArrayAccess;
use IteratorAggregate;

class Parameter implements ArrayAccess, IteratorAggregate
{

  private static $instance = null;
  private $bag = array();
 
  private function __construct()
  {
  }
  
  public function getIterator()
  {
    return new ArrayIterator(self::$bag);

  }
 
  public static function getInstance()
  {
    if(is_null(self::$instance)) 
    {
      self::$instance = new self();
    }
    return self::$instance;
  } 

  public function offsetSet($offset, $value)
  {
    $this->bag[$offset] = $value;
  }

  public function offsetExists($offset)
  {
    return isset($this->bag[$offset]);
  }

  public function offsetUnset($offset)
  {
    return;
  }

  public function offsetGet($offset)
  {
    return isset($this->bag[$offset]) ? $this->bag[$offset] : null;
  }

}
