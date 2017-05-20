<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 5/20/17
 * Time: 3:14 PM
 */

function createAds(){

    $request = \Slim\Slim::getInstance()->request();


    $ads = json_decode($request->getBody());
    if (is_null($ads)){
        echo '{"error":{"text":"Invalid Json"}}';
        die();
    }

    $sqlUpdateStatus = "INSERT INTO `ads`
      (`vedio_id`, `gender`, `age_lower`, `age_higher`,`area`,`city`,`km`, `creation`,`status`) VALUES
      (:videoId,:gender,:ageLower,:ageHigher, :area, :city, :km, :creation,:status)
";

    try {
        $db = getDB();

        $stmt = $db->prepare($sqlUpdateStatus);

        $status = "active";
        $stmt->bindParam("videoId", $ads->videoId);
        $stmt->bindParam("gender", $ads->gender);
        $stmt->bindParam("ageLower", $ads->age_lower);
        $stmt->bindParam("ageHigher", $ads->age_higher);
        $stmt->bindParam("area", $ads->area);
        $stmt->bindParam("city", $ads->city);
        $stmt->bindParam("km", $ads->km);
        $stmt->bindParam("creation", date("Y-m-d H:i:s"));
        $stmt->bindParam("status", $status);

        $stmt->execute();
        $ads->id = $db->lastInsertId();

        $db = null;
        echo '{"ads": ' . json_encode($ads) . '}';
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }


}