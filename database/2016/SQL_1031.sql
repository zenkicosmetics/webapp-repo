ALTER TABLE `cases_verification_usps`
ADD COLUMN `additional_company_local_file_path` VARCHAR(255) NULL DEFAULT NULL AFTER `additional_amazon_file_path`,
ADD COLUMN `additional_company_amazon_file_path` VARCHAR(255) NULL DEFAULT NULL AFTER `additional_company_local_file_path`;