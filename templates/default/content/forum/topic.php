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
            &gt;
            <a href="/forum/topic.php?id=<?php echo $topic['topicID']?>"><?php echo $topic['topicTitle']?></a>
        </div>
        <?php render_result_messages() ?>        
            <table cellpadding="0" cellspacing="0" class="postentry">
                <thead>
                    <tr>
                        <th colspan="4">
                            <h2 class="titles"><?php echo $topic['topicTitle']?></h2>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width:70px;">
                            <a href='/profile.php?user=<?php echo $topic['creatorID']?>'>
                                <img class="profileIcon" src="<?php echo BuckysUser::getProfileIcon(array('thumbnail'=>$topic['thumbnail'], 'userID'=>$topic['creatorID'])) ?>" />
                            </a>
                        </td>
                        <td class="post-content">
                            <a style="font-weight:bold;" href='/profile.php?user=<?php echo $topic['creatorID']?>'>
                                <?php echo $topic['creatorName']; ?>
                            </a>
							<br/>
                            <div>
                                <?php
                                    echo BuckysForumTopic::_convertBBCodeToHTML($topic['topicContent']);                            
                                ?>
                            </div>
                            <?php if ($BUCKYS_GLOBALS['user']['userID'] == $topic['creatorID']):?>
                                <div class="topic-edit-btn-cont">
                                    <a href="javascript:void(0)" rel="/forum/topic.php?action=delete&id=<?php echo $topic['topicID'];?>" class="delete_topic_btn">Delete</a> &middot; <a href="/forum/edit_topic.php?id=<?php echo $topic['topicID'];?>">Edit</a>
                                </div>
                            <?php endif;?>
                            
                        </td>
						<!--
                        <td class="post-creator" style="width:5%;">
                            <a style="font-weight:bold;" href='/profile.php?user=<?php echo $topic['creatorID']?>'>
                                <?php echo $topic['creatorName']; ?>
                            </a>
                        </td>
						-->
                        <td class="post-createddate" style="width:5%;color:#999999;">
                            <?php echo buckys_format_date($topic['createdDate']); ?>
                        </td>
                        <td class="post-votes <?php echo !$topic['voteID'] ? '' : 'voted'?>" <?php echo !$topic['voteID'] ? '' : 'title="' . MSG_ALREADY_CASTED_A_VOTE . '"'?> style="width:5%;">
                            <a href="#" class="thumb-down" data-type='topic' data-id="<?php echo $topic['topicID']?>" data-hashed="<?php echo buckys_encrypt_id($topic['topicID'])?>">Down</a>
                            <a href="#" class="thumb-up" data-type='topic' data-id="<?php echo $topic['topicID']?>" data-hashed="<?php echo buckys_encrypt_id($topic['topicID'])?>">Up</a>
                            <span class="reply-votes">
                                <?php 
                                    if($topic['votes'] > 0){
                                        echo '+';
                                    }
                                    echo $topic['votes'];
                                ?>
                                <span class="loading-wrapper"></span>
                            </span>
                            <?php if(buckys_check_user_acl(USER_ACL_REGISTERED)){ ?>
                            <div class="clear"></div>
                            <a href="/report_object.php" data-type="topic" data-id="<?php echo $topic['topicID']?>" data-idHash="<?php echo buckys_encrypt_id($topic['topicID'])?>" class="report-link">
                            <?php echo !$topic['reportID'] ? 'Report' : 'You reported this.'?>
                            </a>
                            <?php } ?>
                            &nbsp;
                        </td>
                    </tr>
                    <?php if(count($replies) > 0) { ?>
                    <tr>
                        <td colspan="4">
                            <h2 class="titles">Replies</h2>
                            <div class="post-sort-nav">
                                <a href="/forum/topic.php?id=<?php echo $topic['topicID']?>&orderby=oldest&page=<?php echo $page;?>" <?php echo $orderBy == 'oldest' ? 'class="current"' : ''?>>Oldest</a>
                                &middot;
                                <a href="/forum/topic.php?id=<?php echo $topic['topicID']?>&orderby=newest&page=<?php echo $page;?>" <?php echo $orderBy == 'newest' ? 'class="current"' : ''?>>Newest</a>
                                &middot;
                                <a href="/forum/topic.php?id=<?php echo $topic['topicID']?>&orderby=highrated&page=<?php echo $page;?>" <?php echo $orderBy == 'highrated' ? 'class="current"' : ''?>>Highest Rated</a>
                            </div>
                        </td>
                    </tr>
                    <?php
                    foreach($replies as $row){
                    ?>
                    <tr class="reply-tr">
                        <td>
                            <a href='/profile.php?user=<?php echo $row['creatorID']?>'>
                                <img class="profileIcon" src="<?php echo BuckysUser::getProfileIcon(array('thumbnail'=>$row['thumbnail'], 'userID'=>$row['creatorID'])) ?>" />
                            </a>
                        </td>
                        <td class="post-content">
                            <a style="font-weight:bold;" href='/profile.php?user=<?php echo $row['creatorID']?>'>
                                <?php echo $row['creatorName']; ?>
                            </a>
							<br/>
                            <div>
                                <?php echo BuckysForumTopic::_convertBBCodeToHTML($row['replyContent']); ?>
                            </div>
                            <?php if ($BUCKYS_GLOBALS['user']['userID'] == $row['creatorID']):?>
                                <div class="topic-edit-btn-cont">
                                    <a href="/forum/post_reply.php?action=delete&id=<?php echo $topic['topicID'];?>&replyID=<?php echo $row['replyID']?>" class="delete_topic_reply_btn">Delete</a> &middot; <a href="/forum/post_reply.php?id=<?php echo $topic['topicID'];?>&action=edit&replyID=<?php echo $row['replyID']?>">Edit</a>
                                </div>
                            <?php endif;?>
                            
                        </td>
						<!--
                        <td class="post-creator">
                            <a style="font-weight:bold;" href='/profile.php?user=<?php echo $row['creatorID']?>'>
                                <?php echo $row['creatorName']; ?>
                            </a>
                        </td>
						-->
                        <td class="post-createddate" style="color:#999999;">
                            <?php echo buckys_format_date($row['createdDate']); ?>
                        </td>
                        <td class="post-votes <?php echo !$row['voteID'] ? '' : 'voted'?>" <?php echo !$row['voteID'] ? '' : 'title="' . MSG_ALREADY_CASTED_A_VOTE . '"'?>>
                            <a href="#" class="thumb-down" data-type='reply' data-id="<?php echo $row['replyID']?>" data-hashed="<?php echo buckys_encrypt_id($row['replyID'])?>">Down</a>
                            <a href="#" class="thumb-up" data-type='reply' data-id="<?php echo $row['replyID']?>" data-hashed="<?php echo buckys_encrypt_id($row['replyID'])?>">Up</a>                            
                            <span class="reply-votes">
                                <?php 
                                    if($row['votes'] > 0){
                                        echo '+';   
                                    }
                                    echo $row['votes'];
                                ?>
                                <span class="loading-wrapper"></span>
                            </span>
                            <?php if(buckys_check_user_acl(USER_ACL_REGISTERED)){ ?>
                            <div class="clear"></div>                                
                            <a href="/report_object.php" data-type="reply" data-id="<?php echo $row['replyID']?>" data-idHash="<?php echo buckys_encrypt_id($row['replyID'])?>" class="report-link">
                            <?php echo !$row['reportID'] ? 'Report' : 'You reported this.'?>
                            </a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <?php } ?>                    
                    <tr>
                        <td colspan="5" class="btn-td">
                            <?php if(buckys_check_user_acl(USER_ACL_REGISTERED)){ ?>
							<a href="/forum/create_topic.php?category=<?php echo $cr['categoryID']?>" class="redButton" style="margin-right:5px;">New Topic</a>                            
                            <a href="/forum/post_reply.php?id=<?php echo $topic['topicID']?>" class="redButton">Post Reply</a>                        
                            <?php } ?>
                            <?php echo $pagination->renderPaginate('/forum/topic.php?id=' . $topic['topicID'] . '&orderby=' . $orderBy . '&'); ?>
                        </td>
                    </tr>                    
                    
                </tbody>
            </table>                    
    </section>
</section>
<script type="text/javascript">
    jQuery(document).ready(function(){
        $('.post-votes a.thumb-up,.post-votes a.thumb-down').click(function(){
            if($(this).parent().hasClass('voted'))
                return false;
            var link = jQuery(this);
            link.parent().find('.loading-wrapper').show();
            jQuery.ajax({
                url: '/forum/topic.php',
                data: {
                    'objectID': link.attr('data-id'),
                    'objectIDHash': link.attr('data-hashed'),
                    'objectType': link.attr('data-type'),
                    'action': link.attr('class')
                },
                type: 'post',
                dataType: 'xml',
                success: function(rsp){
                    if(jQuery(rsp).find('status').text() == 'success')
                    {
                        link.parent().find('.reply-votes').html(jQuery(rsp).find('votes').text());
                        link.parent().addClass('voted');
                    }else{
                        alert(jQuery(rsp).find('message').text());
                    }
                },
                complete: function(){
                    link.parent().find('.loading-wrapper').hide();
                }
            })
            return false;
        })
    })
</script>