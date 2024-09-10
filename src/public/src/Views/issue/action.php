<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Issue;
use App\Classes\Validation;

$ISSUE = new Issue();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $group = (isset($_POST['group']) ? $VALIDATION->input($_POST['group']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $date))) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $last = $ISSUE->issue_last();

    $count = $ISSUE->issue_count([$type, $text]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/issue");
    }
    $ISSUE->issue_insert([$last, $type, $group, $date, $text, $user_id]);
    $issue_id = $ISSUE->last_insert_id();

    foreach ($_POST['item_product'] as $key => $value) {
      $product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
      $location = (isset($_POST['item_location'][$key]) ? $VALIDATION->input($_POST['item_location'][$key]) : "");
      $store = (isset($_POST['item_store'][$key]) ? $VALIDATION->input($_POST['item_store'][$key]) : "");
      $quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");
      $unit = (isset($_POST['item_unit'][$key]) ? $VALIDATION->input($_POST['item_unit'][$key]) : "");
      $per = $ISSUE->product_per([$product]);

      if (!empty($product)) {
        $count = $ISSUE->item_count([$issue_id, $product, $location, $store, $unit]);
        if (intval($count) === 0) {
          $ISSUE->item_insert([$issue_id, $product, $type, $location, $store, $quantity, $unit]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $group = (isset($_POST['group']) ? $VALIDATION->input($_POST['group']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $date))) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    if (isset($_POST['item_product']) && !empty($_POST['item_product'])) {
      foreach ($_POST['item_product'] as $key => $value) {
        $product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
        $location = (isset($_POST['item_location'][$key]) ? $VALIDATION->input($_POST['item_location'][$key]) : "");
        $store = (isset($_POST['item_store'][$key]) ? $VALIDATION->input($_POST['item_store'][$key]) : "");
        $quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");
        $unit = (isset($_POST['item_unit'][$key]) ? $VALIDATION->input($_POST['item_unit'][$key]) : "");
        $per = $ISSUE->product_per([$product]);
        $quantity = (intval($unit) !== 1 ? $quantity : ($quantity / $per));

        if (!empty($product)) {
          $count = $ISSUE->item_count([$id, $product, $location, $store, $unit]);
          if (intval($count) === 0) {
            $ISSUE->item_insert([$id, $product, $type, $location, $store, $quantity, $unit]);
          }
        }
      }
    }

    $ISSUE->issue_update([$group, $date, $text, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue/edit/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "exchange") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $date))) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $group = (isset($_POST['group']) ? $VALIDATION->input($_POST['group']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $last = $ISSUE->issue_last();

    $count = $ISSUE->issue_count([$type, $text]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/issue");
    }
    $ISSUE->issue_insert([$last, $type, $group, $date, $text, $user_id]);
    $issue_id = $ISSUE->last_insert_id();

    foreach ($_POST['item_product'] as $key => $value) {
      $product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
      $send_location = (isset($_POST['item_send_location'][$key]) ? $VALIDATION->input($_POST['item_send_location'][$key]) : "");
      $send_store = (isset($_POST['item_send_store'][$key]) ? $VALIDATION->input($_POST['item_send_store'][$key]) : "");
      $receive_location = (isset($_POST['item_receive_location'][$key]) ? $VALIDATION->input($_POST['item_receive_location'][$key]) : "");
      $receive_store = (isset($_POST['item_receive_store'][$key]) ? $VALIDATION->input($_POST['item_receive_store'][$key]) : "");
      $quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");
      $unit = (isset($_POST['item_unit'][$key]) ? $VALIDATION->input($_POST['item_unit'][$key]) : "");

      if (!empty($product)) {
        $ISSUE->item_insert([$issue_id, $product, 2, $send_location, $send_store, $quantity, $unit]);
        $ISSUE->item_insert([$issue_id, $product, 1, $receive_location, $receive_store, $quantity, $unit]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update-ex") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $item_product = (!empty($_POST['item_product']) ? $_POST['item_product'] : "");

    if (!empty($item_product)) {
      foreach ($_POST['item_product'] as $key => $value) {
        $product = (isset($_POST['item_product'][$key]) ? $VALIDATION->input($_POST['item_product'][$key]) : "");
        $send = (isset($_POST['item_send'][$key]) ? $VALIDATION->input($_POST['item_send'][$key]) : "");
        $receive = (isset($_POST['item_receive'][$key]) ? $VALIDATION->input($_POST['item_receive'][$key]) : "");
        $quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");

        if (!empty($product)) {
          $ISSUE->item_insert([$id, $product, 2, $send, $quantity]);
          $ISSUE->item_insert([$id, $product, 1, $receive, $quantity]);
        }
      }
    }

    $ISSUE->issue_update([$text, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue/edit/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['id'];
    $exchange = (strpos($item, "-") ? 1 : 2);
    $arr = ($exchange === 1 ? explode("-", $item) : "");
    if (!empty($item)) {
      if ($exchange === 1) {
        $ISSUE->exchange_delete([$arr[0], $arr[1]]);
        echo json_encode(200);
      } else {
        $ISSUE->item_delete([$item]);
        echo json_encode(200);
      }
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

    foreach ($_POST['item_id'] as $key => $value) {
      $item_id = (isset($_POST['item_id'][$key]) ? $VALIDATION->input($_POST['item_id'][$key]) : "");
      $item_confirm = (isset($_POST['item_confirm'][$key]) ? $VALIDATION->input($_POST['item_confirm'][$key]) : "");

      if (!empty($item_id)) {
        $ISSUE->item_confirm([$item_confirm, $item_id]);
      }
    }

    $ISSUE->issue_approve([$status, $uuid]);
    $ISSUE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-ex") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    foreach ($_POST['product'] as $key => $value) {
      $product = (isset($_POST['product'][$key]) ? $VALIDATION->input($_POST['product'][$key]) : "");
      $arr = explode("-", $product);
      $confirm = (isset($_POST['confirm'][$key]) ? $VALIDATION->input($_POST['confirm'][$key]) : "");

      if (!empty($product)) {
        $ISSUE->exchange_confirm([$confirm, $arr[0], $arr[1]]);
      }
    }

    $ISSUE->issue_approve([$status, $uuid]);
    $ISSUE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-update") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");

    $ISSUE->issue_approve([$status, $uuid]);
    $ISSUE->text_insert([$id, $user_id, $remark, $status]);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");

    $count = $ISSUE->auth_count([$user_id, $type]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/issue/auth");
    }

    $ISSUE->auth_insert([$user_id, $type]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue/auth");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $ISSUE->auth_delete([$id]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "issue-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    if (!empty($id)) {
      $ISSUE->issue_delete([$id]);
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
      $VALIDATION->alert("danger", "เฉพาะเอกสาร XLS XLSX CSV!", "/issue");
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

    $last = $ISSUE->issue_last();
    $date = date("Y-m-d");
    $date_text = date("d/m/Y");
    $ISSUE->issue_insert([$last, 1, 0, $date, "ยอดยกมา วันที่ {$date_text}", 1]);
    $issue_id = $ISSUE->last_insert_id();

    foreach ($data as $key => $value) {
      if (!in_array($key, [0])) {
        $code = (isset($value[0]) ? $value[0] : "");
        $name = (isset($value[1]) ? $value[1] : "");
        $location = (isset($value[2]) ? $value[2] : "");
        $location_id = (!empty($location) ? $ISSUE->location_id([$location]) : "");
        $store = (isset($value[3]) ? $value[3] : "");
        $store_id = (!empty($store) ? $ISSUE->store_id([$store]) : "");
        $amount = (isset($value[4]) ? $value[4] : "");
        $per = (isset($value[5]) ? $value[5] : "");

        $product_count = $ISSUE->product_count([$code, $name]);
        if (intval($product_count) === 0) {
          $ISSUE->product_insert([$code, $name, $per, 4]);
        }
        $product_id = (intval($product_count) === 0 ? $ISSUE->product_last_id() : $ISSUE->product_id([$code]));

        $item_count = $ISSUE->item_count([$issue_id, $product_id, $location_id, $store_id, 4]);
        if (intval($item_count) === 0) {
          $ISSUE->item_import([$issue_id, $product_id, 1, $location_id, $store_id, $amount, $amount, 4]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/issue");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "request-data") {
  try {
    $result = $ISSUE->request_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-data") {
  try {
    $result = $ISSUE->approve_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-data") {
  try {
    $result = $ISSUE->manage_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "auth-data") {
  try {
    $result = $ISSUE->auth_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-all-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->item_all_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-50-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->item_50_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-remain-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->item_remain_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "location-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->location_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "store-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->store_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "user-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->user_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "unit-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->unit_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "issue-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ISSUE->issue_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-detail") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = (isset($data['item']) ? $data['item'] : 0);
    $location = (isset($data['location']) ? $data['location'] : 0);
    $store = (isset($data['store']) ? $data['store'] : 0);
    $result = $ISSUE->item_detail([$item], $location, $store);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
