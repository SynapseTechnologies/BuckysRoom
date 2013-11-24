<?php
require(dirname(__FILE__) . '/includes/bootstrap.php');
require_once(DIR_FS_FUNCTIONS . 'recaptcha.php');

//Getting Current User ID
$userID = buckys_is_logged_in();

//If the parameter is null, goto homepage 
if($userID)
    buckys_redirect('/account.php');

if( isset($_GET['action']) && $_GET['action'] == 'verify' )
{
    $token = trim($_GET['token']);
    $email = trim($_GET['email']);
    if(!$token || !$email)
    {
        buckys_redirect("/index.php", MSG_INVALID_REQUEST, MSG_TYPE_ERROR);
    }
    BuckysUser::verifyAccount($email, $token);
    buckys_redirect("/index.php");
}
    
if( isset($_POST['action']) && $_POST['action'] == 'create-account' )
{
    //Check Captcha
    $resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,
                            $_SERVER["REMOTE_ADDR"],
                            $_POST["recaptcha_challenge_field"],
                            $_POST["recaptcha_response_field"]);
    if($resp->is_valid)
    {
        //Create New Account
        $newID = BuckysUser::createNewAccount($_POST);    
        render_result_xml(array(
            'status' => !$newID  ? 'error' : 'success',
            'message' => !$newID ? buckys_get_messages() : MSG_NEW_ACCOUNT_CREATED
        ));
    }else{
        render_result_xml(array(
            'status' => 'error',
            'message' => '<p class="message error">' . ($resp->error == 'incorrect-captcha-sol' ? 'The captcha input is not correct!' : $resp->error) . '</p>'
        ));
    }
    
    exit;
}else if( isset($_POST['action']) && $_POST['action'] == 'reset-password' ){
    BuckysUser::resetPassword($_POST['email']);
}

$showForgotPwdForm = isset($_GET['forgotpwd']) && $_GET['forgotpwd'];

buckys_enqueue_stylesheet('register.css');

buckys_enqueue_javascript('register.js');

$BUCKYS_GLOBALS['content'] = 'register';

$BUCKYS_GLOBALS['title'] =  'Register - BuckysRoom';

require(DIR_FS_TEMPLATE . $BUCKYS_GLOBALS['template'] . "/" . $BUCKYS_GLOBALS['layout'] . ".php");  
