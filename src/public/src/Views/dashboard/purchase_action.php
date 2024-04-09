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
    $date = (isset($_POST['date']) ? explode("-", $VALIDATION->input($_POST['date'])) : "");
    $start = (!empty($date[0]) ? trim($date[0]) : "");
    $end = (!empty($date[1]) ? trim($date[1]) : "");
    $bom = (isset($_POST['bom']) ? $VALIDATION->input($_POST['bom']) : "");

    $result = $DASHBOARD->purchase_data($bom, $start, $end);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "bom-data") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->bom_purchase();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
