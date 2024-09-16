<?php

namespace App\Classes;

use PDO;

class DashboardWaste
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

  public function waste_card()
  {
    $sql = "SELECT 
    IF(DATE(a.created) = DATE(NOW()),COUNT(a.id),0) dd,
    IF(YEAR(a.created) = YEAR(NOW()) AND MONTH(a.created) = MONTH(NOW()),COUNT(a.id),0) mm,
    IF(YEAR(a.created) = YEAR(NOW()),COUNT(a.id),0) yy,
    COUNT(a.id) total
    FROM inventory.waste a
    WHERE a.status IN (1,2)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function waste_item()
  {
    $sql = "SELECT IF(a.type = 1,CONCAT('[',c.code,'] ',c.name),a.item) item,
    FORMAT(SUM(IF(DATE(b.created) = DATE(NOW()),a.quantity,0)),2) dd,
    FORMAT(SUM(IF(YEAR(b.created) = YEAR(NOW()) AND MONTH(a.created) = MONTH(NOW()),a.quantity,0)),2) mm,
    FORMAT(SUM(IF(YEAR(b.created) = YEAR(NOW()),a.quantity,0)),2) yy,
    FORMAT(SUM(a.quantity),2) total,
    a.remark
    FROM inventory.waste_item a
    LEFT JOIN inventory.waste b
    ON a.waste_id = b.id
    LEFT JOIN inventory.product c
    ON a.item = c.id
    WHERE b.status IN (1,2)
    AND a.status = 1
    GROUP BY a.item
    ORDER BY SUM(a.quantity) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function waste_data($start, $end)
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,a.text,CONCAT('WA',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    IF(a.status = 1,'edit','complete') page,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'primary'
        WHEN a.status = 2 THEN 'success'
        WHEN a.status = 3 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.waste a
    WHERE a.id != '' ";

    if (!empty($keyword)) {
      $sql .= " AND a.text LIKE '%{$keyword}%' ";
    }

    if (!empty($start)) {
      $sql .= " AND DATE(a.created) BETWEEN STR_TO_DATE('{$start}','%d/%m/%Y') AND STR_TO_DATE('{$end}','%d/%m/%Y') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.text ASC ";
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
      $status = "<a href='/waste/{$row['page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['ticket'],
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

  public function download($start, $end)
  {
    $sql = "SELECT a.uuid,CONCAT('WA',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,a.text,
    IF(e.type = 1,f.name,e.item) item,e.quantity,e.remark,
    (
    CASE
      WHEN a.status = 1 THEN 'รอตรวจสอบ'
      WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
      WHEN a.status = 3 THEN 'ระงับการใช้งาน'
      ELSE NULL
    END
    ) status_name,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.waste a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.waste_text c
    ON a.id = c.waste_id
    LEFT JOIN inventory.user d
    ON c.user_id = d.id
    LEFT JOIN inventory.waste_item e
    ON a.id = e.waste_id
    LEFT JOIN inventory.product f
    ON e.item = f.id
    WHERE e.status = 1";
    if (!empty($start)) {
      $sql .= " AND DATE(a.created) BETWEEN STR_TO_DATE('{$start}','%d/%m/%Y') AND STR_TO_DATE('{$end}','%d/%m/%Y') ";
    }
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }
}
