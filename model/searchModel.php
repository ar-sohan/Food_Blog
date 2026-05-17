<?php

    require_once('db.php');

    // Returns restaurants whose name OR location OR area match the query,
    // further narrowed by optional location / area filters. All inputs are
    // bound as parameters - no string concatenation into the SQL.
    function searchRestaurants($q, $location, $area){
        $con = getConnection();
        $sql = "select id, name, location, area, short_background
                from restaurants where 1=1";
        $params = [];
        $types  = '';

        if($q !== ''){
            $sql     .= " and (name like ? or location like ? or area like ?)";
            $like     = '%' . $q . '%';
            $params[] = $like; $params[] = $like; $params[] = $like;
            $types   .= 'sss';
        }
        if($location !== ''){
            $sql     .= " and location like ?";
            $params[] = '%' . $location . '%';
            $types   .= 's';
        }
        if($area !== ''){
            $sql     .= " and area like ?";
            $params[] = '%' . $area . '%';
            $types   .= 's';
        }
        $sql .= " order by name asc limit 50";

        $stmt = mysqli_prepare($con, $sql);
        if($types !== ''){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    // Returns menu items whose name matches the query, narrowed by the
    // restaurant's location / area when those filters are supplied.
    function searchMenuItems($q, $location, $area){
        $con = getConnection();
        $sql = "select m.id, m.restaurant_id, m.name, m.description, m.price,
                       m.image_path, r.name as restaurant_name,
                       r.location as restaurant_location, r.area as restaurant_area
                from menu_items m
                join restaurants r on m.restaurant_id = r.id
                where 1=1";
        $params = [];
        $types  = '';

        if($q !== ''){
            $sql     .= " and (m.name like ? or m.description like ?)";
            $like     = '%' . $q . '%';
            $params[] = $like; $params[] = $like;
            $types   .= 'ss';
        }
        if($location !== ''){
            $sql     .= " and r.location like ?";
            $params[] = '%' . $location . '%';
            $types   .= 's';
        }
        if($area !== ''){
            $sql     .= " and r.area like ?";
            $params[] = '%' . $area . '%';
            $types   .= 's';
        }
        $sql .= " order by m.name asc limit 50";

        $stmt = mysqli_prepare($con, $sql);
        if($types !== ''){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

?>
