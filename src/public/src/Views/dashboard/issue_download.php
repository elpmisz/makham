<?php
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$action = (isset($param[0]) ? str_replace("+", "/", $param[0]) : "");
$date = (isset($param[1]) ? explode("-", urldecode(str_replace("+", "/", $param[1]))) : "");
$start = (!empty($date[0]) ? trim($date[0]) : "");
$end = (!empty($date[1]) ? trim($date[1]) : "");
$type = (isset($param[2]) ? str_replace("+", "/", $param[2]) : "");

use App\Classes\DashboardIssue;
use App\Classes\Validation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$ISSUE = new DashboardIssue();
$VALIDATION = new Validation();
$SPREADSHEET = new Spreadsheet();
$WRITER = new Xlsx($SPREADSHEET);

if ($action === 'download') {
  $result = $ISSUE->download($type, $start, $end);

  $columns = ["UUID", "เลขที่เอกสาร", "ผู้ทำรายการ", "ประเภท", "วัตถุดิบ", "จำนวน", "รายละเอียด", "สถานะ", "วันที่"];

  $letters = [];
  for ($i = "A"; $i != $VALIDATION->letters(COUNT($columns) + 1); $i++) {
    $letters[] = $i;
  }

  $columns = array_combine($letters, $columns);

  ob_start();
  $date = date('Ymd');
  $filename = $date . "_issues.csv";
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
}
