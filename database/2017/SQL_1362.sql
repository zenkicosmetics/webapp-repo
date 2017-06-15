ALTER TABLE envelope_files
ADD ocr_flag tinyint DEFAULT 0 AFTER sync_amazon_flag;