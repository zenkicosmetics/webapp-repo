ALTER TABLE `country` 
ADD `letter_national_price` decimal(18,4);

ALTER TABLE `country` 
ADD `letter_international_price` decimal(18,4);

ALTER TABLE `country` 
ADD `package_national_price` decimal(18,4);

ALTER TABLE `country` 
ADD `package_international_price` decimal(18,4);

UPDATE country
SET letter_national_price = 5,
letter_international_price = 10,
package_national_price = 20,
package_international_price = 50