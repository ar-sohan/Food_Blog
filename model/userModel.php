<?php

    require_once('db.php');

    // Look up a single user by email. Returns the row as an assoc array,
    // or NULL if no match. Uses a prepared statement (no string concat).
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

    // Convenience helper for the registration form + AJAX uniqueness check.
    function emailExists($email){
        return findUserByEmail($email) !== null;
    }

    // Insert a new user. Expects $user = ['name'=>, 'email'=>, 'password'=>, 'role'=>].
    // Hashes the password with password_hash() before storing. Returns the new
    // user id on success, 0 on failure.
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
function loginUser($email, $password){
    $user = findUserByEmail($email);
    if($user && password_verify($password, $user['password_hash'])){
        return $user;
    }
    return null;
}
?>
