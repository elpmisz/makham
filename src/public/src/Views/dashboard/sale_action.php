<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\DashboardSale;
use App\Classes\Validation;

$DASHBOARD = new DashboardSale();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "sale-data") {
  try {
    $result = $DASHBOARD->sale_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "month-data") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->sale_month();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "year-data") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->sale_year();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
