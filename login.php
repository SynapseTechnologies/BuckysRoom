<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');

if(isset($_POST['login_submit']))
{
    if( BuckysTracker::getLoginAttemps() >= MAX_LOGIN_ATTEMPT )
    {
        buckys_redirect('/register.php', MSG_EXCEED_MAX_LOGIN_ATTEMPS, MSG_TYPE_ERROR);
    }
    
    BuckysTracker::addTrack('login');
    //E-mail    
    if(!trim($_POST['email'])){
        $loginError = 1;        
        buckys_redirect('/register.php', MSG_EMPTY_EMAIL, MSG_TYPE_ERROR);
    }else if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST['email'])){
        buckys_redirect('/register.php', MSG_INVALID_EMAIL, MSG_TYPE_ERROR);
    }
    
    //Password
    if(empty($_POST['password'])){
        buckys_redirect('/register.php', MSG_EMPTY_PASSWORD, MSG_TYPE_ERROR);
    }
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $info = buckys_get_user_by_email($email);
    if(buckys_not_null($info))
    {
        if( !buckys_validate_password($password, $info['password']) ) //Password Incorrect
        {
            buckys_redirect('/register.php', MSG_INVALID_LOGIN_INFO, MSG_TYPE_ERROR);
        }else if($info['status'] == 0){ //Account Not Verified or Banned            
            buckys_redirect('/index.php', !$info['token'] ? MSG_ACCOUNT_BANNED : MSG_ACCOUNT_NOT_VERIFIED, MSG_TYPE_ERROR);
        }else{ //Login Success            
            //Clear Login Attemps
            BuckysTracker::clearLoginAttemps();
            
            $_SESSION['userID'] = $info['userID'];
            //Init Some Session Values
            $_SESSION['converation_list'] = array();
            
            //If the keep me signed in is checked, save data to cookie
            if ($_POST['keep_sign_in'] == 1) {
                setcookie('bkuid0', base64_encode($info['userID']), time() + COOKIE_LIFETIME, "/", "buckysroom.com");
                $uidEncrypted = buckys_encrypt_id($info['userID']);
                setcookie('bkuid1', base64_encode($uidEncrypted), time() + COOKIE_LIFETIME, "/", "buckysroom.com");
                setcookie('bkuid2', base64_encode($_SESSION['user_encrypt_salt']), time() + COOKIE_LIFETIME, "/", "buckysroom.com");
            }
            
            buckys_redirect('/account.php');
        }
    }else{ //Email Incorrect
        buckys_redirect('/register.php', MSG_INVALID_LOGIN_INFO, MSG_TYPE_ERROR);
    }        
}
