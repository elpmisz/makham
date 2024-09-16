<?php

namespace App\Classes;

use PDO;

class DashboardPurchase
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "DASHBOARD CLASS";
  }

  public function purchase_card()
  {
    $sql = "SELECT 
    FORMAT(SUM(IF(DATE(a.created) = DATE(NOW()),a.confirm,0)),2) dd,
    FORMAT(SUM(IF(YEAR(a.created) = YEAR(NOW()) AND MONTH(a.created) = MONTH(NOW()),a.confirm,0)),2) mm,
    FORMAT(SUM(IF(YEAR(a.created) = YEAR(NOW()),a.confirm,0)),2) yy,
    FORMAT(SUM(a.confirm ),2) total
    FROM inventory.purchase a
    WHERE a.status IN (3,4,5)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function purchase_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "c.name", "a.machine", "a.amount", "a.confirm", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.`uuid`,CONCAT('PO',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(c.firstname,' ',c.lastname) fullname,
    a.customer_id,CONCAT('คุณ',d.name) customer_name,
    a.amount,a.machine,a.per,
    DATE_FORMAT(a.date_produce,'%d/%m/%Y') produce,
    DATE_FORMAT(a.date_delivery,'%d/%m/%Y') delivery,
    GROUP_CONCAT(e.name) products, 
    a.`text`,
    (
    CASE 
      WHEN a.status = 1 THEN 'view'
      WHEN a.status = 2 THEN 'process'
      WHEN a.status IN (3,4,5) THEN 'complete'
      ELSE NULL
    END
    ) page,
    (
    CASE 
      WHEN a.status = 1 THEN 'รอเบิกวัถุดิบ'
      WHEN a.status = 2 THEN 'กำลังผลิต'
      WHEN a.status = 3 THEN 'รอตรวจสอบ'
      WHEN a.status = 4 THEN 'ผ่านการตรวจสอบ'
      WHEN a.status = 5 THEN 'ระงับการใช้งาน'
      ELSE NULL
    END
    ) status_name,
    (
    CASE 
      WHEN a.status = 1 THEN 'info'
      WHEN a.status = 2 THEN 'primary'
      WHEN a.status = 3 THEN 'warning'
      WHEN a.status = 4 THEN 'success'
      WHEN a.status = 5 THEN 'danger'
      ELSE NULL
    END
    ) status_color,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created 
    FROM inventory.purchase a 
    LEFT JOIN inventory.purchase_item b 
    ON a.id  = b.purchase_id  
    LEFT JOIN inventory.`user` c 
    ON a.user_id = c.login 
    LEFT JOIN inventory.customer d 
    ON a.customer_id = d.id 
    LEFT JOIN inventory.product e 
    ON b.product_id = e.id
    WHERE b.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND a.name LIKE '%{$keyword}%' ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.date_produce DESC ";
    }

    $sql2 = "";
    if ($limit_length) {
      $sql2 .= " LIMIT {$limit_start}, {$limit_length}";
    }

    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $filter = $stmt->rowCount();
    $stmt = $this->dbcon->prepare($sql . $sql2);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($result as $row) {
      $status = "<a href='/purchase/{$row['page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['customer_name'],
        $row['amount'],
        $row['produce'],
        $row['delivery'],
        str_replace(",", "<br>", $row['products']),
        str_replace("\n", "<br>", $row['text']),
        $row['created'],
      ];
    }

    $output = [
      "draw"    => $draw,
      "recordsTotal"  =>  $total,
      "recordsFiltered" => $filter,
      "data"    => $data
    ];
    return $output;
  }

  public function download($bom, $start, $end)
  {
    $sql = "SELECT a.uuid,CONCAT('PR',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,c.name bom_name,DATE_FORMAT(a.date, '%d/%m/%Y') date,
    a.machine,a.amount,a.confirm,a.text,
    (
    CASE
      WHEN a.status = 1 THEN 'รอการอนุมัติ'
      WHEN a.status = 2 THEN 'รอเบิกวัตถุดิบ'
      WHEN a.status = 3 THEN 'กำลังผลิต'
      WHEN a.status = 4 THEN 'รอตรวจสอบ'
      WHEN a.status = 5 THEN 'ผ่านการตรวจสอบ'
      WHEN a.status = 6 THEN 'ระงับการใช้งาน'
      ELSE NULL
    END
    ) status_name,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.bom c
    ON a.bom = c.id
    WHERE a.id != '' ";
    if (!empty($bom)) {
      $sql .= " AND a.bom = '{$bom}' ";
    }
    if (!empty($start)) {
      $sql .= " AND DATE(a.date) BETWEEN STR_TO_DATE('{$start}','%d/%m/%Y') AND STR_TO_DATE('{$end}','%d/%m/%Y') ";
    }
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }
}
