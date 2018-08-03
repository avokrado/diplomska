<?php
/**
 * Created by PhpStorm.
 * User: Uros
 * Date: 26. 07. 2018
 * Time: 10:46
 */
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


$app->get('/api/randomRecept', function (ServerRequestInterface $request, ResponseInterface $response) {

    $client = new MongoDB\Client;
    $db = $client->recepti;
    $recepti = $db->recepti;

    $randomRecept = [];
    //$mongoQuery = array('$sample' => array('size'=> 1));
    $result = $recepti->aggregate(array(
        array('$sample' =>  array(
            'size' => 1
        ))
    ));

    foreach ($result as $recept) {
        array_push($randomRecept, $recept);
    }

    $res = $response->withJSON($randomRecept,200,JSON_UNESCAPED_UNICODE);

    return $res;
});





$app->post('/api/recepti', function (ServerRequestInterface $request, ResponseInterface $response) {

    //pridobi sestavine iz request bodyja
    $array_sestavin = array();
    $json = $request->getBody();
    $data = json_decode($json, true);
    foreach ($data['sestavine'] as $sestavina) {
        array_push($array_sestavin, $sestavina);
    }


    $queryResponse = MongoQuery( $array_sestavin );


    $res = $response->withJSON($queryResponse,200,JSON_UNESCAPED_UNICODE);

    return $res;
});


function MongoQuery( $array_sestavin ) {
    if(count($array_sestavin) === 3) {
        $mongoQuery =
            array('$or' => array(
                array('$and' => array(
                    array('sestavine' => array('$elemMatch' => array('sestavina'=> $array_sestavin[0]))),
                    array('sestavine' => array('$elemMatch' => array('sestavina' => $array_sestavin[1]))),
                    array('sestavine' => array('$elemMatch' => array('sestavina' => $array_sestavin[2])))
                )),
                array('$and' => array(
                    array( 'sestavine' => array('$elemMatch' => array('sestavina'=> $array_sestavin[0]))),
                    array('sestavine' => array('$elemMatch' => array('sestavina' => $array_sestavin[1])))
                )),
                array('sestavine' => array('$elemMatch' => array('sestavina'=> $array_sestavin[0])))
            ));
    } else if (count($array_sestavin) == 2) {
        $mongoQuery =
            array('$or' => array(
                array('$and' => array(
                    array('sestavine' => array('$elemMatch' => array('sestavina'=> $array_sestavin[0]))),
                    array('sestavine' => array('$elemMatch' => array('sestavina' => $array_sestavin[1])))
                )),
                array('sestavine' => array('$elemMatch' => array('sestavina'=> $array_sestavin[0])))
            ));
    } else if ( count($array_sestavin) == 1) {
        $mongoQuery = array('sestavine' => array('$elemMatch' => array('sestavina'=> $array_sestavin[0])));
    }


    $client = new MongoDB\Client;
    $db = $client->recepti;
    $recepti = $db->recepti;


    $response = $recepti->find($mongoQuery);
    $receptiArray = [];

    foreach ($response as $recept) {
        array_push($receptiArray, $recept);
    }

    return $receptiArray;
}