<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Waste;
use App\Classes\Validation;

$WASTE = new Waste();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $purchase_id = (isset($_POST['purchase_id']) ? $VALIDATION->input($_POST['purchase_id']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $last = $WASTE->waste_last();

    $WASTE->waste_insert([$last, $purchase_id, $text, $user_id]);
    $waste_id = $WASTE->last_insert_id();

    foreach ($_POST['item_product'] as $key => $value) {
      $item_product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
      $item_quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");
      $item_remark = (isset($_POST['item_remark'][$key]) ? $VALIDATION->input($_POST['item_remark'][$key]) : "");

      $count = $WASTE->item_count([$id, 1, $item_product]);
      if (!empty($item_product) && $count === 0) {
        $WASTE->item_insert([$waste_id, 1, $item_product, $item_quantity, $item_remark]);
      }
    }

    foreach ($_POST['other_product'] as $key => $value) {
      $other_product = (isset($_POST['other_product'][$key]) ? $VALIDATION->input($_POST['other_product'][$key]) : "");
      $other_quantity = (isset($_POST['other_quantity'][$key]) ? $VALIDATION->input($_POST['other_quantity'][$key]) : "");
      $other_remark = (isset($_POST['other_remark'][$key]) ? $VALIDATION->input($_POST['other_remark'][$key]) : "");

      $count = $WASTE->item_count([$id, 2, $item_product]);
      if (!empty($other_product) && $count === 0) {
        $WASTE->item_insert([$waste_id, 2, $other_product, $other_quantity, $other_remark]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/waste");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $item_product = (!empty($_POST['item_product']) ? $_POST['item_product'] : "");
    $other_product = (!empty($_POST['other_product']) ? $_POST['other_product'] : "");

    if (!empty($item_product)) {
      foreach ($_POST['item_product'] as $key => $value) {
        $item_product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
        $item_quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");
        $item_remark = (isset($_POST['item_remark'][$key]) ? $VALIDATION->input($_POST['item_remark'][$key]) : "");

        $count = $WASTE->item_count([$id, 1, $item_product]);
        if (!empty($item_product) && $count === 0) {
          $WASTE->item_insert([$id, 1, $item_product, $item_quantity, $item_remark]);
        }
      }
    }

    if (!empty($other_product)) {
      foreach ($_POST['other_product'] as $key => $value) {
        $other_product = (isset($_POST['other_product'][$key]) ? $VALIDATION->input($_POST['other_product'][$key]) : "");
        $other_quantity = (isset($_POST['other_quantity'][$key]) ? $VALIDATION->input($_POST['other_quantity'][$key]) : "");
        $other_remark = (isset($_POST['other_remark'][$key]) ? $VALIDATION->input($_POST['other_remark'][$key]) : "");

        $count = $WASTE->item_count([$id, 2, $item_product]);
        if (!empty($other_product) && $count === 0) {
          $WASTE->item_insert([$id, 2, $other_product, $other_quantity, $other_remark]);
        }
      }
    }

    $WASTE->waste_update([$text, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/waste");
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

    $WASTE->waste_approve([$status, $uuid]);
    $WASTE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/waste");
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

    $WASTE->waste_approve([$status, $uuid]);
    $WASTE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/waste/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");

    $count = $WASTE->auth_count([$user_id, $type]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/waste/auth");
    }

    $WASTE->auth_insert([$user_id, $type]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/waste/auth");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $WASTE->auth_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "waste-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $WASTE->waste_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "other") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");

    $count = $WASTE->other_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/waste/other");
    }

    $WASTE->other_insert([$name]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/waste/other");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "other-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $WASTE->other_delete([$id]);
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
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/waste");
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

        $count = $WASTE->uuid_count([$uuid]);

        if (intval($count) > 0) {
          $WASTE->waste_update([$name, $text, $status, $uuid]);
        } else {
          $WASTE->waste_insert([$name, $text]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/waste");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "waste-data") {
  try {
    $result = $WASTE->waste_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-data") {
  try {
    $result = $WASTE->approve_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-data") {
  try {
    $result = $WASTE->manage_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-data") {
  try {
    $result = $WASTE->auth_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "other-data") {
  try {
    $result = $WASTE->other_data();
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
      $WASTE->item_delete([$item]);
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
    $result = $WASTE->user_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "purchase-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $WASTE->purchase_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "other-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $WASTE->other_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
