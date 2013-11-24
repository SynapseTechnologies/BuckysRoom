<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}

$tradeNotifyInfo = $view['trade_user_info'];
if (!$tradeNotifyInfo)
    $tradeNotifyInfo = array();

?>
<section id="main_section">
    <?php buckys_get_panel('account_links'); ?>    
    <section id="right_side" class="floatright">    
        <section id="right_side_padding" class="user-info-section">
            <h2 class="titles">Send Me a Notification When...</h2>
            <div>            
                <?php render_result_messages() ?>                            
                <div class="notify-setting-panel">
                    
                    <form method="post" action="/notify.php" onsubmit="validateNotifyForm();return false;" id="notifyForm" style="padding-top:5px;">
                        <input type="hidden" value="saveNotifyInfo" name="action">
                        <h3 class="sub_section_title">Trade</h3>
                        <div class="row">
                            <div class="l">Someone makes me an offer</div>
                            <div class="r"><input type="checkbox" name="optOfferReceived" id="optOfferReceived" <?php if ($tradeNotifyInfo['optOfferReceived'] == 1) echo 'checked="checked"'?> value="1" ></div>
                            <div class="clear"></div>
                        </div>
                        
                        <div class="row">
                            <div class="l">Someone accepts my offer</div>
                            <div class="r"><input type="checkbox" name="optOfferAccepted" id="optOfferAccepted" <?php if ($tradeNotifyInfo['optOfferAccepted'] == 1) echo 'checked="checked"'?> value="1" ></div>
                            <div class="clear"></div>
                        </div>
                        
                        <div class="row">
                            <div class="l">Someone declines my offer</div>
                            <div class="r"><input type="checkbox" name="optOfferDeclined" id="optOfferDeclined" <?php if ($tradeNotifyInfo['optOfferDeclined'] == 1) echo 'checked="checked"'?> value="1" ></div>
                            <div class="clear"></div>
                        </div>
                        
                        <div class="row">
                            <div class="l">Feedback is received</div>
                            <div class="r"><input type="checkbox" name="optFeedbackReceived" id="optFeedbackReceived" <?php if ($tradeNotifyInfo['optFeedbackReceived'] == 1) echo 'checked="checked"'?> value="1" ></div>
                            <div class="clear"></div>
                        </div>
                        <br />
                        <h3 class="sub_section_title">Forum</h3>
                        <div class="row">
                            <div class="l">Someone replies to my topic</div>
                            <div class="r"><input type="checkbox" name="notifyRepliedToMyTopic" id="notifyRepliedToMyTopic" <?php if ($forumNotifyInfo['notifyRepliedToMyTopic'] == 1) echo 'checked="checked"'?> value="1" ></div>
                            <div class="clear"></div>
                        </div>
                        <div class="row">
                            <div class="l">Someone replies to a topic that I replied to</div>
                            <div class="r"><input type="checkbox" name="notifyRepliedToMyReply" id="notifyRepliedToMyReply" <?php if ($forumNotifyInfo['notifyRepliedToMyReply'] == 1) echo 'checked="checked"'?> value="1" ></div>
                            <div class="clear"></div>
                        </div>
                        <div class="row">
                            <div class="l">My post has been approved</div>
                            <div class="r"><input type="checkbox" name="notifyMyPostApproved" id="notifyMyPostApproved" <?php if ($forumNotifyInfo['notifyMyPostApproved'] == 1) echo 'checked="checked"'?> value="1" ></div>
                            <div class="clear"></div>
                        </div>                        
                        <div class="btn-row">
                            <input type="submit" value="Submit" class="redButton" name="submit" id="submit">                            
                        </div>
                        
                    </form>                        
                </div>
            </div>
            
            <div class="clear"></div>                
        </section>
    </section>
</section>
