<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Quality;
use App\Classes\Validation;

$QUALITY = new Quality();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $date))) : "");
    $receive = (isset($_POST['receive']) ? $VALIDATION->input($_POST['receive']) : "");
    $receive = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $receive))) : "");
    $product_id = (isset($_POST['product_id']) ? $VALIDATION->input($_POST['product_id']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $last = $QUALITY->quality_last();

    $quality_count = $QUALITY->quality_count([$user_id, $date, $receive, $product_id, $text]);
    if (intval($quality_count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/quality");
    }

    $QUALITY->quality_insert([$last, $user_id, $date, $receive, $product_id, $text]);
    $quality_id = $QUALITY->last_insert_id();

    foreach ($_POST['item_start'] as $key => $value) {
      $item_start = (isset($_POST['item_start'][$key]) ? $VALIDATION->input($_POST['item_start'][$key]) : "");
      $item_user = (isset($_POST['item_user'][$key]) ? $VALIDATION->input($_POST['item_user'][$key]) : "");
      $item_sup = (isset($_POST['item_sup'][$key]) ? $VALIDATION->input($_POST['item_sup'][$key]) : "");
      $item_quantity = (isset($_POST['item_quantity'][$key]) ? array_column($_POST['item_quantity'], $key) : "");
      $item_quantity = (!empty($item_quantity) ? implode(",", $item_quantity) : "");
      $item_end = (isset($_POST['item_end'][$key]) ? $VALIDATION->input($_POST['item_end'][$key]) : "");

      $item_count = $QUALITY->item_count([$quality_id, $item_start, $item_user, $item_sup, $item_quantity, $item_end]);
      if (!empty($item_start) && $item_count === 0) {
        $QUALITY->item_insert([$quality_id, $item_start, $item_user, $item_sup, $item_quantity, $item_end]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/quality");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $date))) : "");
    $receive = (isset($_POST['receive']) ? $VALIDATION->input($_POST['receive']) : "");
    $receive = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $receive))) : "");
    $product_id = (isset($_POST['product_id']) ? $VALIDATION->input($_POST['product_id']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    foreach ($_POST['item_start'] as $key => $value) {
      $item_start = (isset($_POST['item_start'][$key]) ? $VALIDATION->input($_POST['item_start'][$key]) : "");
      $item_user = (isset($_POST['item_user'][$key]) ? $VALIDATION->input($_POST['item_user'][$key]) : "");
      $item_sup = (isset($_POST['item_sup'][$key]) ? $VALIDATION->input($_POST['item_sup'][$key]) : "");
      $item_quantity = (isset($_POST['item_quantity'][$key]) ? array_column($_POST['item_quantity'], $key) : "");
      $item_quantity = (!empty($item_quantity) ? implode(",", $item_quantity) : "");
      $item_end = (isset($_POST['item_end'][$key]) ? $VALIDATION->input($_POST['item_end'][$key]) : "");

      $item_count = $QUALITY->item_count([$id, $item_start, $item_user, $item_sup, $item_quantity, $item_end]);
      if (!empty($item_start) && $item_count === 0) {
        $QUALITY->item_insert([$id, $item_start, $item_user, $item_sup, $item_quantity, $item_end]);
      }
    }

    $QUALITY->quality_update([$date, $receive, $product_id, $text, $uuid]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/quality/edit/{$uuid}");
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

    $QUALITY->quality_approve([$status, $uuid]);
    $QUALITY->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/quality");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-update") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    $QUALITY->quality_approve([$status, $uuid]);
    $QUALITY->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/quality/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['id'];
    if (!empty($item)) {
      $QUALITY->item_delete([$item]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "quality-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['id'];
    if (!empty($item)) {
      $QUALITY->quality_delete([$item]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "quality-data") {
  try {
    $result = $QUALITY->quality_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-data") {
  try {
    $result = $QUALITY->approve_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-data") {
  try {
    $result = $QUALITY->manage_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");

    $count = $QUALITY->auth_count([$user_id, $type]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/quality/auth");
    }

    $QUALITY->auth_insert([$user_id, $type]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/quality/auth");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $QUALITY->auth_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-data") {
  try {
    $result = $QUALITY->auth_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "subject") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");

    $count = $QUALITY->subject_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/quality/subject");
    }

    $QUALITY->subject_insert([$name]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/quality/subject");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "subject-data") {
  try {
    $result = $QUALITY->subject_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "subject-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $QUALITY->subject_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "user-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $QUALITY->user_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "sup-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $QUALITY->sup_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "product-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $QUALITY->product_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
