<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\DashboardIssue;
use App\Classes\Validation;

$DASHBOARD = new DashboardIssue();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "issue-data") {
  try {
    $date = (isset($_POST['date']) ? explode("-", $VALIDATION->input($_POST['date'])) : "");
    $start = (!empty($date[0]) ? trim($date[0]) : "");
    $end = (!empty($date[1]) ? trim($date[1]) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");

    $result = $DASHBOARD->issue_data($type, $start, $end);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "income") {
  try {
    $result = $DASHBOARD->income();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "outcome") {
  try {
    $result = $DASHBOARD->outcome();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
