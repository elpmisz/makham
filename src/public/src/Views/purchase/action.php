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
    $bom = (isset($_POST['bom']) ? $VALIDATION->input($_POST['bom']) : "");
    $amount = (isset($_POST['amount']) ? $VALIDATION->input($_POST['amount']) : "");
    $machine = (isset($_POST['machine']) ? $VALIDATION->input($_POST['machine']) : "");
    $date = (isset($_POST['date']) ? str_replace("/", "-", $_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime($date)) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $last = $PURCHASE->purchase_last();

    $count = $PURCHASE->purchase_count([$bom, $machine, $amount, $date]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/purchase");
    }

    $PURCHASE->purchase_insert([$last, $user_id, $bom, $machine, $amount, $date, $text]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $machine = (isset($_POST['machine']) ? $VALIDATION->input($_POST['machine']) : "");
    $date = (isset($_POST['date']) ? str_replace("/", "-", $_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime($date)) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $PURCHASE->purchase_update([$machine, $date, $text, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
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

    $PURCHASE->purchase_approve([$status, $uuid]);
    $PURCHASE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "product") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    $PURCHASE->purchase_approve([$status, $uuid]);
    $PURCHASE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "process") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $confirm = (isset($_POST['confirm']) ? $VALIDATION->input($_POST['confirm']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    $PURCHASE->purchase_process([$confirm, $status, $uuid]);
    $PURCHASE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "check") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $requester = (isset($_POST['requester']) ? $VALIDATION->input($_POST['requester']) : "");
    $product_id = (isset($_POST['product_id']) ? $VALIDATION->input($_POST['product_id']) : "");
    $bom = (isset($_POST['bom']) ? $VALIDATION->input($_POST['bom']) : "");
    $ticket = (isset($_POST['ticket']) ? $VALIDATION->input($_POST['ticket']) : "");
    $confirm = (isset($_POST['confirm']) ? $VALIDATION->input($_POST['confirm']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    if (intval($status) === 5) {
      $last = $ISSUE->issue_last();
      $ISSUE->issue_purchase([$last, "สั่งผลิตตาม เลขที่ใบ {$ticket}", $requester]);
      $issue_id = $ISSUE->last_insert_id();
      $ISSUE->item_purchase([$issue_id, $product_id, $confirm, $confirm]);
      $ISSUE->text_insert([$issue_id, $user_id, $remark, 2]);
    }

    $PURCHASE->purchase_approve([$status, $uuid]);
    $PURCHASE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
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

if ($action === "upload") {
  try {
    $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '');
    $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '');
    $file_allow = ["xls", "xlsx", "csv"];
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

    if (!in_array($file_extension, $file_allow)) :
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/purchase");
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

        $count = $PURCHASE->uuid_count([$uuid]);

        if (intval($count) > 0) {
          $PURCHASE->purchase_update([$name, $text, $status, $uuid]);
        } else {
          $PURCHASE->purchase_insert([$name, $text]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/purchase");
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

if ($action === "product-data") {
  try {
    $result = $PURCHASE->product_data();
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
