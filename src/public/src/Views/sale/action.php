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
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $promotion = (isset($_POST['promotion']) ? $VALIDATION->input($_POST['promotion']) : "");
    $vat = (isset($_POST['vat']) ? $VALIDATION->input($_POST['vat']) : "");
    $last = $SALE->sale_last();

    $SALE->sale_insert([$last, $user_id, $customer, $text, $promotion, $vat]);
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

    if (!empty($promotion)) {
      $discount = $SALE->discount_view([$promotion]);
      $discount_type = $discount['type'];
      $discount_value = $discount['discount'];
      $discount = (intval($discount_type) === 1 ? $discount_value : ($total * $discount_value));
    }
    $discount = (!empty($discount) ? $discount : 0);

    $total = (!empty($promotion) ? ($total - $discount) : $total);
    $SALE->amount_update([$total, $discount, $sale_id]);

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
