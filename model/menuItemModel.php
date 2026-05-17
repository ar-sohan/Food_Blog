<?php

    require_once('db.php');

    function countMenuItems(){
        $con = getConnection();
        $result = mysqli_query($con, "select count(*) as c from menu_items");
        $row = mysqli_fetch_assoc($result);
        return (int)$row['c'];
    }

    function createMenuItem($m){
        $con = getConnection();
        $sql = "insert into menu_items (restaurant_id, name, description, price, image_path)
                values (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "issds",
            $m['restaurant_id'], $m['name'], $m['description'],
            $m['price'], $m['image_path']
        );
        if(mysqli_stmt_execute($stmt)){
            return mysqli_insert_id($con);
        }
        return 0;
    }

    function updateMenuItem($id, $m){
        $con = getConnection();
        if($m['image_path'] === null){
            $sql = "update menu_items
                    set name = ?, description = ?, price = ?
                    where id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ssdi",
                $m['name'], $m['description'], $m['price'], $id
            );
        } else {
            $sql = "update menu_items
                    set name = ?, description = ?, price = ?, image_path = ?
                    where id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ssdsi",
                $m['name'], $m['description'], $m['price'],
                $m['image_path'], $id
            );
        }
        return mysqli_stmt_execute($stmt);
    }

    function deleteMenuItem($id){
        $con = getConnection();
        $sql = "delete from menu_items where id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
?>