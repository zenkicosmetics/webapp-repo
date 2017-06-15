<?php if (isset($buttons) && is_array($buttons)): ?>

	<?php 
	
		// What type of buttons?
		if(isset($button_type) && $button_type == 'primary'):
			$btn_class = 'btn';
		elseif(isset($button_type) && $button_type == 'secondary'):
			$btn_class = 'button';
		else:
			// Default to primary
			$btn_class = 'btn';
		endif;
	
	?>

	<?php foreach ($buttons as $key => $button): ?>
		<?php
		/**
		 * @var		$extra	array associative
		 * @since	1.2.0-beta2
		 */ ?>
		<?php $extra	= NULL; ?>
		<?php $button	= ! is_numeric($key) && ($extra = $button) ? $key : $button; ?>

		<?php switch ($button) :
			case 'delete': 
				if($btn_class == 'btn') $btn_class .= ' red';
			
			?>
				<button type="submit" name="btnAction" value="delete" class="<?php echo $btn_class; ?> confirm">
					<span>Delete</span>
				</button>
				<?php break;
			case 'save':?>
			    <button type="submit" name="btnAction" value="<?php echo $button ?>" class="<?php echo $btn_class; ?> blue admin-button">
					<span>Save</span>
				</button>
				<?php break;
			case 'save_exit':?>
			    <button type="submit" name="btnAction" value="<?php echo $button ?>" class="<?php echo $btn_class; ?> blue">
					<span>Save & Exit</span>
				</button>
				<?php break;
			case 'upload': ?>
				<button type="submit" name="btnAction" value="<?php echo $button ?>" class="<?php echo $btn_class; ?> blue">
					<span><?php echo lang('buttons.' . $button); ?></span>
				</button>
				<?php break;
			case 'cancel':
				if($btn_class == 'btn') $btn_class .= ' gray';
				$uri = 'admin/' . $module;
				echo anchor($uri, 'Cancel', 'class="admin-button '.$btn_class. ' ' . $button . '"');
				break;

			/**
			 * @var		$id scalar - optionally can be received from an associative key from array $extra
			 * @since	1.2.0-beta2
			 */
			case 'edit':
				$id = is_array($extra) && array_key_exists('id', $extra) ? '/' . $button . '/' . $extra['id'] : NULL;
				if($btn_class == 'btn') $btn_class .= ' gray';

				echo anchor('admin/' . $module . $id, 'Edit', 'class="'.$btn_class.' ' . $button . '"');
				break; ?>

		<?php endswitch; ?>
	<?php endforeach; ?>
<?php endif; ?>
