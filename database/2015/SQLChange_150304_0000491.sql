ALTER TABLE location
ADD public_flag tinyint;

UPDATE location SET public_flag = 1