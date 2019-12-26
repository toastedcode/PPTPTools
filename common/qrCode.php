<?php
require_once 'params.php';
require_once '../phpqrcode/phpqrcode.php';

$params = Params::parse();

if ($params->keyExists("qrCodeContent"))
{
   QRCode::png($params->get("qrCodeContent"));
}
?>