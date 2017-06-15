<form id="addEditEnvelopeCommentForm" method="post" class="dialog-form" action="<?php echo base_url()?>scans/todo/comment_detail" autocomplete="off">
	<table>
	    <tr>
			<th>Comment</th>
			<td>
                <textarea id="addEditEnvelopeCommentForm_txt" name="text" class="input-width" style="width: 400px; height: 140px;"><?php echo !empty( $envelope_comment)? $envelope_comment->text: ''; ?></textarea>
			</td>
		</tr>
	</table>
	<input type="hidden" id="addEditEnvelopeCommentForm_envelope_id" name="envelope_id" value="<?php echo isset( $envelope_id)? $envelope_id : '';?>" />
</form>
<script type="text/javascript">
$(document).ready( function() {
});
</script>