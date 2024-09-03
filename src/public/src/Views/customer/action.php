<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Customer;
use App\Classes\Validation;

$CUSTOMER = new Customer();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");
    $vat = (isset($_POST['vat']) ? $VALIDATION->input($_POST['vat']) : "");
    $email = (isset($_POST['email']) ? $VALIDATION->input($_POST['email']) : "");
    $address = (isset($_POST['address']) ? $VALIDATION->input($_POST['address']) : "");
    $sub = (isset($_POST['sub']) ? $VALIDATION->input($_POST['sub']) : "");
    $latitude = (isset($_POST['latitude']) ? $VALIDATION->input($_POST['latitude']) : "");
    $longitude = (isset($_POST['longitude']) ? $VALIDATION->input($_POST['longitude']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $count = $CUSTOMER->customer_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/customer");
    }

    $CUSTOMER->customer_insert([$name, $vat, $email, $contact, $address, $sub, $latitude, $longitude, $text]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/customer");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");
    $vat = (isset($_POST['vat']) ? $VALIDATION->input($_POST['vat']) : "");
    $email = (isset($_POST['email']) ? $VALIDATION->input($_POST['email']) : "");
    $address = (isset($_POST['address']) ? $VALIDATION->input($_POST['address']) : "");
    $sub = (isset($_POST['sub']) ? $VALIDATION->input($_POST['sub']) : "");
    $latitude = (isset($_POST['latitude']) ? $VALIDATION->input($_POST['latitude']) : "");
    $longitude = (isset($_POST['longitude']) ? $VALIDATION->input($_POST['longitude']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $CUSTOMER->customer_update([$name, $vat, $email, $contact, $address, $sub, $latitude, $longitude, $text, $status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/customer/edit/{$uuid}");
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
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/customer");
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
        $vat = (isset($value[2]) ? $value[2] : "");
        $email = (isset($value[3]) ? $value[3] : "");
        $contact = (isset($value[4]) ? $value[4] : "");
        $address = (isset($value[5]) ? $value[5] : "");
        $sub = (isset($value[6]) ? $value[6] : "");
        $district = (isset($value[7]) ? $value[7] : "");
        $province = (isset($value[8]) ? $value[8] : "");
        $postal = (isset($value[9]) ? $value[9] : "");
        $text = (isset($value[10]) ? $value[10] : "");
        $status = (isset($value[11]) ? $value[11] : "");
        $status = ($status === "ใช้งาน" ? 1 : 2);

        $count = $CUSTOMER->uuid_count([$uuid]);

        $row = $CUSTOMER->subcode([$sub]);
        $subcode = $row['subcode'];
        if (intval($count) > 0) {
          $CUSTOMER->customer_update([$name, $vat, $email, $contact, $address, $subcode, $text, $status, $uuid]);
        } else {
          $CUSTOMER->customer_insert([$name, $vat, $email, $contact, $address, $subcode, $text]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/customer");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "customer-data") {
  try {
    $result = $CUSTOMER->customer_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "address-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $CUSTOMER->address_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "address-view") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $sub = $data['sub'];
    $result = $CUSTOMER->address_view([$sub]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
