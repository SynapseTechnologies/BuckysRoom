<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}

$userIns = new BuckysUser();
$pageIns = new BuckysPage();
$pageFollowerIns = new BuckysPageFollower();

$searcuResult = $view['search_result'];

?>

<script type="text/javascript">
    
</script>

<section id="main_section">
    
    <?php buckys_get_panel('top_search');?>
    
    
    <section id="main_content" class="search-result-panel">
            
            <?php render_result_messages(); ?>
            
            <div class="search-result-list">
                <?php
                    if (count($searcuResult) > 0) {
                        foreach($searcuResult as $data) {
                            if ($data['type'] == 'user') {
                                //Display user
                                $userData = $userIns->getUserData($data['userID']);
                                
                                if (empty($userData))
                                    continue;
                                
                                $profileLink = '/profile.php?user=' . $userData['userID'];
                                $sendMessageLink = '/messages_compose.php?to=' . $userData['userID'];
                ?>
                            <div class="node">
                                <div class="img-cont"><?php render_profile_link($userData, 'thumbIcon'); ?></div>
                                <div class="desc">
                                    <a href="<?php echo $profileLink;?>"><b><?php echo $userData['firstName'] . ' ' . $userData['lastName']; ?></b></a> <br/>
                                    <span><?php if ($userData['gender_visibility'] == 1) echo $userData['gender'];?></span><br/>
                                    <span><?php if ($userData['birthdate_visibility'] == 1 && $userData['birthdate'] != '0000-00-00' && strtotime($userData['birthdate']) !== false) echo date('F j, Y', strtotime($userData['birthdate']));?></span><br/>
                                    <span><?php echo $data['PPFollowers']?> Friend(s)</span>
                                </div>
                                <div class="action">
                                    <a href="<?php echo $profileLink;?>">View Profile</a> <br/>
                                    <a href="<?php echo $sendMessageLink;?>">Send Message</a> <br/>
                                </div>
                                
                                <div class="clear"></div>
                            </div>  
                <?php
                                
                            }
                            else {
                                //Display Page
                                
                                $pageData = $pageIns->getPageByID($data['pageID']);
                                $followerCount = $pageFollowerIns->getNumberOfFollowers($data['pageID']);
                                
                                if (empty($pageData))
                                    continue;
                                
                                $pageLink = '/page.php?pid=' . $pageData['pageID'];
                
                ?>
                            <div class="node">
                                <div class="img-cont"><?php render_pagethumb_link($pageData, 'thumbIcon'); ?></div>
                                <div class="desc">
                                    <a href="<?php echo $pageLink;?>"><b><?php echo $pageData['title']; ?></b></a> <br/><br/>
                                    <span><?php echo $data['PPFollowers']?> Follower(s)</span>
                                </div>
                                <div class="action">
                                    <a href="<?php echo $pageLink;?>">View Page</a>                                    
                                </div>
                                <div class="clear"></div>
                            </div>
                <?php
                                
                            }
                        }
                    }
                    else {
                        echo '<div class="no-data">Nothing to see here.</div>';
                    }
                
                ?>
            </div>
			
			<?php $pagination->renderPaginate($view['page_base_url'], count($searcuResult)); ?>
        
    </section>
</section>
