UPDATE `pricing` 
SET item_value = 7 * item_value,
item_unit = 'days'
WHERE item_name IN ('storing_items_letters', 'storing_items_packages')
and item_unit = 'weeks';

UPDATE `pricing` 
SET item_value = 7 * item_value,
item_unit = 'days'
WHERE item_name IN ('storing_items_letters', 'storing_items_packages')
and item_unit = 'week';