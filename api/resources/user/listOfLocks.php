<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 5/18/17
 * Time: 12:50 PM
 */

function listOfLocks($userId){

    $sql = "SELECT b.name,b.id,c.name
                from lock_user_mappings as a
                inner join locks as b
                inner join manufactures as c
                where a.user_id = :userId, a.lock_id = b.id, b.manufacture_id = c.id";

    try{
        $db = getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindParam("userId", $userId);

        $stmt->execute();
        $locks = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"locks": ' . json_encode($locks) . '}';
    }
    catch (Exception $e){
        echo '{"error":{"text":' . $e->getMessage() . '}}';

    }
}