<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 5/18/17
 * Time: 11:41 AM
 */

//there could be 2 cases
// this user is first user
// this user is share key user.
function regUser(){
    $request = \Slim\Slim::getInstance()->request();


    $user = json_decode($request->getBody());
    if (is_null($user)){
        echo '{"error":{"text":"Invalid Json"}}';
        die();
    }


    $sql = "INSERT INTO `users`(`full_name`, `mobile`, `dob`, `gender`, `otp`, `status`, `creation`, `last_login`, `router_id`)
              VALUES (fullName, mobile, dob, gender, otp, status, creation, lastLogin, routerId );";

    $sqlGetUser = "SELECT  `mobile`
                      FROM `users` WHERE id=:userId;";

    $sqlRegUser = "SELECT * FROM USERS WHERE mobile = :mobile";

    $updateUser = "UPDATE `users` SET
                      `full_name`=:full_name,
                      `password`=:password,
                      `pin`=:pin,
                      `email`=:pin,
                      `status`=:status
                  WHERE id=:id";

    try {
        $db = getDB();

        $stmt = $db->prepare($sqlGetUser);
        //$service_provider->status = "new";

        $stmt->bindParam("id", $user -> userId);

        $stmt->execute();

        $user = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($user[0]->user_id == 0){
            if($locks[0]->otp == $user->otp ){

                $stmt = $db->prepare($sql);
                $status = "active";
                //$service_provider->status = "new";

                $stmt->bindParam("full_name", $user->full_name);
                $stmt->bindParam("mobile", $user->mobile);
                $stmt->bindParam("password", $user->password);
                $stmt->bindParam("pin", $user->pin);
                $stmt->bindParam("email", $user->email);
                $stmt->bindParam("status", $status);

                $stmt->execute();

                $user->id = $db->lastInsertId();


            }else {
                echo '{"error":{"text":"unauthorized access"}}';
            }

        }else {
            $stmt = $db->prepare($sqlRegUser);
            //$service_provider->status = "new";

            $stmt->bindParam("mobile", $user->mobile);

            $stmt->execute();

            $regUser = $stmt->fetchAll(PDO::FETCH_OBJ);

            if($regUser[0]->otp == $user->otp ){

                $stmt = $db->prepare($updateUser);
                $status = "active";
                //$service_provider->status = "new";
                $stmt->bindParam("id", $regUser[0]->id);

                $stmt->bindParam("full_name", $user->full_name);
                $stmt->bindParam("password", $user->password);
                $stmt->bindParam("pin", $user->pin);
                $stmt->bindParam("email", $user->email);
                $stmt->bindParam("status", $status);

                $stmt->execute();

                $user->id = $regUser[0]->id;


            }else {
                echo '{"error":{"text":"unauthorized access"}}';
            }

        }


        $db = null;
        echo '{"user": ' . json_encode($user) . '}';
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}