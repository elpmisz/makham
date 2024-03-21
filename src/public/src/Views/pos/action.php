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
    $customer = (isset($_POST['customer']) ? $VALIDATION->input($_POST['customer']) : "");
    $text = "ขายผ่าน POS";
    $promotion = (isset($_POST['promotion']) ? $VALIDATION->input($_POST['promotion']) : "");
    $vat = (isset($_POST['vat']) ? $VALIDATION->input($_POST['vat']) : "");
    $last = $SALE->sale_last();

    $SALE->sale_insert([$last, $user_id, $customer, $text, $promotion, $vat]);
    $sale_id = $SALE->last_insert_id();

    $total = 0;
    foreach ($_POST['product'] as $key => $value) {
      $product = (isset($_POST['product'][$key]) ? $VALIDATION->input($_POST['product'][$key]) : "");
      $price = (isset($_POST['price'][$key]) ? $VALIDATION->input($_POST['price'][$key]) : "");
      $quantity = (isset($_POST['quantity'][$key]) ? $VALIDATION->input($_POST['quantity'][$key]) : "");

      $total += ($price * $quantity);

      if (!empty($product)) {
        $SALE->item_insert([$sale_id, $product, $price, $quantity, $quantity]);
      }
    }

    $discount = $SALE->discount_view([$promotion]);
    $discount_type = $discount['type'];
    $discount_value = $discount['discount'];

    $total = (intval($discount_type) === 1 ? ($total - $discount_value) : ($total - ($total * $discount_value)));
    $SALE->amount_update([$total, $sale_id]);
    unset($_SESSION['cart']);

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/pos");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "customer-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $SALE->customer_select($keyword);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "product-add") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $pid = $data['product'];

    if (isset($_SESSION['cart'][$pid])) {
      $_SESSION['cart'][$pid]++;
    } else {
      $_SESSION['cart'][$pid] = 1;
    }
    echo json_encode(200);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "product-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $pid = $data['product'];

    if (!empty($pid)) {
      unset($_SESSION['cart'][$pid]);
    }
    echo json_encode(200);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "cart-clear") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    unset($_SESSION['cart']);
    echo json_encode(200);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
