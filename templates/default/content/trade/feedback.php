<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  


$feedbackList = $view['feedback'];
$userIns = new BuckysUser();

if (!$view['myRatingInfo'])
    $view['myRatingInfo'] = array();

?>

<section id="main_section">
    
    <?php buckys_get_panel('trade_top_search');?>
        
    <section id="feedback-left-panel">
        <?php 
            $myInfo = $userIns->getUserBasicInfo($view['myID']);
            $myData = BuckysUser::getUserData($view['myID']);
            
            $totalRating = 'No';
            $positiveRating = '';

            if ($view['myRatingInfo']['totalRating'] != '' && $view['myRatingInfo']['totalRating'] > 0) {
                $totalRating = $view['myRatingInfo']['totalRating'];
                if (is_numeric($view['myRatingInfo']['positiveRating'])) {
                    $positiveRating = number_format($view['myRatingInfo']['positiveRating'] / $totalRating * 100, 2, '.', '') . '% Positive';
                }
            }
            
            
        ?>
        <div class="titles">
            <?php echo trim($myInfo['firstName'] . ' ' . $myInfo['lastName']);?>
        </div>
        <div class="feedback-user-img">
            <?php render_profile_link($myData, 'mainProfilePic'); ?>
        </div>
        <div>
            <?php 
                if (is_numeric($totalRating)) {
                    echo sprintf('<a href="%s" class="rating">(%d ratings)</a> %s', '/trade/feedback.php?user=' . $view['myID'], $totalRating, $positiveRating);
                }
                else {
                    echo sprintf('(%s ratings)', $totalRating);
                }
            ?>
        </div>
    </section>
    <section id="feedback-right-panel">
    
        <span class="titles">Feedback</span><br/>
        <div>
            <?php if ($view['type'] != 'received'):?>
                <a href="/trade/feedback.php?user=<?php echo $view['myID'];?>">Received</a> |
                <span>Given</span>
            <?php else :?>
                <span>Received</span> |
                <a href="/trade/feedback.php?user=<?php echo $view['myID'];?>&type=given">Given</a>
            <?php endif;?>
            
        </div>
        <div class="trade-available-list">
            <?php if (isset($feedbackList) && count($feedbackList) > 0) :?>
                
                <table cellpadding="0" cellspacing="0" class="feedback-table">
                    <thead>
                        <th width="440">Feedback</th>
                        <th width="230"><?php if ($view['type'] == 'received') echo 'From'; else echo 'To';?></th>
                        <th width="100">Date</th>
                    </thead>
                    <tbody>
                
                <?php 
                    foreach ($feedbackList as $feedbackData) :
                        
                        $feedbackText = '';
                        $itemTitle = '';
                        $theirID = '';
                        $feedbackScore = '';
                        $feedbackDate = '';
                        $theirTotalRating = '';
                        $theirPositiveRating = '';
                    
                        if ($view['type'] == 'received') {
                            
                            if ($feedbackData['sellerID'] == $view['myID']) {
                                $feedbackText = $feedbackData['buyerToSellerFeedback'];
                                $feedbackScore = $feedbackData['buyerToSellerScore'];
                                $feedbackDate = $feedbackData['buyerToSellerFeedbackCreatedAt'];
                                
                                $itemTitle = $feedbackData['sellerItemTitle'];
                                $theirID = $feedbackData['buyerID'];
                                
                                
                            }
                            else {
                                $feedbackText = $feedbackData['sellerToBuyerFeedback'];
                                $feedbackScore = $feedbackData['sellerToBuyerScore'];
                                $feedbackDate = $feedbackData['sellerToBuyerFeedbackCreatedAt'];
                                
                                $itemTitle = $feedbackData['buyerItemTitle'];
                                $theirID = $feedbackData['sellerID'];
                            }
                            
                        }
                        else {
                            //Given
                            
                            if ($feedbackData['sellerID'] == $view['myID']) {
                                $feedbackText = $feedbackData['sellerToBuyerFeedback'];
                                $feedbackScore = $feedbackData['sellerToBuyerScore'];
                                $feedbackDate = $feedbackData['sellerToBuyerFeedbackCreatedAt'];
                                
                                $itemTitle = $feedbackData['buyerItemTitle'];
                                $theirID = $feedbackData['buyerID'];
                            }
                            else {
                                $feedbackText = $feedbackData['buyerToSellerFeedback'];
                                $feedbackScore = $feedbackData['buyerToSellerScore'];
                                $feedbackDate = $feedbackData['buyerToSellerFeedbackCreatedAt'];
                                
                                $itemTitle = $feedbackData['sellerItemTitle'];
                                $theirID = $feedbackData['sellerID'];
                            }
                            
                        }
                        
                        
                        if ($feedbackData['sellerID'] == $view['myID']) {
                            $theirTotalRating = $feedbackData['buyerTotalRating'];
                            $theirPositiveRating = $feedbackData['buyerPositiveRating'];
                        }
                        else {
                            $theirTotalRating = $feedbackData['sellerTotalRating'];
                            $theirPositiveRating = $feedbackData['sellerPositiveRating'];
                        }
                            
                        
                        $feedbackDate = date('F j, Y', strtotime($feedbackDate));
                        
                        
                        $totalRating = 'No';
                        $positiveRating = '';

                        if ($theirTotalRating != '' && $theirTotalRating > 0) {
                            $totalRating = $theirTotalRating;
                            if (is_numeric($theirPositiveRating)) {
                                $positiveRating = number_format($theirPositiveRating / $totalRating * 100, 2, '.', '') . '% Positive';
                            }
                        }
                        
                        $theirInfo = $userIns->getUserBasicInfo($theirID);
                        
                        
                ?>
                    <tr>
                        <td>
                            <div class="<?php if ($feedbackScore > 0) echo 'feedback-positive'; else echo 'feedback-negative';?>"></div>
                            <div class="f-text">
                                <p><?php echo $feedbackText;?></p>
                                <div class="i-title"><?php echo $itemTitle;?></div>
                            </div>
                            <div class="clear"></div>
                        </td>
                        <td>
                            
                            <div class="f-user-image">
                                <a href="/profile.php?user=<?php echo $theirID;?>" class="profileLink">
                                    <img src="<?php echo BuckysUser::getProfileIcon($theirID)?>" class="postIcons" />                                    
                                </a>
                            </div>
                            <div class="f-user-desc">
                                <a href="/profile.php?user=<?php echo $theirID;?>" class="profileLink">
                                    <span><?php echo trim($theirInfo['firstName'] . ' ' . $theirInfo['lastName']);?></span>
                                </a> <br/>
                                <?php 
                                    if (is_numeric($totalRating)) {
                                        echo sprintf('<a href="%s" class="rating">(%d ratings)</a> %s', '/trade/feedback.php?user=' . $theirID, $totalRating, $positiveRating);
                                    }
                                    else {
                                        echo sprintf('(%s ratings)', $totalRating);
                                    }
                                ?>
                            </div>
                            <div class="clear"></div>
                            
                        </td>
                        <td>
                            <?php echo $feedbackDate;?>
                        </td>
                    </tr>
                    
                    
                <?php endforeach;?>
                    </tbody>
                </table>
                
                <?php buckys_get_panel('trade_pagination');?>
                
            <?php else:?>
                
                <div class="no-trade-data"> - No data available - </div>
            
            <?php endif;?>
                
        </div>
        
        <div class="clear"></div>                
        
    </section>
</section>
