<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\DashboardProduct;
use App\Classes\Validation;

$DASHBOARD = new DashboardProduct();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "product-data") {
  try {
    $result = $DASHBOARD->product_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "category") {
  try {
    $result = $DASHBOARD->category();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "location") {
  try {
    $result = $DASHBOARD->location();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
