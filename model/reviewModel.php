<?php

    require_once('db.php');

    // ----- READ ---------------------------------------------------------

    // Returns every review on a menu item, newest first, with the author's name.
    function getReviewsForMenuItem($menuItemId){
        $con = getConnection();
        $sql = "select r.id, r.user_id, r.comment, r.created_at, u.name as user_name
                from reviews r
                join users u on r.user_id = u.id
                where r.menu_item_id = ?
                order by r.created_at desc";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $menuItemId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    function findReviewById($id){
        $con = getConnection();
        $sql = "select id, menu_item_id, user_id, comment, created_at
                from reviews where id = ? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // ----- WRITE --------------------------------------------------------

    // Insert a review. Returns an assoc array for the newly created row (with
    // user_name joined in) so the AJAX response can append it to the page
    // without a refetch. Returns NULL on failure.
    function addReview($menuItemId, $userId, $comment){
        $con = getConnection();
        $sql = "insert into reviews (menu_item_id, user_id, comment)
                values (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $menuItemId, $userId, $comment);
        if(!mysqli_stmt_execute($stmt)){
            return null;
        }
        $newId = mysqli_insert_id($con);

        $sql2 = "select r.id, r.user_id, r.comment, r.created_at, u.name as user_name
                 from reviews r join users u on r.user_id = u.id
                 where r.id = ? limit 1";
        $stmt2 = mysqli_prepare($con, $sql2);
        mysqli_stmt_bind_param($stmt2, "i", $newId);
        mysqli_stmt_execute($stmt2);
        $result = mysqli_stmt_get_result($stmt2);
        return mysqli_fetch_assoc($result);
    }

    // Owner-scoped delete: only succeeds if the review belongs to this user.
    // Returns true if a row was actually deleted, false otherwise.
    function deleteReviewByIdAndUser($id, $userId){
        $con = getConnection();
        $sql = "delete from reviews where id = ? and user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $id, $userId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }
?>