<?php namespace Como\App;

use Closure;
use Como\Routing\Collection;
use Como\Routing\Parameter;
use Como\Response\Response;
use Como\App\Middleware;

use Como\Log\Log;

class Application
{
  /*
   * @var Como\Routing\Colletion
   */
  private $route_collection;

  /*
   * @var Como\Routing\Parameter
   */
  private $route_parameters;


  public function __construct()
  {
    $this->route_collection = Collection::getInstance();
    $this->route_parameters = Parameter::getInstance();
    $this->middleware = new Middleware();
  }
 
  /**
   * This method collects information about routes
   * It takes a URI as arg 1, then the callback as last arg
   * It is then pushed onto the Routing stack
   * This method only registers routes.
   * The applicaiton must call app::run() to invoke it
   */ 
  public function get()
  {
    $arguments = func_get_args();
    
    $uri = array_shift($arguments);
  
    $closure = array_pop($arguments);
   
    $this->route_collection->push("GET", $uri, $closure);  
    
  }
  
  public function using(Closure $callback)
  {
    $this->middleware->push($callback);

  }

  public function param($key, $value)
  {
    if(isset($key))
    {
      if(strlen($key) > 1)
      {
        if(substr($key, 0, 1) === ':')
        {
          $this->route_parameters[$key] = $value;
        }
      }
    }
  }
 
  public function routes()
  {
    return $this->route_collection;
  }
  
  public function run()
  {
    $routes = $this->route_collection->all();
    $request_uri = $_SERVER['REQUEST_URI'];
    
    $request_uri = preg_replace('/(\?.*)/','', $request_uri);

    $request_uri_split = explode('/', $request_uri);
     
    array_shift($request_uri_split);
    $request_uri_segment_length = count($request_uri_split);

    while($route = array_shift($routes['GET']))
    {
      
      $uri_segments = explode('/', $route['uri']);
      
      array_shift($uri_segments);
  
      $uri_segments_length = count($uri_segments);
    
      /*
       * Handle default route '/'
       */ 
      if($route['uri'] == '/' && count($request_uri_split) === 1 && $request_uri_split[0] === '')
      {
        $request_route = array(
          "path"    => $route['uri'],
          "params"  => array()
        );
        
        $request = new \Como\Http\Request($request_route, $_GET, $_POST, $_SERVER);
        $response = new \Como\Http\Response();
 
        foreach($this->middleware->all() as $key => $value)
        {
          $value($request, $response);
        }
        
        $route['callback']($request, $response);
        
        return;
      }
     
      if($uri_segments_length === $request_uri_segment_length)
      { 
        $all_good = false;
        $resolved_params = array();

        foreach($uri_segments as $key => $value)
        {
          if(isset($this->route_parameters[$value]))
          {

            if(preg_match($this->route_parameters[$value], $request_uri_split[$key]))
            {
           
              $param_name = str_replace(':', '', $value);
 
              if(! isset($resolved_params[$param_name]))
              {
                $resolved_params[str_replace(':','', $value)] = $request_uri_split[$key];
              }
              else  
              {
                $tmp_param_name = $resolved_params[$param_name];                

                unset($resolved_params[$param_name]);
                $resolved_params[$param_name] = array();
 
                array_push($resolved_params[$param_name], $tmp_param_name);
                array_push($resolved_params[$param_name], $request_uri_split[$key]);

              }

              $all_good = true;
            
            } 
            else
            {
              $all_good = false;
            }
          
          }     
   
        }
        if(true === $all_good) 
        {
          $request_route = array(
            "path"    => $route['uri'],
            "params"  => $resolved_params
          );
          $request = new \Como\Http\Request($request_route, $_GET, $_POST, $_SERVER);
          $response = new \Como\Http\Response;

          foreach($this->middleware->all() as $key => $value)
          {
            $value($request, $response);
          }
          
          $route['callback']($request, $response);

          return;

        }
      }
       
      if($route['uri'] == $request_uri)
      {
          $request_route = array(
            "path"    => $route['uri'],
            "params"  => array()
          );
          $request = new \Como\Http\Request($request_route, $_GET, $_POST, $_SERVER);

          Log::debug("reached default route test case - " . $route['uri']);

          $route['callback']($request, new \Como\Http\Response());

        return;
      }

    }
    
    $response = new \Como\Http\Response();
    $response->send(404);
  }
  
}
