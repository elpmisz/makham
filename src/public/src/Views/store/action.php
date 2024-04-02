<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Store;
use App\Classes\Validation;

$STORE = new Store();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $room = (isset($_POST['room']) ? $VALIDATION->input($_POST['room']) : "");
    $zone = (isset($_POST['zone']) ? $VALIDATION->input($_POST['zone']) : "");
    $floor = (isset($_POST['floor']) ? $VALIDATION->input($_POST['floor']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $count = $STORE->store_count([$room, $zone, $floor]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/store");
    }

    $STORE->store_insert([$room, $zone, $floor, $text]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/store");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $room = (isset($_POST['room']) ? $VALIDATION->input($_POST['room']) : "");
    $zone = (isset($_POST['zone']) ? $VALIDATION->input($_POST['zone']) : "");
    $floor = (isset($_POST['floor']) ? $VALIDATION->input($_POST['floor']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $STORE->store_update([$room, $zone, $floor, $text, $status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/store");
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
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/store");
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
        $room = (isset($value[1]) ? $value[1] : "");
        $floor = (isset($value[2]) ? $value[2] : "");
        $zone = (isset($value[3]) ? $value[3] : "");
        $text = (isset($value[4]) ? $value[4] : "");
        $status = (isset($value[5]) ? $value[5] : "");
        $status = ($status === "ใช้งาน" ? 1 : 2);

        $count = $STORE->store_count([$room, $zone, $floor]);

        if (intval($count) > 0) {
          $STORE->store_update([$room, $zone, $floor, $text, $status, $uuid]);
        } else {
          $STORE->store_insert([$room, $zone, $floor, $text]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/store");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "store-data") {
  try {
    $result = $STORE->store_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
