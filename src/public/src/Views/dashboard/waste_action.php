<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\DashboardWaste;
use App\Classes\Validation;

$DASHBOARD = new DashboardWaste();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "waste-data") {
  try {
    $date = (isset($_POST['date']) ? explode("-", $VALIDATION->input($_POST['date'])) : "");
    $start = (!empty($date[0]) ? trim($date[0]) : "");
    $end = (!empty($date[1]) ? trim($date[1]) : "");

    $result = $DASHBOARD->waste_data($start, $end);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "item-data") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->waste_item();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
