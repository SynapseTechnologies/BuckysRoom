<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>
<section id="main_section">    
    <?php buckys_get_panel('account_links'); ?>        
    <section id="right_side">
        <section id="right_side_padding">
            <?php render_result_messages() ?>
            <div id="current-moderator-box">        
                <?php if(!$currentModerator) { ?>
                <h2 class="titles">Current Moderator</h2>
                <?php }else{ ?>
                <h2 class="titles">Current Moderator - <?php echo $currentModerator['firstName'] . " " . $currentModerator['lastName']?></h2>
                <a href="profile.php?user=<?php echo $currentModerator['userID'];?>" style="float:left;"><img src="<?php echo BuckysUser::getProfileIcon($currentModerator)?>" style="width:170px;margin-right:10px;"/></a>
				<a href="http://buckysroom.com/messages_compose.php?to=<?php echo $currentModerator['userID'];?>">Send Message</a> <br />
				<a href="profile.php?user=<?php echo $currentModerator['userID'];?>">View Profile</a>
                <?php } ?>
            </div>
            <div id="apply-moderator-box">
                <form id="applymoderator" name="applymoderator" action="/moderator.php" method="post">
                    <h2 class="titles">Job Description</h2>
                    <p>Community Moderators are responsible for handling all reported content. This content includes all messages, comments, and posts that have been reported by users. Responsibilities include deciding whether or not to delete or approve reported content, as well as banning users that have abused the site.<br /><br />
                    This position is for one week.
                    <br /><br />
                    <b>&beta; 125.00/week</b>            
                    </p>
                    <br />
                    <input type="submit" value="Apply Now" class="redButton" /><br />
                    <textarea cols="10" name="moderator_text" id="moderator_text" rows="5" placeholder="Tell us why you would make a good moderator..."></textarea>
                    <input type="hidden" name="action" value="apply_candidate" />
                    <input type="hidden" name="type" id="type" value="<?php echo $moderatorType?>" />
                </form>
            </div>
            <div class="clear"></div>
            <div id="candidates-box">
                <h2 class="titles">Vote for the Next Moderator <small>(<?php echo $remindTimeString?> left)</small></h2>
                <?php foreach($candidates as $row){ ?>
                <div class="candidate-row" id="candidate-row<?php echo $row['candidateID']?>">
                    <div class="votes  <?php echo  $row['voteID'] ? 'voted' : '' ?>">
                        <span class="votes-count"><?php echo $row['votes'] > 0 ? '+' : '' ?><?php echo $row['votes']?></span>
                        <a href="#" class="thumb-up" data-id="<?php echo $row['candidateID']?>" data-hashed="<?php echo buckys_encrypt_id($row['candidateID'])?>"></a>
						<!-- <a href="#" class="thumb-down" data-id="<?php echo $row['candidateID']?>" data-hashed="<?php echo buckys_encrypt_id($row['candidateID'])?>"></a> -->
                        <div class="loading-wrapper"></div>
                    </div>
                    <a href="/profile.php?user=<?php echo $row['userID']?>"><img src="<?php echo BuckysUser::getProfileIcon($row)?>" class="candidateImg" /></a>
                    <div class="candidate-detail">
                        <a style="font-weight:bold;" href="/profile.php?user=<?php echo $row['userID']?>"><?php echo $row['firstName'] . " " . $row['lastName']?></a>
                        <p><?php echo $row['candidateText']?></p>
                        <?php if(buckys_check_user_acl(USER_ACL_ADMINISTRATOR)) { ?>
                        <a href="/moderator.php?type=<?php echo $moderatorType?>&action=choose-moderator&id=<?php echo $row['candidateID']?>&idHash=<?php echo buckys_encrypt_id($row['candidateID'])?>" class="redButton">Choose Moderator</a>
                        <?php } ?>
                    </div>                    
                    <div class="clear"></div>
                </div>
                <?php } ?>
            </div>
            <br />
            <br />
            <?php $pagination->renderPaginate('/moderator.php?type=' . $moderatorType . '&', count($candidates)); ?>
        </section>
    </section>
</section>
