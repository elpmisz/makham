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
    echo "<pre>";
    print_r($_POST);
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
