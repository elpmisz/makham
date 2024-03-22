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
    $sql = "SELECT 
    SUM(IF(a.type = 1,1,0)) income,
    SUM(IF(a.type = 2,1,0)) outcome,
    SUM(IF(a.type IN (1,2),1,0)) total,
    SUM(IF(a.status = 1,1,0)) approve
    FROM inventory.issue a
    WHERE a.status IN (1,2)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function income()
  {
    $sql = "SELECT e.code product_code,e.name product_name,CONCAT('[',e.code,'] ',e.name) product,
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0)) income,
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0)) outcome,
    (
      SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
      SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    ) remain,
    SUM(IF(b.type = 2 AND b.status = 2,a.confirm,0)) issue,
    SUM(IF(c.status IN (3,4,5),a.confirm,0)) purchase,
    SUM(IF(d.status = 1,a.confirm,0)) sale
    FROM inventory.issue_item a
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    LEFT JOIN inventory.purchase c
    ON a.purchase_id = c.id
    LEFT JOIN inventory.sale d
    ON a.sale_id = d.id
    RIGHT JOIN inventory.product e
    ON a.product_id = e.id
    WHERE e.status = 1
    AND a.product_id IS NOT NULL
    GROUP BY e.id
    ORDER BY SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0)) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function outcome()
  {
    $sql = "SELECT e.code product_code,e.name product_name,CONCAT('[',e.code,'] ',e.name) product,
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0)) income,
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0)) outcome,
    (
      SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
      SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    ) remain,
    SUM(IF(b.type = 2 AND b.status = 2,a.confirm,0)) issue,
    SUM(IF(c.status IN (3,4,5),a.confirm,0)) purchase,
    SUM(IF(d.status = 1,a.confirm,0)) sale
    FROM inventory.issue_item a
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    LEFT JOIN inventory.purchase c
    ON a.purchase_id = c.id
    LEFT JOIN inventory.sale d
    ON a.sale_id = d.id
    RIGHT JOIN inventory.product e
    ON a.product_id = e.id
    WHERE e.status = 1
    AND a.product_id IS NOT NULL
    GROUP BY e.id
    ORDER BY SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0)) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function issue_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.type", "b.firstname", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,a.text,
    (
      CASE
        WHEN a.type = 1 THEN 'นำเข้า'
        WHEN a.type = 2 THEN 'เบิกออก'
        ELSE NULL
      END
    ) type_name,
    (
      CASE
        WHEN a.type = 1 THEN 'primary'
        WHEN a.type = 2 THEN 'warning'
        ELSE NULL
      END
    ) type_color,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'รายการถูกยกเลิก'
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
    ON a.user_id = b.id ";

    if (!empty($keyword)) {
      $sql .= " WHERE a.text LIKE '%{$keyword}%' ";
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
}
