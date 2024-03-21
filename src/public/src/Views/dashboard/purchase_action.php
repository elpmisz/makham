<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\DashboardPurchase;
use App\Classes\Validation;

$DASHBOARD = new DashboardPurchase();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "purchase-data") {
  try {
    $result = $DASHBOARD->purchase_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "machine-month-data") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->machine_purchase();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "machine-year-data") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->machine_purchase();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
