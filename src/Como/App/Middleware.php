<?php namespace Como\App;

use Closure;

class Middleware
{
  private $methods = array();
  
  public function push(Closure $callback)
  {
    $this->methods[] = $callback;

  }
  
  public function all()
  {
    return $this->methods;

  }
}
