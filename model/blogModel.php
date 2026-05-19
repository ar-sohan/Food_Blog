<?php

    require_once('db.php');
    $lastBlogDbError = '';

    function ensureBlogImageColumn(){
        static $checked = false;
        if($checked){ return; }
        $checked = true;

        $con = getConnection();
        $result = mysqli_query($con, "show columns from food_experience_posts like 'image_path'");
        if($result && mysqli_num_rows($result) === 0){
            mysqli_query($con, "alter table food_experience_posts add image_path varchar(255) default null after content");
        }
    }

    function getAllBlogPosts(){
        ensureBlogImageColumn();
        $con = getConnection();
        $sql = "select p.id, p.title, p.content, p.image_path, p.post_type, p.created_at, u.name as author_name
                from food_experience_posts p
                join users u on p.user_id = u.id
                order by p.created_at desc";
        $result = mysqli_query($con, $sql);
        $rows = [];
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    function getBlogPostById($id){
        ensureBlogImageColumn();
        $con = getConnection();
        $sql = "select p.id, p.user_id, p.title, p.content, p.image_path, p.post_type, p.created_at, p.updated_at,
                       u.name as author_name
                from food_experience_posts p
                join users u on p.user_id = u.id
                where p.id = ? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    function createBlogPost($post){
        ensureBlogImageColumn();
        $con = getConnection();
        $sql = "insert into food_experience_posts (user_id, title, content, image_path, post_type)
                values (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "issss",
            $post['user_id'], $post['title'], $post['content'], $post['image_path'], $post['post_type']
        );
        if(mysqli_stmt_execute($stmt)){
            return mysqli_insert_id($con);
        }
        return 0;
    }

    function updateBlogPost($id, $post){
        ensureBlogImageColumn();
        $con = getConnection();
        if($post['image_path'] === null){
            $sql = "update food_experience_posts
                    set title = ?, content = ?, post_type = ?
                    where id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "sssi",
                $post['title'], $post['content'], $post['post_type'], $id
            );
        } else {
            $sql = "update food_experience_posts
                    set title = ?, content = ?, image_path = ?, post_type = ?
                    where id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ssssi",
                $post['title'], $post['content'], $post['image_path'], $post['post_type'], $id
            );
        }
        return mysqli_stmt_execute($stmt);
    }

    function getLastBlogDbError(){
        global $lastBlogDbError;
        return $lastBlogDbError;
    }

    function deleteBlogPost($id){
        global $lastBlogDbError;
        $lastBlogDbError = '';
        $con = getConnection();
        $id = (int)$id;

        $commentTable = mysqli_query($con, "show tables like 'food_experience_comments'");
        if($commentTable && mysqli_num_rows($commentTable) > 0){
            if(!mysqli_query($con, "delete from food_experience_comments where post_id = " . $id)){
                $lastBlogDbError = mysqli_error($con);
                return false;
            }
        }

        if(!mysqli_query($con, "delete from food_experience_posts where id = " . $id . " limit 1")){
            $lastBlogDbError = mysqli_error($con);
            return false;
        }

        return mysqli_affected_rows($con) > 0;
    }

?>
