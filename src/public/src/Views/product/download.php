<?php

use App\Classes\Product;
use App\Classes\Validation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$PRODUCT = new Product();
$VALIDATION = new Validation();
$SPREADSHEET = new Spreadsheet();
$WRITER = new Xlsx($SPREADSHEET);

$result = $PRODUCT->download();

array_walk_recursive($result, "htmldecode");

function htmldecode(&$item, $key)
{
  $item = html_entity_decode($item, ENT_COMPAT, "UTF-8");
}

$columns = ["รหัส", "ชื่อ", "ราคาซื้อ", "ราคาขาย", "MIN", "MAX", "กิโลกรัม/หน่วย", "คงเหลือ", "รายละเอียด", "ผู้จัดจำหน่าย", "หน่วยนับ", "ยี่ห้อ", "สถานะ", "วันที"];

$letters = [];
for ($i = "A"; $i != $VALIDATION->letters(COUNT($columns) + 1); $i++) {
  $letters[] = $i;
}

$columns = array_combine($letters, $columns);

ob_start();
$date = date('Ymd');
$filename = $date . "_products.csv";
header("Content-Encoding: UTF-8");
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$filename}");
ob_end_clean();

$output = fopen("php://output", "w");
fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
fputcsv($output, $columns);

foreach ($result as $data) {
  fputcsv($output, $data);
}

fclose($output);
die();
