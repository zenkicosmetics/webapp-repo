ALTER TABLE `customers`
	ADD COLUMN `direct_access_key` VARCHAR(64) NULL AFTER `show_mobile_adv_flag`,
	ADD COLUMN `direct_access_expired` INT NULL AFTER `direct_access_key`;


update emails
set content = concat(content, '
<p>
	&nbsp;</p>
<p>
	-----------------------------</p>
<p>
	Some instructions for verification:</p>
<p>
	- The backside of an ID document does not count as separate ID</p>
<p>
	- 2 different ID documents are required</p>
<p>
	- The ID must be in latin alphabet or have a notarized translation</p>
<p>
	- Tthe CMRA form requires a real signature or a verified digital signature.&nbsp;</p>
<p>
	- The CMRA form must be completely uploaded as one file, not only a scan of the signature field</p>
<p>
	- The name of the IDs must match the postbox name under which you want to receive mail</p>
<p>
	- A bank or credit card does not count as ID document</p>
<hr /><p><a style="color: #187dc6;" href="{direct_access_url}" target="_blank">
<u><span style="color:#187dc6;">
<span style="font-family: arial,helvetica,sans-serif;">Go directly to your ClevverMail Account here ...</span></span></u></a>
</p>
')




