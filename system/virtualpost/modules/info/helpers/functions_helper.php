<?php defined('BASEPATH') OR exit('No direct script access allowed');

function calculateVolumeWeightOfPackage(array $package)
{
    // Height, Width, Length
    $H = $package[ShippingConfigs::PACKAGE_HEIGHT];
    $W = $package[ShippingConfigs::PACKAGE_WIDTH];
    $L = $package[ShippingConfigs::PACKAGE_LENGTH];

    // Volume weight (VW)
    $VW = round((($H * $W * $L) * 1.2) / ShippingConfigs::FEDEX_FACTOR_B, 2);

    return $VW;
}