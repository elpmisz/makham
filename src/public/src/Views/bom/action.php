<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Bom;
use App\Classes\Validation;

$BOM = new Bom();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $count = $BOM->bom_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/bom");
    }

    $BOM->bom_insert([$name, $text]);
    $bom_id = $BOM->last_insert_id();

    foreach ($_POST['product_id'] as $key => $value) {
      $product_id = (isset($_POST['product_id'][$key]) ? $VALIDATION->input($_POST['product_id'][$key]) : "");
      $product_quantity = (isset($_POST['product_quantity'][$key]) ? $VALIDATION->input($_POST['product_quantity'][$key]) : "");

      if (!empty($product_id)) {
        $BOM->item_insert([$bom_id, $product_id, $product_quantity]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/bom");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    foreach ($_POST['product_id'] as $key => $value) {
      $product_id = (isset($_POST['product_id'][$key]) ? $VALIDATION->input($_POST['product_id'][$key]) : "");
      $product_quantity = (isset($_POST['product_quantity'][$key]) ? $VALIDATION->input($_POST['product_quantity'][$key]) : "");

      if (!empty($product_id)) {
        $BOM->item_insert([$id, $product_id, $product_quantity]);
      }
    }

    foreach ($_POST['item__id'] as $key => $value) {
      $item__id = (isset($_POST['item__id'][$key]) ? $VALIDATION->input($_POST['item__id'][$key]) : "");
      $product__quantity = (isset($_POST['product__quantity'][$key]) ? $VALIDATION->input($_POST['product__quantity'][$key]) : "");

      if (!empty($item__id)) {
        $BOM->item_update([$product__quantity, $item__id]);
      }
    }

    $BOM->bom_update([$name, $text, $status, $uuid]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/bom");
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
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/bom");
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
        $text = (isset($value[2]) ? $value[2] : "");
        $status = (isset($value[6]) ? $value[6] : "");
        $status = ($status === "ใช้งาน" ? 1 : 2);

        $bom_count = $BOM->uuid_count([$uuid]);
        if (intval($bom_count) > 0) {
          $BOM->bom_update([$name, $text, $status, $uuid]);
        } else {
          $BOM->bom_insert([$name, $text]);
        }

        $bom_id = $BOM->bom_id([$uuid]);
        $product = (isset($value[3]) ? $value[3] : "");
        $product = (!empty($product) ? $BOM->product_id([$product]) : "");
        $item_id = $BOM->item_id([$bom_id, $product]);
        $quantity = (isset($value[4]) ? $value[4] : "");
        $item_count = $BOM->item_count([$bom_id, $product]);
        if (intval($item_count) > 0) {
          $BOM->item_update([$quantity, $item_id]);
        } else {
          $BOM->item_insert([$bom_id, $product, $quantity]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/bom");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "bom-data") {
  try {
    $result = $BOM->bom_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $BOM->item_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-unit") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['item'];
    $result = $BOM->item_unit([$item]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['id'];

    if (!empty($item)) {
      $BOM->item_delete([$item]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
