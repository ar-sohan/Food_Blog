<?php 
require_once (__DIR__ . '/db.php');

//insertion
  function registerUser($user){
        $con = getConnection();
        $sql = "insert into users (name, email, password_hash, role) values ('{$user['name']}', '{$user['email']}', '{$user['password']}', '{$user['role']}')";
        if(mysqli_query($con, $sql)){
            return true;
        }else{
            return false;
        }
    }

    
function checkEmailExists($email){
        $con = getConnection();
        $sql = "select * from users where email='{$email}'";
        $result = mysqli_query($con, $sql);
        if(mysqli_num_rows($result) >= 1){
            return true;  //email takem already
        }else{
            return false; // evail can use 
        }
    }

    function loginUser($user){
        $con = getConnection();
        $sql = "select * from users where email='{$user['email']}'";
        $result = mysqli_query($con, $sql);
        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);
            //hassed password
            if(password_verify($user['password'], $row['password_hash'])){
                return $row;  
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function getUserById($id){
        $con = getConnection();
        $sql = "  select * from users where id='{$id}'";
        $result = mysqli_query($con, $sql);

        if(mysqli_num_rows($result) == 1){
            return mysqli_fetch_assoc($result);
        }else{
            return false;
        }
    }

    function updateUser($user){ 
        $con = getConnection();
        $sql = "  update users set name='{$user['name']}', email='{$user['email']}' where id='{$user['id']}'";
            if (mysqli_query($con, $sql)){
                return true;
            }else{
                return false;
            }
        }

 function updatePassword($user){
        $con = getConnection();
         $hashed= password_hash($user["password"], PASSWORD_DEFAULT);
        $sql = "update users set password_hash='{$hashed}' where id='{$user['id']}'";
            if (mysqli_query($con, $sql)){
                return true;
            }else{
                return false;
            }
        }

        function saveRememberToken($user){
            $con = getConnection();
            $sql = "update users set remember_token='{$user['remember_token']}' where id='{$user['id']}'";
                if (mysqli_query($con, $sql)){
                    return true;
                }else{
                    return false;
                }
            }

            function getUserByToken($token){
                $con = getConnection();
                $sql = " select * from users where remember_token = '{$token}'";
                $result = mysqli_query($con, $sql);
                if (mysqli_num_rows($result) == 1){
                        return mysqli_fetch_assoc($result);
                    }else {
                        return false;
                    }
            }
            function clearRememberToken($id){ 
                $con = getConnection();
                $sql = "update users set remember_token = null where id = '{$id}'";
                mysqli_query($con,$sql);
            }




?>
