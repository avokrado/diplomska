<?php
/**
 * Created by PhpStorm.
 * User: Uros
 * Date: 26. 07. 2018
 * Time: 10:03
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';



$app = new \Slim\App;
require_once('../app/api/recept.php');





$app->run();