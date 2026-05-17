<?php

    require_once('db.php');

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

    function countRestaurants(){
        $con = getConnection();
        $result = mysqli_query($con, "select count(*) as c from restaurants");
        $row = mysqli_fetch_assoc($result);
        return (int)$row['c'];
    }

    function createRestaurant($r){
        $con = getConnection();
        $sql = "insert into restaurants (name, location, area, short_background, goals)
                values (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssss",
            $r['name'], $r['location'], $r['area'],
            $r['short_background'], $r['goals']
        );
        if(mysqli_stmt_execute($stmt)){
            return mysqli_insert_id($con);
        }
        return 0;
    }

    function updateRestaurant($id, $r){
        $con = getConnection();
        $sql = "update restaurants
                set name = ?, location = ?, area = ?, short_background = ?, goals = ?
                where id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi",
            $r['name'], $r['location'], $r['area'],
            $r['short_background'], $r['goals'], $id
        );
        return mysqli_stmt_execute($stmt);
    }

    function deleteRestaurant($id){
        $con = getConnection();
        $sql = "delete from restaurants where id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

?>
