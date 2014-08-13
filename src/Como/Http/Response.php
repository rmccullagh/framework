<?php namespace Como\Http;

use Como\Http\ResponseCodes;

class Response
{
  private $status_code = 200;
  private $headers = array();
  private $type = 'text/plain';
 
  /*
   * @var Como\Http\Codes
   */
  private $response_codes;

  public function __construct()
  {
    $this->response_codes = new ResponseCodes();
 
  }

  public function status($status)
  {
    if(isset($this->response_codes[$status]))
    {
      http_response_code($status);
    }

    return $this;
  }

  public function set($data, $value = null)
  { 
    if(is_array($data))
    {
      foreach($data as $key => $value)
      {
        $this->headers[$key] = $value;
      }
    }
    else
    {
      $this->headers[$data] = $value;    
    }
    
    return $this;
  }
  
  public function get($header)
  { 
    if(isset($this->headers[$header]))
    {
      return $this->headers[$header];
    }
    return null;
  }
  
  public function cookie()
  {
    return $this;
  }

  public function clearCookie()
  {
    return $this;
  }
  
  public function redirect()
  {
    $args = func_get_args();
    $length = count($args);
  
    if($length == 2)
    {
      http_response_code($args[0]);
      $location = $args[1];
      header("Location: $location");
      exit;
    }
    if($length == 1)
    {
      http_response_code(302);
      $location = $args[1];
      header("Location: $location");
      exit;

    }
  }

  public function location($path)
  {
  }
  
  /*
   * res.send([body|status], [body])
   */
  public function send()
  {
    $args = func_get_args();
    $length = count($args);
    if($length === 1)
    {
      if(is_string($args[0]))
      {
        $this->set('Content-Type', 'text/html');
        $this->sendHeaders();
        echo $args[0];
      }
      if(is_array($args[0]))
      {
        $this->set('Content-Type', 'application/json');
        $this->sendHeaders();
        echo json_encode($args[0], JSON_PRETTY_PRINT);
      }
      if(is_int($args[0]))
      {
        if(isset($this->response_codes[$args[0]]))
        {
          http_response_code($args[0]);
          $this->sendHeaders();
          echo $this->response_codes[$args[0]];
        } else {
          http_response_code(200);
          $this->sendHeaders();
          echo "OK";
        }
      }
    }
    if($length === 2)
    {
      if(is_int($args[0]))
      {
        http_response_code($args[0]);
  
      }
      else 
      {
        http_response_code(200);

      }
      if(is_string($args[1]))
      {
        $this->set('Content-Type', 'text/html');
        $this->sendHeaders();
        echo $args[1];

      }
      if(is_array($args[1]))
      {
        $this->set('Content-Type', 'application/json');
        $this->sendHeaders();
        echo json_encode($args[1], JSON_PRETTY_PRINT);

      }
    }
  }
  
  public function type($type)
  {
    $this->set('Content-Type', $type);
    return $this;
  }
  
  /*
   * res.render(view, [locals], callback)
   */
  public function render()
  {


  }
  private function sendHeaders()
  {
    foreach($this->headers as $key => $value)
    {   
      header("$key: $value");
    }
  }
}
