<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>
<section id="main_section">
    <section id="main_content">
        <div id="breadcrumbs">
            <a href="/forum">Forum Home</a>
            <?php foreach($hierarchical as $cr){ ?>
                &gt;
                <a href="/forum/<?php echo $cr['parentID'] == 0 ? 'index.php' : 'category.php'?>?id=<?php echo $cr['categoryID']?>"><?php echo $cr['categoryName'] ?></a>
            <?php } ?>
        </div>
        <?php render_result_messages() ?>
        <h2 class="titles"><?php echo $category['categoryName']?></h2>
            <table cellpadding="0" cellspacing="0" class="forumlist">
                <?php if(count($topics) > 0){ ?>
                <thead>
                    <tr>
                        <th>Topic</th>
                        <th>Author</th>
                        <th class="td-counts" style="padding-right:10px;">Replies</th>
                        <th>Last Post</th>
                    </tr>
                </thead>   
                <tfoot>
                    <tr>
                        <td colspan="5"><?php echo $pagination->renderPaginate('/category.php?', count($topics))?></td>
                    </tr> 
                    <?php if(buckys_check_user_acl(USER_ACL_REGISTERED)){ ?>
                    <tr>
                        <td colspan="5" class="btn-td">
                            <a href="/forum/create_topic.php?category=<?php echo $category['categoryID']?>" class="redButton">New Topic</a>
                        </td>
                    </tr>                    
                    <?php } ?>                   
                </tfoot>             
                <tbody>                  
                    <?php foreach($topics as $row){ ?>
                    <tr>
                        <td style="width:70%;padding-right:10px;"><a href="/forum/topic.php?id=<?php echo $row['topicID']?>"><?php echo $row['topicTitle']?></a></td>
                        <td style="padding-right:10px;"><a style="font-weight:bold;" href="/profile.php?u=<?php echo $row['creatorID']?>"><?php echo $row['creatorName']?></a></td>
                        <td class="td-counts"><?php echo $row['replies']?></td>
                        <td>
                            <?php
                                if($row['lastReplierID'] > 0){ 
                            ?>
                                <a style="font-weight:bold;" href="/profile.php?u=<?php echo $row['lastReplierID']?>"><?php echo $row['lastReplierName']?></a>                            
                            <?php
								echo '<span style="color:#999999;">';
                                echo buckys_format_date($row['lastReplyDate']);
								echo '</span>';
                                }else{
                            ?>
                                <a style="font-weight:bold;" href="/profile.php?u=<?php echo $row['creatorID']?>"><?php echo $row['creatorName']?></a>                            
                            <?php
								echo '<span style="color:#999999;">';
                                echo buckys_format_date($row['createdDate']);
								echo '</span>';
                                }
                            ?>
                        </td>
                    </tr>
                    <?php } ?>                                      
                </tbody>            
                <?php }else{ ?>                    
                <tbody>
                    <tr>
                        <td colspan="6">Nothing to see here</td>
                    </tr>
                </tbody>
                <?php } ?>                    
            </table>       
    </section>
</section>
