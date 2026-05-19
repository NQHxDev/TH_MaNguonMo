<?php

// class DefaultController extends BaseController
// {
//    public function index()
//    {

//       $isLocal = ($_SERVER['HTTP_HOST'] === '[IP_ADDRESS]' || $_SERVER['HTTP_HOST'] === 'localhost');
//       $serverInfo = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown server';
//       $phpVersion = PHP_VERSION;

//       $data = compact('isLocal', 'serverInfo', 'phpVersion');
//       $this->view('default', $data);
//    }
// }

class DefaultController {

   public function index(){
      echo "Hello World!";
   }
}
