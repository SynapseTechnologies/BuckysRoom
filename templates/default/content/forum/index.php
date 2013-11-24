<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>
<section id="main_section">
    <section id="main_content">
        <?php if(isset($hierarchical)){ ?>
        <div id="breadcrumbs">
            <a href="/forum">Forum Home</a>
            <?php foreach($hierarchical as $cr){ ?>
                &gt;
                <a href="/forum/<?php echo $cr['parentID'] == 0 ? 'index.php' : 'category.php'?>?id=<?php echo $cr['categoryID']?>"><?php echo $cr['categoryName'] ?></a>
            <?php } ?>
        </div>
        <?php } ?>
        <?php render_result_messages() ?>
        <table cellpadding="0" cellspacing="0" class="forumlist">
            <?php foreach($categories as $cat){ ?>            
                <thead>
                    <tr>
                        <th style="padding:0px;padding-bottom:5px;" class="titles"><?php echo $cat['categoryName']?></th>
                        <th style="padding:0px;" >Last Post</th>
                        <th style="padding:0px;" class="td-counts">Topics</th>
                        <th style="padding:0px;" class="td-counts">Replies</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cat['children'] as $idx=>$subCat){ ?>
                    <tr <?php echo $idx == count($cat['children']) - 1 ? 'class="last-tr"' : ''?>>
                        <td><a href="/forum/category.php?id=<?php echo $subCat['categoryID']?>"><?php echo $subCat['categoryName']?></a></td>
                        <td width="70%">
                          <?php 
                            if($subCat['lastTopicID'] > 0 ){
                                echo '<a href="/profile.php?user=' . $subCat['lastPosterID'] . '"><img src="' . BuckysUser::getProfileIcon($subCat['lastPosterID']) . '" class="poster-icon" /></a>';
                                echo "<a href='/forum/topic.php?id=" . $subCat['lastTopicID'] . "'>";
                                if(strlen($subCat['lastPostTitle']) > 200)      
                                    echo substr($subCat['lastPostTitle'], 0, 195) . "...";
                                else 
                                    echo $subCat['lastPostTitle'];
                                echo "</a><br />";
								?>
                                <a style="font-weight:bold;" href="/profile.php?user=<?php echo $subCat['lastPosterID']?>"><?php echo $subCat['lastPosterName']?></a>                                
                                <?php								
								echo '<span style="color: #999999;">';
                                echo buckys_format_date($subCat['lastPostDate']);
								echo '</span>';
                            } else {
                                echo "-";
                            }
                          ?>
                        </td>
                        <td class="td-counts"><?php echo $subCat['topics']?></td>
                        <td class="td-counts"><?php echo $subCat['replies']?></td>
                    </tr>
                    <?php } ?>                    
                </tbody>            
            <?php } ?>
        </table>
    </section>
</section>