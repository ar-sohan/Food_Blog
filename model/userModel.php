<?php

    require_once('db.php');

    
    // look for single user by email. returns the row as an assoc array, or NULL.
    function findUserByEmail($email){
        $con = getConnection();
        $sql = "select id, name, email, password_hash, role, profile_picture
                from users where email = ? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // look fora single user by id.
    function findUserById($id){
        $con = getConnection();
        $sql = "select id, name, email, password_hash, role, profile_picture
                from users where id = ? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // remember-me cookie lookup. this cookie carries a raw token; the DB stores
    // its sha256 hash so a leaked DB row can't be used to forge a cookie.
    function findUserByRememberToken($rawToken){
        $hash = hash('sha256', $rawToken);
        $con = getConnection();
        $sql = "select id, name, email, role from users where remember_token = ? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $hash);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // convenience helper for signup validation + AJAX uniqueness check.
    function emailExists($email){
        return findUserByEmail($email) !== null;
    }

    // Used during profile update to allow keeping the same email.
    function emailExistsForOtherUser($email, $excludeUserId){
        $con = getConnection();
        $sql = "select id from users where email = ? and id <> ? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $email, $excludeUserId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result) !== null;
    }

    

    // Insert a new user. Returns the new user id on success, 0 on failure.
    function addUser($user){
        $con = getConnection();
        $sql = "insert into users (name, email, password_hash, role)
                values (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        $hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $role = (isset($user['role']) && $user['role'] === 'admin') ? 'admin' : 'member';
        mysqli_stmt_bind_param($stmt, "ssss", $user['name'], $user['email'], $hash, $role);
        if(mysqli_stmt_execute($stmt)){
            return mysqli_insert_id($con);
        }
        return 0;
    }

    // Update the profile fields the user can edit. $profilePic = filename or NULL
    // to keep existing.
    function updateUserProfile($id, $name, $email, $profilePic = null){
        $con = getConnection();
        if($profilePic === null){
            $sql  = "update users set name = ?, email = ? where id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $id);
        } else {
            $sql  = "update users set name = ?, email = ?, profile_picture = ? where id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $profilePic, $id);
        }
        return mysqli_stmt_execute($stmt);
    }

    // Update the password hash (caller should have already verified the
    // current password and built the new hash with password_hash()).
    function updateUserPassword($id, $newPasswordHash){
        $con = getConnection();
        $sql = "update users set password_hash = ? where id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $newPasswordHash, $id);
        return mysqli_stmt_execute($stmt);
    }

    // Set/clear the Remember-Me token. $rawToken is the value sent in the cookie;
    // we store its sha256 hash.
    function setUserRememberToken($id, $rawToken){
        $hash = hash('sha256', $rawToken);
        $con = getConnection();
        $sql = "update users set remember_token = ? where id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $hash, $id);
        return mysqli_stmt_execute($stmt);
    }

    function clearUserRememberToken($id){
        $con = getConnection();
        $sql = "update users set remember_token = NULL where id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

?>
