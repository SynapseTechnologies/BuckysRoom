<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>
<section id="main_section">
    <section id="wrapper">
        <div id="register-wrapper">               
            <?php render_result_messages(); ?>        
            <div id="new-account">                                                
                <h2 class="titles">Registration</h2>
                <form name="newaccount" method="post" action="" id="newaccount">
                    <div class="row">
                        <label>First Name:</label>
                        <input type="text" name="firstName" id="firstName" maxlength="30" value="" autocomplete="off" class="input" />
                    </div>
                    <div class="row">
                        <label>Last Name:</label>
                        <input type="text" name="lastName" id="lastName" maxlength="60" value="" autocomplete="off" class="input" />
                    </div>
                    <div class="row">
                        <label>E-mail:</label>
                        <input type="text" name="email" id="email" maxlength="60" value="" autocomplete="off" class="input" />
                    </div>
                    <div class="row">
                        <label>Password:</label>
                        <input type="password" name="password" id="password" maxlength="60" value="" autocomplete="off" class="input" />
                    </div>
                    <div class="row">
                        <label>Confirm Password:</label>
                        <input type="password" name="password2" id="password2" maxlength="60" value="" autocomplete="off" class="input" />
                    </div>
                    <div class="row captcha-row">                        
                        <?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY); ?>
                        <div class="clear"></div>
                    </div>
                    <div class="row">				<label></label>
                        <input class="redButton" value="Register" type="submit" />
                    </div>
                    <?php render_loading_wrapper(); ?>
                </form>		
            </div>
            <div id="login-wrap">
                <h2 class="titles">Login</h2>
                <form id="loginform" action="/login.php" method="post" <?php echo $showForgotPwdForm ? 'style="display: none"' : ''?>> 
                    <div class="row">
                        <label for="email">E-mail:</label>
                        <input type="text" class="input" maxlength="60" name="email" id="email" />
                    </div>
                    <div class="row">
                        <label for="password">Password:</label>
                        <input type="password" class="input" maxlength="20" name="password" id="password" />
                    </div>
                    <div class="row checkbox-row">
                        <label>
                        <input type="checkbox" name="keep_sign_in" id="keep_sign_in" value="1" />
                        Keep me Signed In
                        </label>
                    </div>
                    <div class="row">
                        <label></label>
                        <input type="submit" value="Log In" class="redButton" name="login_submit">
                         or <a href="/register.php#forgotpwdform" class="goto-forgotpwdform">Forgot Password?</a>
                    </div>
                </form>
                <form id="forgotpwdform" action="/register.php" method="post" <?php echo !$showForgotPwdForm ? 'style="display: none"' : ''?>>
                    <div class="row">
                        <label for="email">E-mail:</label>
                        <input type="text" class="input" maxlength="60" name="email" id="email" />
                    </div>
                    <div class="row">
                        <label></label>
                        <input type="submit" value="Reset Password" class="redButton" />
                         or <a href="/register.php#loginform" class="goto-loginform">Login Now?</a>
                    </div>
                    <input type="hidden" name="action" value="reset-password" />
                </form>
            </div>
            <div class="clear"></div>
        </div>
    </section>
</section>