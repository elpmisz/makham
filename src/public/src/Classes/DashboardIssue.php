<?php

namespace App\Classes;

use PDO;

class DashboardIssue
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

  public function issue_card()
  {
    $sql = "SELECT COUNT(*) total,
    SUM(IF(a.type = 1,1,0)) income,
    SUM(IF(a.type = 2,1,0)) outcome,
    SUM(IF(a.type = 3,1,0)) exchange
    FROM inventory.issue a
    WHERE a.status IN (1,2)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function income()
  {
    $sql = "SELECT c.uuid,CONCAT('[',c.code,'] ',c.name) item,SUM(a.confirm) total
    FROM inventory.issue_item a
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    WHERE b.type = 1
    AND a.status = 1
    AND b.status IN (1,2)
    GROUP BY a.product_id
    ORDER BY SUM(a.confirm) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function outcome()
  {
    $sql = "SELECT c.uuid,CONCAT('[',c.code,'] ',c.name) item,SUM(a.confirm) total
    FROM inventory.issue_item a
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    WHERE b.type = 2
    AND a.status = 1
    AND b.status IN (1,2)
    GROUP BY a.product_id
    ORDER BY SUM(a.confirm) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function issue_data($type, $start, $end)
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "a.type", "b.firstname", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,a.text,
    IF(a.status = 1,'edit','complete') page,
    (
      CASE
        WHEN a.type = 1 THEN 'นำเข้า'
        WHEN a.type = 2 THEN 'เบิกออก'
        WHEN a.type = 3 THEN 'โอนย้าย'
        ELSE NULL
      END
    ) type_name,
    (
      CASE
        WHEN a.type = 1 THEN 'success'
        WHEN a.type = 2 THEN 'primary'
        WHEN a.type = 3 THEN 'info'
        ELSE NULL
      END
    ) type_color,
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
    FROM inventory.issue a 
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    WHERE a.id != '' ";

    if (!empty($keyword)) {
      $sql .= " AND (a.text LIKE '%{$keyword}%') ";
    }

    if (!empty($type)) {
      $sql .= " AND a.type = '{$type}' ";
    }

    if (!empty($start)) {
      $sql .= " AND DATE(a.created) BETWEEN STR_TO_DATE('{$start}','%d/%m/%Y') AND STR_TO_DATE('{$end}','%d/%m/%Y') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.created DESC ";
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
      $status = "<a href='/issue/complete/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light' target='_blank'>{$row['status_name']}</a>";
      $type = "<span class='badge badge-{$row['type_color']}'>{$row['type_name']}</span>";
      $data[] = [
        $status,
        $row['ticket'],
        $type,
        $row['fullname'],
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

  public function download($type, $start, $end)
  {
    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(d.firstname,' ',d.lastname) username,
    (
    CASE
      WHEN a.type = 1 THEN 'นำเข้า'
      WHEN a.type = 2 THEN 'เบิกออก'
      WHEN a.type = 3 THEN 'โอนย้าย'
      ELSE NULL
    END
    ) type_name,
    c.name product_name,b.confirm,a.text,
    (
    CASE
      WHEN a.status = 1 THEN 'รอตรวจสอบ'
      WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
      WHEN a.status = 3 THEN 'ระงับการใช้งาน'
      ELSE NULL
    END
    ) status_name,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.issue a
    LEFT JOIN inventory.issue_item b
    ON a.id = b.issue_id
    LEFT JOIN inventory.product c
    ON b.product_id = c.id
    LEFT JOIN inventory.user d
    ON a.user_id = d.id
    WHERE b.status = 1 ";
    if (!empty($type)) {
      $sql .= " AND a.type = '{$type}' ";
    }
    if (!empty($start)) {
      $sql .= " AND DATE(a.created) BETWEEN STR_TO_DATE('{$start}','%d/%m/%Y') AND STR_TO_DATE('{$end}','%d/%m/%Y') ";
    }
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }
}
