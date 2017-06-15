<?php if ($this->method == 'edit'): ?>
	<h2 class="header-title"><?php echo lang('feedback.edit_title'); ?></h2>
<?php else: ?>
	<h2 class="header-title"><?php echo lang('feedback.add_title'); ?></h2>
<?php endif; ?>

<div class="button_container">
    <div class="input-form">
<?php echo form_open(uri_string(), 'class="crud"'); ?>
<div class="tabs">
	<ul class="tab-menu">
		<li><a href="#feedback-content-tab"><span><?php echo lang('feedback.tab_content_label'); ?></span></a></li>
	</ul>
	<div class="form_inputs" id="feedback-content-tab">
		<table>
			<tr>
				<th><label for="name"><?php echo lang('feedback.name_label');?> <span>*</span></label></th>
				<td><?php echo form_input('Name', $feedback->Name);?></td>
			</tr>
			<tr>
				<th><label for="subject"><?php echo lang('feedback.subject_label');?> <span>*</span></label></th>
				<td><?php echo form_input('Subject', $feedback->Subject);?></td>
			</tr>
			<tr>
				<th><?php echo lang('feedback.status_label'); ?></th>
        		<td>
        			    <?php echo code_master_form_dropdown(array(
        			         "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                             "value" => $feedback->Status,
                             "name" => 'status',
                             "id"	=> 'status',
                             "clazz" => 'input-width',
                             "style" => '',
        			         "has_empty" => true
        			     ));?>
        		</td>
			</tr>
			<tr>
				<th><label for="message"><?php echo lang('feedback.message_label');?></label></th>
				<td>
					<?php echo form_textarea(array('id' => 'Message', 'name' => 'Message', 'value' =>  $feedback->Message, 'rows' => 8)); ?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td>
					<?php $this->load->view('partials/buttons', array('buttons' => array('save', 'cancel'), 'module' => 'feedback'  )); ?>
				</td>
			</tr>
		   </table>
	</div>
</div>	
<?php echo form_close();?>
</div>
</div>

<script type="text/javascript">
CKEDITOR.replace( 'Message' );
</script>
