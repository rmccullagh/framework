<?php namespace Como\App;

class app extends Facade
{
  public static function getRealImplementerName()
  {
    
    return 'Como\App\Application';

  }

}
