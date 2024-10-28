<?php

session_start();

if(isset($_SESSION['account'])){
    if(!$_SESSION['account']['is_staff']){
        header('location: login.php');
    }
}else{
    header('location: login.php');
}

require_once('../tools/functions.php');
require_once('../classes/account.class.php');
$accountObj = new Account();

$first_name = $last_name = $username = $password =$confirmpassword = $role = '';
$first_nameErr = $last_nameErr = $usernameErr = $passwordErr = $confirmpasswordErr = $roleErr = '';


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $confirmpassword = clean_input($_POST['confirmpassword']);
    $role = clean_input($_POST['role']);
    
    if(empty($first_name)){
        $first_nameErr = 'First name is required.';
    }

    if(empty($last_name)){
        $last_nameErr = 'Last name is required.';
    }

    if(empty($username)){
        $usernameErr = 'Username is required.';
    }else{
        if($accountObj->usernameExist($username,"")){
            $usernameErr = 'Username Already Exist.';
        }
    }

    if(empty($password)){
        $passwordErr = 'Password is required.';
    }else if(strlen($password) < 8){
        $passwordErr = 'Password must be 8 characters and above.';
    }

    if(empty($confirmpassword)){
        $confirmpasswordErr = 'Confirm Password is required.';
    }else if($password != $confirmpassword){
        $confirmpasswordErr = 'Passwords do not match.';
    }

    if(empty($role)){
        $roleErr = 'Role is required.';
    }


    // If there are validation errors, return them as JSON
    if(!empty($first_nameErr) || !empty($last_nameErr) || !empty($usernameErr) || !empty($passwordErr) || !empty($confirmpasswordErr) || !empty(($roleErr))){
        echo json_encode([
            'status' => 'error',
            'first_nameErr' => $first_nameErr,
            'last_nameErr' => $last_nameErr,
            'usernameErr' => $usernameErr,
            'passwordErr' => $passwordErr,
            'confirmpasswordErr' => $confirmpasswordErr,
            'roleErr' => $roleErr
        ]);
        exit;
    }

    if(empty($first_nameErr) && empty($last_nameErr) && empty($usernameErr) && empty($passwordErr) && empty($confirmpasswordErr) && empty($roleErr)){
        $accountObj->first_name = $first_name;
        $accountObj->last_name = $last_name;
        $accountObj->username = $username;
        $accountObj->password = $password;
        $accountObj->role = $role;
        if($role == "admin"){
            $accountObj->is_admin = true;
        }

        if($accountObj->add()){
            echo json_encode(['status' => 'success']);
    
        exit;
        }
    }
}

