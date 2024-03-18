<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Sale;
use App\Classes\Validation;

$SALE = new Sale();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $promotion = (isset($_POST['promotion']) ? $VALIDATION->input($_POST['promotion']) : "");
    $vat = (isset($_POST['vat']) ? $VALIDATION->input($_POST['vat']) : "");
    $last = $SALE->sale_last();

    $SALE->sale_insert([$last, $user_id, $text, $promotion, $vat]);
    $sale_id = $SALE->last_insert_id();

    $total = 0;
    foreach ($_POST['product_id'] as $key => $value) {
      $product = (isset($_POST['product_id'][$key]) ? $VALIDATION->input($_POST['product_id'][$key]) : "");
      $price = (isset($_POST['product_price'][$key]) ? $VALIDATION->input($_POST['product_price'][$key]) : "");
      $quantity = (isset($_POST['product_quantity'][$key]) ? $VALIDATION->input($_POST['product_quantity'][$key]) : "");

      $total += ($price * $quantity);

      if (!empty($product)) {
        $SALE->item_insert([$sale_id, $product, $price, $quantity, $quantity]);
      }
    }
    $SALE->amount_update([$total, $sale_id]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/sale");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    foreach ($_POST['product__id'] as $key => $value) {
      $product = (isset($_POST['product__id'][$key]) ? $VALIDATION->input($_POST['product__id'][$key]) : "");
      $quantity = (isset($_POST['product__quantity'][$key]) ? $VALIDATION->input($_POST['product__quantity'][$key]) : "");

      if (!empty($product)) {
        $SALE->item_update([$quantity, $product]);
      }
    }

    foreach ($_POST['product_id'] as $key => $value) {
      $product = (isset($_POST['product_id'][$key]) ? $VALIDATION->input($_POST['product_id'][$key]) : "");
      $quantity = (isset($_POST['product_quantity'][$key]) ? $VALIDATION->input($_POST['product_quantity'][$key]) : "");

      if (!empty($product)) {
        $count = $SALE->item_count([$id, $product]);
        if (intval($count) === 0) {
          $SALE->item_insert([$id, $product, $quantity]);
        }
      }
    }

    $SALE->sale_update([$text, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/sale");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['id'];

    if (!empty($item)) {
      $SALE->item_delete([$item]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    foreach ($_POST['product'] as $key => $value) {
      $product = (isset($_POST['product'][$key]) ? $VALIDATION->input($_POST['product'][$key]) : "");
      $confirm = (isset($_POST['confirm'][$key]) ? $VALIDATION->input($_POST['confirm'][$key]) : "");

      if (!empty($product)) {
        $SALE->item_confirm([$confirm, $product]);
      }
    }

    $SALE->sale_approve([$status, $uuid]);
    $SALE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/sale");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");

    $count = $SALE->auth_count([$user_id, $type]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/sale/auth");
    }

    $SALE->auth_insert([$user_id, $type]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/sale/auth");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $SALE->auth_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
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
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/sale");
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
        $status = (isset($value[3]) ? $value[3] : "");
        $status = ($status === "ใช้งาน" ? 1 : 2);

        $count = $SALE->uuid_count([$uuid]);

        if (intval($count) > 0) {
          $SALE->sale_update([$name, $text, $status, $uuid]);
        } else {
          $SALE->sale_insert([$name, $text]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/sale");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "sale-data") {
  try {
    $result = $SALE->sale_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-data") {
  try {
    $result = $SALE->approve_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-data") {
  try {
    $result = $SALE->auth_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-all-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $SALE->item_all_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "promotion-detail") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $promotion = $data['promotion'];
    $result = $SALE->promotion_detail([$promotion]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "promotion-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $SALE->promotion_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "product-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $SALE->product_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
