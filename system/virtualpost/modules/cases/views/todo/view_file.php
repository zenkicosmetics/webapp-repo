<?php if($ext == 'pdf'){?>
<embed src="<?php echo base_url()?>cases/todo/view_file?id=<?php echo $id?>&type=<?php echo $type; ?>&case_id=<?php echo $case_id?>&op=<?php echo $op?>&t=<?php echo time()?>"  width="100%" height="100%" type='application/pdf'>
<?php }else{?>
<img src="<?php echo base_url()?>cases/todo/view_file?id=<?php echo $id?>&type=<?php echo $type; ?>&case_id=<?php echo $case_id?>&op=<?php echo $op?>&t=<?php echo time()?>" width="100%"  />
<?php }?>