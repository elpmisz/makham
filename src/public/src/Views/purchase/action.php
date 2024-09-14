<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Issue;
use App\Classes\Purchase;
use App\Classes\Validation;

$ISSUE = new Issue();
$PURCHASE = new Purchase();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $customer = (isset($_POST['customer']) ? $VALIDATION->input($_POST['customer']) : "");
    $amount = (isset($_POST['amount']) ? $VALIDATION->input($_POST['amount']) : "");
    $machine = (isset($_POST['machine']) ? $VALIDATION->input($_POST['machine']) : "");
    $per = (isset($_POST['per']) ? $VALIDATION->input($_POST['per']) : "");
    $date_produce = (isset($_POST['date_produce']) ? str_replace("/", "-", $_POST['date_produce']) : "");
    $date_produce = (!empty($date_produce) ? date("Y-m-d", strtotime($date_produce)) : "");
    $date_delivery = (isset($_POST['date_delivery']) ? str_replace("/", "-", $_POST['date_delivery']) : "");
    $date_delivery = (!empty($date_delivery) ? date("Y-m-d", strtotime($date_delivery)) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $last = $PURCHASE->purchase_last();

    $purchase_count = $PURCHASE->purchase_count([$customer, $amount, $machine, $per, $date_produce, $date_delivery]);
    if (intval($purchase_count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/purchase");
    }

    $PURCHASE->purchase_insert([$last, $user_id, $customer, $amount, $machine, $per, $date_produce, $date_delivery, $text]);
    $purchase_id = $PURCHASE->last_insert_id();

    foreach ($_POST['item_product'] as $key => $value) {
      $product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
      $location = (isset($_POST['item_location'][$key]) ? $VALIDATION->input($_POST['item_location'][$key]) : "");
      $store = (isset($_POST['item_store'][$key]) ? $VALIDATION->input($_POST['item_store'][$key]) : "");
      $quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");
      $unit = (isset($_POST['item_unit'][$key]) ? $VALIDATION->input($_POST['item_unit'][$key]) : "");

      if (!empty($product)) {
        $item_count = $PURCHASE->purchase_item_count([$purchase_id, $product, $location, $store, $quantity, $unit]);
        if (intval($item_count) === 0) {
          $PURCHASE->purchase_item_insert([$purchase_id, $product, $location, $store, $quantity, $unit]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $customer = (isset($_POST['customer']) ? $VALIDATION->input($_POST['customer']) : "");
    $amount = (isset($_POST['amount']) ? $VALIDATION->input($_POST['amount']) : "");
    $machine = (isset($_POST['machine']) ? $VALIDATION->input($_POST['machine']) : "");
    $per = (isset($_POST['per']) ? $VALIDATION->input($_POST['per']) : "");
    $date_produce = (isset($_POST['date_produce']) ? str_replace("/", "-", $_POST['date_produce']) : "");
    $date_produce = (!empty($date_produce) ? date("Y-m-d", strtotime($date_produce)) : "");
    $date_delivery = (isset($_POST['date_delivery']) ? str_replace("/", "-", $_POST['date_delivery']) : "");
    $date_delivery = (!empty($date_delivery) ? date("Y-m-d", strtotime($date_delivery)) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $issue_id = (isset($_POST['issue_id']) ? $VALIDATION->input($_POST['issue_id']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    if (isset($_POST['item_product']) && !empty($_POST['item_product'])) {
      foreach ($_POST['item_product'] as $key => $value) {
        $product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
        $location = (isset($_POST['item_location'][$key]) ? $VALIDATION->input($_POST['item_location'][$key]) : "");
        $store = (isset($_POST['item_store'][$key]) ? $VALIDATION->input($_POST['item_store'][$key]) : "");
        $quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");
        $unit = (isset($_POST['item_unit'][$key]) ? $VALIDATION->input($_POST['item_unit'][$key]) : "");

        if (!empty($product)) {
          $item_count = $PURCHASE->purchase_item_count([$purchase_id, $product, $location, $store, $quantity, $unit]);
          if (intval($item_count) === 0) {
            $PURCHASE->purchase_item_insert([$purchase_id, $product, $location, $store, $quantity, $unit]);
          }
        }
      }
    }

    $PURCHASE->purchase_update([$customer, $amount, $machine, $per, $date_produce, $date_delivery, $text, $issue_id, $status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "process") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    foreach ($_POST['item_id'] as $key => $value) {
      $item_id = (isset($_POST['item_id'][$key]) ? $VALIDATION->input($_POST['item_id'][$key]) : "");
      $item_confirm = (isset($_POST['item_confirm'][$key]) ? $VALIDATION->input($_POST['item_confirm'][$key]) : "");

      $PURCHASE->purchase_item_update([$item_confirm, $item_id]);
    }

    $PURCHASE->purchase_process([$status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "check") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $ticket = (isset($_POST['ticket']) ? $VALIDATION->input($_POST['ticket']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $last = $ISSUE->issue_last();
    $ISSUE->issue_purchase([$last, "สั่งผลิตตาม เลขที่ใบ {$ticket}", $user_id]);
    $issue_id = $ISSUE->last_insert_id();
    $ISSUE->text_insert([$issue_id, $user_id, "", 2]);

    foreach ($_POST['item_id'] as $key => $value) {
      $item_id = (isset($_POST['item_id'][$key]) ? $VALIDATION->input($_POST['item_id'][$key]) : "");
      $item_product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
      $item_location = (isset($_POST['item_location'][$key]) ? $VALIDATION->input($_POST['item_location'][$key]) : "");
      $item_store = (isset($_POST['item_store'][$key]) ? $VALIDATION->input($_POST['item_store'][$key]) : "");
      $item_confirm = (isset($_POST['item_confirm'][$key]) ? $VALIDATION->input($_POST['item_confirm'][$key]) : "");

      $PURCHASE->purchase_item_update([$item_confirm, $item_id]);
      $ISSUE->item_purchase([$issue_id, $item_product, $item_location, $item_confirm, $item_confirm]);
    }

    $PURCHASE->purchase_process([$status, $uuid]);
    $PURCHASE->text_insert([$id, $user_id, "ผ่านการตรวจสอบ", $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    $PURCHASE->purchase_process([$status, $uuid]);
    $PURCHASE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");

    $count = $PURCHASE->auth_count([$user_id, $type]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/purchase/auth");
    }

    $PURCHASE->auth_insert([$user_id, $type]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase/auth");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $PURCHASE->auth_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "purchase-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $PURCHASE->purchase_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $PURCHASE->purchase_item_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "purchase-data") {
  try {
    $result = $PURCHASE->purchase_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-data") {
  try {
    $result = $PURCHASE->approve_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-data") {
  try {
    $result = $PURCHASE->manage_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-data") {
  try {
    $result = $PURCHASE->auth_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "bom-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PURCHASE->bom_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "bom-item") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $bom = $data['bom'];
    $result = $PURCHASE->bom_item([$bom]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "machine-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PURCHASE->machine_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "user-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PURCHASE->user_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "customer-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $PURCHASE->customer_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
