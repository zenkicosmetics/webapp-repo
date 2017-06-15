update pricing set item_unit = 'pieces' where item_name='included_incomming_items';
update pricing set item_unit = 'no/yes' where item_name='hand_sorting_of_advertising';
update pricing set item_unit = 'pieces' where item_name='envelope_scanning_front';
update pricing set item_unit = 'pieces' where item_name='included_opening_scanning';
update pricing set item_unit = 'pieces' where item_name='trashing_items';
update pricing set item_unit = 'EUR/month' where item_name='postbox_fee';
update pricing set item_unit = 'EUR/month' where item_name='postbox_fee_as_you_go';
update pricing set item_unit = 'EUR/hour' where item_name='special_requests_charge_by_time';
update pricing set item_unit = 'no/yes' where item_name='cloud_service_connection';

