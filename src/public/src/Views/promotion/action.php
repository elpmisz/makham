<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Promotion;
use App\Classes\Validation;

$PROMOTION = new Promotion();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $discount = (isset($_POST['discount']) ? $VALIDATION->input($_POST['discount']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $conv = (!empty($date) ? explode("-", $date) : "");
    $start = date("Y-m-d", strtotime(str_replace("/", "-", trim($conv[0]))));
    $end = date("Y-m-d", strtotime(str_replace("/", "-", trim($conv[1]))));

    $count = $PROMOTION->promotion_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/promotion");
    }

    $PROMOTION->promotion_insert([$name, $date, $start, $end, $discount, $type, $text]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/promotion");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $discount = (isset($_POST['discount']) ? $VALIDATION->input($_POST['discount']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $conv = (!empty($date) ? explode("-", $date) : "");
    $start = date("Y-m-d", strtotime(str_replace("/", "-", trim($conv[0]))));
    $end = date("Y-m-d", strtotime(str_replace("/", "-", trim($conv[1]))));

    $PROMOTION->promotion_update([$name, $date, $start, $end, $discount, $type, $text, $status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/promotion");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "upload") {
  try {
    $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '');
    $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '');
    $file_allow = ["xls", "xlsx", "csv"];
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

    if (!in_array($file_extension, $file_allow)) :
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/promotion");
    endif;

    if ($file_extension === "xls") {
      $READER = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
    } elseif ($file_extension === "xlsx") {
      $READER = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    } else {
      $READER = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
    }

    $READ = $READER->load($file_tmp);
    $result = $READ->getActiveSheet()->toArray();

    $data = [];
    foreach ($result as $value) {
      $data[] = array_map("trim", $value);
    }

    foreach ($data as $key => $value) {
      if (!in_array($key, [0])) {
        $uuid = (isset($value[0]) ? $value[0] : "");
        $name = (isset($value[1]) ? $value[1] : "");
        $date = (isset($value[2]) ? $value[2] : "");
        $discount = (isset($value[3]) ? $value[3] : "");
        $type = (isset($value[4]) ? $value[4] : "");
        $type = ($type === "บาท" ? 1 : 2);
        $text = (isset($value[5]) ? $value[5] : "");
        $status = (isset($value[6]) ? $value[6] : "");
        $status = ($status === "ใช้งาน" ? 1 : 2);

        $count = $PROMOTION->uuid_count([$uuid]);

        if (intval($count) > 0) {
          $PROMOTION->promotion_update([$name, $date, $discount, $type, $text, $status, $uuid]);
        } else {
          $PROMOTION->promotion_insert([$name, $date, $discount, $type, $text]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/promotion");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "promotion-data") {
  try {
    $result = $PROMOTION->promotion_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
