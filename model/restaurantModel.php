<?php

    require_once('db.php');

    // Read-only helpers needed by Task 1 (browse). Task 2 will extend this file
    // with create/update/delete functions.

    function getAllRestaurants(){
        $con = getConnection();
        $sql = "select id, name, location, area, short_background, goals, created_at
                from restaurants order by name asc";
        $result = mysqli_query($con, $sql);
        $rows = [];
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    function getRestaurantById($id){
        $con = getConnection();
        $sql = "select id, name, location, area, short_background, goals, created_at
                from restaurants where id = ? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

?>
