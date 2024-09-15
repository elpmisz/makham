<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Product;
use App\Classes\Validation;

$PRODUCT = new Product();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $code = (isset($_POST['code']) ? $VALIDATION->input($_POST['code']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $cost = (isset($_POST['cost']) ? $VALIDATION->input($_POST['cost']) : "");
    $price = (isset($_POST['price']) ? $VALIDATION->input($_POST['price']) : "");
    $min = (isset($_POST['min']) ? $VALIDATION->input($_POST['min']) : "");
    $max = (isset($_POST['max']) ? $VALIDATION->input($_POST['max']) : "");
    $bom = (isset($_POST['bom']) ? $VALIDATION->input($_POST['bom']) : "");
    $supplier = (isset($_POST['supplier']) ? $VALIDATION->input($_POST['supplier']) : "");
    $unit = (isset($_POST['unit']) ? $VALIDATION->input($_POST['unit']) : "");
    $per = (isset($_POST['per']) ? $VALIDATION->input($_POST['per']) : "");
    $brand = (isset($_POST['brand']) ? $VALIDATION->input($_POST['brand']) : "");
    $category = (isset($_POST['category']) ? $VALIDATION->input($_POST['category']) : "");
    $store = (isset($_POST['store']) ? $VALIDATION->input($_POST['store']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $count = $PRODUCT->product_count([$code, $name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/product");
    }

    $PRODUCT->product_insert([$code, $name, $cost, $price, $min, $max, $per, $bom, $supplier, $unit, $brand, $category, $store, $text]);
    $product_id = $PRODUCT->last_insert_id();

    foreach ($_FILES['file']['name'] as $key => $row) {
      $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'][$key] : "");
      $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'][$key] : "");
      $file_random = md5(microtime());
      $file_image = ["png", "jpeg", "jpg"];
      $file_document = ["pdf", "doc", "docx", "xls", "xlsx"];
      $file_allow = array_merge($file_image);
      $file_extension = pathinfo(strtolower($file_name), PATHINFO_EXTENSION);

      if (!empty($file_name)) {
        if (in_array($file_extension, $file_allow)) {
          if (in_array($file_extension, $file_document)) {
            $file_rename = "{$file_random}.{$file_extension}";
            $file_path = (__DIR__ . "/../../Publics/product/{$file_rename}");
            move_uploaded_file($file_tmp, $file_path);
          }
          if (in_array($file_extension, $file_image)) {
            $file_rename = "{$file_random}.webp";
            $file_path = (__DIR__ . "/../../Publics/product/{$file_rename}");
            $VALIDATION->image_upload($file_tmp, $file_path);
          }
          $PRODUCT->image_insert([$product_id, $file_rename]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/product");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $product_id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $code = (isset($_POST['code']) ? $VALIDATION->input($_POST['code']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $cost = (isset($_POST['cost']) ? $VALIDATION->input($_POST['cost']) : "");
    $price = (isset($_POST['price']) ? $VALIDATION->input($_POST['price']) : "");
    $min = (isset($_POST['min']) ? $VALIDATION->input($_POST['min']) : "");
    $max = (isset($_POST['max']) ? $VALIDATION->input($_POST['max']) : "");
    $bom = (isset($_POST['bom']) ? $VALIDATION->input($_POST['bom']) : "");
    $supplier = (isset($_POST['supplier']) ? $VALIDATION->input($_POST['supplier']) : "");
    $unit = (isset($_POST['unit']) ? $VALIDATION->input($_POST['unit']) : "");
    $per = (isset($_POST['per']) ? $VALIDATION->input($_POST['per']) : "");
    $brand = (isset($_POST['brand']) ? $VALIDATION->input($_POST['brand']) : "");
    $category = (isset($_POST['category']) ? $VALIDATION->input($_POST['category']) : "");
    $store = (isset($_POST['store']) ? $VALIDATION->input($_POST['store']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    foreach ($_FILES['file']['name'] as $key => $row) {
      $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'][$key] : "");
      $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'][$key] : "");
      $file_random = md5(microtime());
      $file_image = ["png", "jpeg", "jpg"];
      $file_document = ["pdf", "doc", "docx", "xls", "xlsx"];
      $file_allow = array_merge($file_image);
      $file_extension = pathinfo(strtolower($file_name), PATHINFO_EXTENSION);

      if (!empty($file_name)) {
        if (in_array($file_extension, $file_allow)) {
          if (in_array($file_extension, $file_document)) {
            $file_rename = "{$file_random}.{$file_extension}";
            $file_path = (__DIR__ . "/../../Publics/product/{$file_rename}");
            move_uploaded_file($file_tmp, $file_path);
          }
          if (in_array($file_extension, $file_image)) {
            $file_rename = "{$file_random}.webp";
            $file_path = (__DIR__ . "/../../Publics/product/{$file_rename}");
            $VALIDATION->image_upload($file_tmp, $file_path);
          }
          $PRODUCT->image_insert([$product_id, $file_rename]);
        }
      }
    }

    $PRODUCT->product_update([$code, $name, $cost, $price, $min, $max, $per, $bom, $supplier, $unit, $brand, $category, $store, $text, $status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/product/edit/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "image-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    if (!empty($id)) {
      $PRODUCT->image_delete([$id]);
      $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!");
      echo json_encode(200);
    } else {
      $VALIDATION->alert("danger", "ระบบมีปัญหา กรุณาลองใหม่อีกครั้ง!");
      echo json_encode(500);
    }
    echo json_encode($id);
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
        $code = (isset($value[0]) ? $value[0] : "");
        $name = (isset($value[1]) ? $value[1] : "");
        $cost = (isset($value[2]) ? $value[2] : "");
        $price = (isset($value[3]) ? $value[3] : "");
        $min = (isset($value[4]) ? $value[4] : "");
        $max = (isset($value[5]) ? $value[5] : "");
        $per = (isset($value[6]) ? $value[6] : "");
        $text = (isset($value[8]) ? $value[8] : "");
        $supplier = (isset($value[9]) ? $value[9] : "");
        $supplier = (!empty($supplier) ? $PRODUCT->supplier_id([$supplier]) : "");
        $unit = (isset($value[10]) ? $value[10] : "");
        $unit = (!empty($unit) ? $PRODUCT->unit_id([$supplier]) : "");
        $brand = (isset($value[11]) ? $value[11] : "");
        $brand = (!empty($brand) ? $PRODUCT->brand_id([$brand]) : "");
        $category = (isset($value[12]) ? $value[12] : "");
        $category = (!empty($category) ? $PRODUCT->category_id([$category]) : "");
        $store = (isset($value[13]) ? $value[13] : "");
        $store = (!empty($store) ? $PRODUCT->store_id([$store]) : "");
        $status = (isset($value[14]) ? $value[14] : "");
        $status = ($status === "ใช้งาน" ? 1 : 2);
        $bom = "";

        $count = $PRODUCT->product_count([$code, $name]);

        if (intval($count) > 0) {
          $PRODUCT->product_update([$code, $name, $cost, $price, $min, $max, $per, $bom, $supplier, $unit, $brand, $category, $store, $text, $status, $code, $name]);
        } else {
          $PRODUCT->product_insert([$code, $name, $cost, $price, $min, $max, $per, $bom, $supplier, $unit, $brand, $category, $store, $text]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/product");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "product-data") {
  try {
    $location = (isset($_POST['location']) ? $VALIDATION->input($_POST['location']) : "");
    $store = (isset($_POST['store']) ? $VALIDATION->input($_POST['store']) : "");
    $result = $PRODUCT->product_data($location, $store);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "issue-data") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $result = $PRODUCT->issue_data($uuid);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "bom-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PRODUCT->bom_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "supplier-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PRODUCT->supplier_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "unit-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PRODUCT->unit_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "brand-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PRODUCT->brand_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "location-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PRODUCT->location_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "category-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PRODUCT->category_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "store-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PRODUCT->store_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
