<?php
require_once 'params.php';
require_once '../thirdParty/phpqrcode/phpqrcode.php';

$params = Params::parse();

if ($params->keyExists("qrCodeContent"))
{
   QRCode::png($params->get("qrCodeContent"));
}
?>