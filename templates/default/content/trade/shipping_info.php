<?php
if(!isset($BUCKYS_GLOBALS))
{
    die("Invalid Request!");
}

$tradeShippingInfo = $view['trade_user_info'];

?>



<section id="main_section">
    
    <?php buckys_get_panel('trade_top_search');?>
        
    <?php buckys_get_panel('trade_main_nav'); ?>
    <section id="right_side" class="floatright">
    
        <span class="titles">Shipping Info</span><br/>
        <div>
            
            <div class="trade-item-panel" style="padding-top:0px;">
                
                <?php if ($view['update_message'] != '') :?>
                    <p style="" class="message error"><?php echo $view['update_message'];?></p>
                <?php endif;?>
                
                <form method="post" action="/trade/shipping_info.php" onsubmit="validateShippingInfoForm();return false;" id="shippingInfoForm" style="padding-top:5px;">
                    <input type="hidden" value="saveShippingInfo" name="action">
                    <div class="row">
                        <label for="shippingAddress">Address:</label>
                        <span class="inputholder"><input type="text" value="<?php echo $tradeShippingInfo['shippingAddress'];?>" class="input" name="shippingAddress" id="shippingAddress"></span>                        
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="shippingCity">City:</label>
                        <span class="inputholder"><input type="text" value="<?php echo $tradeShippingInfo['shippingCity']; ?>" class="input" name="shippingCity" id="shippingCity"></span>                        
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="shippingState">State:</label>
                        <span class="inputholder"><input type="text" value="<?php echo $tradeShippingInfo['shippingState']; ?>" class="input" name="shippingState" id="shippingState"></span>                        
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="shippingZip">Zip:</label>
                        <span class="inputholder"><input type="text" value="<?php echo $tradeShippingInfo['shippingZip']; ?>" class="input" name="shippingZip" id="shippingZip"></span>                        
                        <div class="clear"></div>
                    </div>
                    
                    <div class="row">
                        <label for="shippingCountryID">Country:</label>
                        <span class="inputholder">
                            <select class="input select" name="shippingCountryID" id="shippingCountryID">
                                <option value=""> - Select - </option>
                                <?php 
                                    if (count($view['country_list']) > 0) {
                                        foreach ($view['country_list'] as $countryData) {
                                            
                                            $selected = '';
                                            if ($tradeShippingInfo['shippingCountryID'] == $countryData['countryID'])
                                                $selected = 'selected="selected"';
                                                
                                            echo sprintf('<option value="%d" %s>%s</option>', $countryData['countryID'], $selected, $countryData['country_title']);
                                        }
                                    }
                                ?>
                            </select>
                        </span>
                        <div class="clear"></div>
                    </div>
                    
                    
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
