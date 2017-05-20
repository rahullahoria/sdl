<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 5/18/17
 * Time: 11:42 AM
 */

//function of this is to recode the lock status, if lock doesnot send this beat signal with in at ime frame,
//this mean that there can be a issue with the lock

//UPDATE `locks` SET `id`=[value-1],`manufacture_id`=[value-2],`otp`=[value-3],`user_id`=[value-4],`status`=[value-5],`last_ok`=[value-6],`creation`=[value-7],`last_update`=[value-8] WHERE 1

function updateStatus($lockID){

    $sqlUpdateStatus = "UPDATE `locks` SET
          `last_ok`=:last_ok
          WHERE id=:lockId
";

    try {
        $db = getDB();

        $stmt = $db->prepare($sqlUpdateStatus);

        $stmt->bindParam("id", $lockId);
        $stmt->bindParam("last_ok", date("Y-m-d H:i:s"));

        $stmt->execute();
        $db = null;
        echo '{"Commands": ' . json_encode(null) . '}';
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }


}