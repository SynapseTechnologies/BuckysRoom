<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}  
?>

<script type="text/javascript">
    <?php if (isset($view['item'])) :?>
        var current_img_files = <?php if ($view['item']['images'] != '') echo json_encode(explode("|", $view['item']['images'])); else echo json_encode(array());?>;        
    <?php else :?>
        var current_img_files = []; 
    <?php endif;?>
</script>




<?php if ($view['no_credits']) :?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            //jQuery("#tradeEditForm").attr("disabled", true);
            jQuery("#tradeEditForm :input").attr("disabled", true);
            $("#image_upload_btn").hide();
        });
    </script>
<?php endif;?>

<section id="main_section">
    
    <?php buckys_get_panel('trade_top_search');?>
        
    <?php buckys_get_panel('trade_main_nav'); ?>
    <section id="right_side" class="floatright">
    
        <div><span class="titles"><?php echo $view['page_title'];?></span></div>
        
        
        <?php if ($view['no_credits']) :?>
            <p class="message error">You have not enough credits. Click <a href="/credits.php" style="color:white;text-decoration:underline">here</a> to purchase credits.</p>
        <?php endif;?>
        
        
        <?php if ($view['type'] == 'relist') :?>
        <div class="special-note">
            You will relist this item for <?php echo TRADE_ITEM_LIFETIME;?> days more. It will be available for another <?php echo TRADE_ITEM_LIFETIME;?> days from now on. <br/>
            <span>Note: It requires 1 credit to relist item.</span>
        </div>
        <?php endif;?>
        
        <div>
            
            <div class="trade-item-panel" style="padding-top:0px;">
                <form method="post" id="tradeEditForm" style="padding-top:5px;">
                    
                    <input type="hidden" id="actionName" value="<?php echo $view['action_name'];?>" >
                    <input type="hidden" id="itemID" value="<?php if (isset($view['item'])) echo $view['item']['itemID']; ?>" >
                    <input type="hidden" id="actionType" value="<?php if (isset($view['type'])) echo $view['type']; ?>" >
                    <div class="row">
                        <label for="title">Title:</label>
                        <span class="inputholder"><input type="text" value="<?php if (isset($view['item'])) echo $view['item']['title']; ?>" class="input" name="title" id="title" maxlength="80"></span>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="subtitle">Subtitle:</label>
                        <span class="inputholder"><input type="text" value="<?php if (isset($view['item'])) echo $view['item']['subtitle']; ?>" class="input" name="subtitle" id="subtitle" maxlength="60"></span>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="description">Description:</label>
                        <span class="inputholder"><textarea class="input inputdesc" name="description" id="description" maxlength="5000"><?php if (isset($view['item'])) echo $view['item']['description']; ?></textarea></span>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="items_wanted">Items Wanted:</label>
                        <span class="inputholder"><input class="input" name="items_wanted" id="items_wanted" maxlength="500" value="<?php if (isset($view['item'])) echo $view['item']['itemWanted']; ?>"></span>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="category">Category:</label>
                        <span class="inputholder">
                            <select class="input select" name="category" id="category">
                                <option value=""> - Select - </option>
                                <?php 
                                    if (count($view['category_list']) > 0) {
                                        foreach ($view['category_list'] as $catData) {
                                            
                                            $selected = '';
                                            
                                            if (isset($view['item']) && $view['item']['catID'] == $catData['catID'])
                                                $selected = 'selected="selected"';
                                            
                                            echo sprintf('<option value="%d" %s>%s</option>', $catData['catID'], $selected, $catData['name']);
                                        }
                                    }
                                ?>
                            </select>
                        </span>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label>Images (Up to 5) :</label>
                        <span class="inputholder">
                            <input type="button" id="image_upload_btn" name="image_upload_btn" type="file" />
                        </span>
                        <div class="clear"></div>
                    </div>
                    <div class="row" id="image_list_container">                        
                    </div>
                    
                    <div class="clear"></div>
                    <div class="row">
                        <label for="location">Item Location:</label>
                        <span class="inputholder">
                            <select class="input select" name="location" id="location">
                                <option value=""> - Select - </option>
                                <?php 
                                    if (count($view['country_list']) > 0) {
                                        foreach ($view['country_list'] as $countryData) {
                                            
                                            $selected = '';
                                            if (isset($view['item']) && $view['item']['locationID'] == $countryData['countryID'])
                                                $selected = 'selected="selected"';
                                                
                                            echo sprintf('<option value="%d" %s>%s</option>', $countryData['countryID'], $selected, $countryData['country_title']);
                                        }
                                    }
                                ?>
                            </select>
                        </span>
                        <div class="clear"></div>
                    </div>
                    
                    <?php if ($view['type'] == 'relist' || $view['type'] == 'additem') :?>
                        <div class="row" style="margin-bottom:5px;margin-top:15px;">
                            <label for="">&nbsp;</label>
                            
                                <span class="inputholder" style="font-weight:bold;">
                                    Cost: &#946; 1.00
                                </span>
                            
                            <div class="clear"></div>
                        </div>
                    
                    <?php endif;?>
                    
                    <div class="row">
                        <label for="">&nbsp;</label>
                        <span class="inputholder">
                            <input type="submit" value="Submit" class="redButton" name="submit" id="submit">
                        </span>
                        <div class="clear"></div>
                    </div>
                    
                </form>                        
            </div>
        </div>
        
        <div class="clear"></div>                
        
    </section>
</section>
