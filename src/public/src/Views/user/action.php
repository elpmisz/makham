<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\User;
use App\Classes\Validation;

$USER = new User();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $email = (isset($_POST['email']) ? $VALIDATION->input($_POST['email']) : "");
    $firstname = (isset($_POST['firstname']) ? $VALIDATION->input($_POST['firstname']) : "");
    $lastname = (isset($_POST['lastname']) ? $VALIDATION->input($_POST['lastname']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");

    $count = $USER->user_count([$email]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/user");
    }

    $default_password = $USER->default_password();
    $hash_password = password_hash($default_password, PASSWORD_DEFAULT);

    $USER->login_insert([$email, $hash_password]);
    $login = $USER->last_insert_id();
    $USER->user_insert([$login, $firstname, $lastname, $contact]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/user");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $email = (isset($_POST['email']) ? $VALIDATION->input($_POST['email']) : "");
    $firstname = (isset($_POST['firstname']) ? $VALIDATION->input($_POST['firstname']) : "");
    $lastname = (isset($_POST['lastname']) ? $VALIDATION->input($_POST['lastname']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");
    $level = (isset($_POST['level']) ? $VALIDATION->input($_POST['level']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $USER->admin_update([$email, $level, $status, $firstname, $lastname, $contact, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/user");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "change") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $password = (isset($_POST['password']) ? $VALIDATION->input($_POST['password']) : "");
    $password2 = (isset($_POST['password2']) ? $VALIDATION->input($_POST['password2']) : "");

    if ($password !== $password2) {
      $VALIDATION->alert("danger", "รหัสผ่านไม่ตรงกัน!", "/user/change");
    }

    $hash_password = password_hash($password, PASSWORD_DEFAULT);
    $USER->change_password([$hash_password, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "profile") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $email = (isset($_POST['email']) ? $VALIDATION->input($_POST['email']) : "");
    $firstname = (isset($_POST['firstname']) ? $VALIDATION->input($_POST['firstname']) : "");
    $lastname = (isset($_POST['lastname']) ? $VALIDATION->input($_POST['lastname']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");

    $USER->user_update([$firstname, $lastname, $contact, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/user/profile");
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
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/unit");
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
        $email = (isset($value[1]) ? $value[1] : "");
        $firstname = (isset($value[2]) ? $value[2] : "");
        $lastname = (isset($value[3]) ? $value[3] : "");
        $contact = (isset($value[4]) ? $value[4] : "");
        $level = (isset($value[5]) ? $value[5] : "");
        $level = ($level === "ผู้ใช้งาน" ? 1 : 9);
        $status = (isset($value[6]) ? $value[6] : "");
        $status = ($status === "ใช้งาน" ? 1 : 2);

        $default_password = $USER->default_password();
        $hash_password = password_hash($default_password, PASSWORD_DEFAULT);

        $count = $USER->user_count([$email]);
        if (intval($count) > 0) {
          $USER->admin_update([$email, $level, $status, $firstname, $lastname, $contact, $uuid]);
        } else {
          $USER->admin_insert([$email, $hash_password, $level, $status]);
          $login = $USER->last_insert_id();
          $USER->user_insert([$login, $firstname, $lastname, $contact]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/user");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "user-data") {
  try {
    $result = $USER->user_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
