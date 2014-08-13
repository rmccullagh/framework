<?php namespace Como\Http;

class Request
{
  public $route;

  public $params = array();

  public $query  = array();

  public $body     = array();

  private $headers = array();

  private $server = array();

  public $ip;

  public $host;

  public $xhr = false;

  public $originalUrl;

  public function __construct($route, $query, $body, $server)
  {
    $this->route       = $route;
    $this->params      = $route['params'];
    $this->query       = $query;
    $this->body        = $body;
    $this->server      = $server;
    $this->ip          = $server['REMOTE_ADDR'];
    $this->host        = $server['HTTP_HOST'];
    $this->originalUrl = $server['REQUEST_URI'];
    $this->extractHttpHeaders();

    if($this->get('x-requested-with') === 'XMLHttpRequest')
    {
      $this->xhr = true;
    }

  }
 
  protected function extractHttpHeaders()
  {
    foreach($this->server as $key => $value)
    {
      if(substr($key, 0, 4) === 'HTTP')
      {
        $field = strtolower(str_replace('_', '-', substr($key, 5)));
        $this->headers[$field] = $value;

      }

    }
  } 
 
  public function get($field)
  {
    $field = strtolower($field);
    return isset($this->headers[$field]) ? $this->headers[$field] : null;
  } 

  public function header($field)
  {
    return $this->get($field);

  }

  public function param($name)
  {
    if(isset($this->params[$name])) 
    {
      return $this->params[$name];
    }
    if(isset($this->body[$name]))
    {
      return $this->body[$name];
    }
    if(isset($this->query[$name]))
    {
      return $this->query[$name];
    }
    return null; 
  }
}
