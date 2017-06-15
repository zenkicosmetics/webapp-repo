
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
	('000114', '/var/www/clevvermail_webapp/shared/data/', '/var/www/clevvermail_webapp/shared/data/', 'absolute path for upload file', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'absolute path for upload file.');



========================= script prepare============================

I. Permission:
grant folder '/var/www/clevvermail_webapp/shared/data/' with read/write for apache user.


II. Update script.
1. cases module.
UPDATE cases_verification_company_hard
	SET verification_local_file_path = REPLACE(verification_local_file_path, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, shareholders_local_file_path_01 = REPLACE(shareholders_local_file_path_01, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, shareholders_local_file_path_02 = REPLACE(shareholders_local_file_path_02, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, shareholders_local_file_path_03 = REPLACE(shareholders_local_file_path_03, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, shareholders_local_file_path_04 = REPLACE(shareholders_local_file_path_04, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')


UPDATE cases_verification_personal_identity
	SET verification_local_file_path = REPLACE(verification_local_file_path, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, driver_license_document_local_file_path = REPLACE(driver_license_document_local_file_path, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')


UPDATE cases_verification_usps
	SET verification_local_file_path = REPLACE(verification_local_file_path, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, id_of_applicant_local_file_path = REPLACE(id_of_applicant_local_file_path, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, license_of_applicant_local_file_path = REPLACE(license_of_applicant_local_file_path, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')
	, additional_local_file_path = REPLACE(additional_local_file_path, '/home/developer/www/app/system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/')


2. scan module
UPDATE envelope_files
	SET local_file_name = REPLACE(local_file_name, 'system/virtualpost/uploads/', '/var/www/clevvermail_webapp/shared/data/');

3. invoice:

UPDATE invoice_summary
	SET invoice_file_path = REPLACE(invoice_file_path, 'system/virtualpost/downloads/', '/var/www/clevvermail_webapp/shared/data/');
	
UPDATE invoices_pdf_job_hist
	SET local_filepath = REPLACE(local_filepath, 'system/virtualpost/downloads/', '/var/www/clevvermail_webapp/shared/data/');












