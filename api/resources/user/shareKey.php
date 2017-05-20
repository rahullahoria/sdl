<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 5/18/17
 * Time: 1:06 PM
 */

function shareKey($userId,$lockId){

    $request = \Slim\Slim::getInstance()->request();

    $requestJson = json_decode($request->getBody());


    $sql = "INSERT INTO `users`
                (
                `full_name`,
                `mobile`,
                `otp`
                )
                VALUES (
                    :full_name,
                    :mobile,
                    :otp
                    );";

    $sqlAddKey = "INSERT INTO `lock_user_mappings`
        (`lock_id`, `user_id`, `upto`, `creation`)
        VALUES (:lockId,:userId,:upto,:creation)";

    try{
        $db = getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindParam("full_name", $requestJson->full_name);
        $stmt->bindParam("mobile", $requestJson->mobile);
        $otp = getOTP();
        $stmt->bindParam("otp", $otp);

        $stmt->execute();
        $newUserId = $db->lastInsertId();

        if($newUserId){

            $stmt = $db->prepare($sqlAddKey);

            $stmt->bindParam("lockId", $lockId);
            $stmt->bindParam("userId", $newUserId);
            $stmt->bindParam("upto", $requestJson->upto);
            $stmt->bindParam("creation", date("Y-m-d H:i:s"));

            $stmt->execute();

            $message =
                "LockId: $lockId, otp: \n " . $otp;
            sendSMS($requestJson->mobile, $message);

        }

        $db = null;
        echo '{"locks": ' . json_encode($locks) . '}';
    }
    catch (Exception $e){
        echo '{"error":{"text":' . $e->getMessage() . '}}';

    }


}