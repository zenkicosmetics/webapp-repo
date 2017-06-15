<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Test controller for the social module (frontend)
 * 
 * @author DuNT
 * @package PyroCMS
 * @subpackage Widget module
 * @category Modules
 */
?>
<div class="left column" style="margin-right: 10px; width: 180px; margin-left: 10px;">
    <?php 
	    $cur_theme = Settings::get(APConstants::FRONTEND_THEMES_CODE); 
	    $category_image = $data->IconURL;
	    if (empty($data->IconURL)) {
            if ($data->Description == 'Axle Drive') {
                $category_image = APContext::getImagePath().'/Axle-Drive_category_icon.png';
            } else {
                $category_image = APContext::getImagePath().'/generic-parts-category_icon.png';
            }
        }
	?>
    <div class="category_title" style="width: 180px">
        <img class="category_image" alt="<?php echo $data->Description;?>" src="<?php echo $category_image?>"/>
    </div>
    <ul style="min-height: 150px;">
        <li style="font-weight: bold;"><a href="#" class="group_item" data-treetype="<?php echo $tree_type?>" data-DescID="<?php echo implode(",", $data->DescID)?>" data-nodeId="<?php echo $data->NodeID;?>" style="color: #0089C8;"><?php echo $data->Description;?></a></li>
        <?php
            $count = 1; 
            $hidden = '';
            foreach ($data->Childs as $child) {
                if($count > 5){
                    $hidden = 'hidden';
                }
        ?>
            <li class="sub_category_item <?php echo $hidden?>"><a href="#" data-treetype="<?php echo $tree_type?>" class="group_item" data-nodeId="<?php echo $child->NodeID;?>" data-DescID="<?php echo implode(",", $child->DescID)?>" style="color: #0089C8;"><?php echo $child->Description;?></a></li>
            
        <?php 
            $count++;
         } 
         ?>
         <?php 
             if(!empty($hidden)) {
         ?>
             <li><a href="#" class="more" style="color: #0089C8;">...More</a></li>
         <?php }?>
    </ul>
</div>