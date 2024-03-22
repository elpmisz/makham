<?php

namespace App\Classes;

use PDO;

class DashboardProduct
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

  public function product_card()
  {
    $sql = "SELECT SUM(IF(a.category = 1,1,0)) rm,
    SUM(IF(a.category = 2,1,0)) mx,
    SUM(IF(a.category = 3,1,0)) pk,
    SUM(IF(a.category = 4,1,0)) fg,
    COUNT(*) total
    FROM inventory.product a
    LEFT JOIN inventory.category b
    ON a.category = b.id
    WHERE a.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function category()
  {
    $sql = "SELECT b.name category,COUNT(*) total
    FROM inventory.product a
    LEFT JOIN inventory.category b
    ON a.category = b.id
    WHERE a.status = 1
    GROUP BY a.category
    ORDER BY COUNT(*) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function location()
  {
    $sql = "SELECT b.name location,COUNT(*) total
    FROM inventory.product a
    LEFT JOIN inventory.location b
    ON a.location = b.id
    WHERE a.status = 1
    GROUP BY a.location
    ORDER BY COUNT(*) DESC
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

    $column = [
      "e.code", "e.name",
      "SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0))",
      "SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))",
      "(
      SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
      SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    )",
      "SUM(IF(b.type = 2 AND b.status = 2,a.confirm,0))",
      "SUM(IF(c.status IN (3,4,5),a.confirm,0))",
      "SUM(IF(d.status = 1,a.confirm,0))"
    ];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT e.uuid,e.code product_code,e.name product_name,CONCAT('[',e.code,'] ',e.name) product,
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0)) income,
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0)) outcome,
    (
      SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
      SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    ) remain,
    SUM(IF(b.type = 2 AND b.status = 2,a.confirm,0)) issue,
    SUM(IF(c.status IN (3,4,5),a.confirm,0)) purchase,
    SUM(IF(d.status = 1,a.confirm,0)) sale,
    IF(e.status = 1,'ใช้งาน','ระงับการใช้งาน') status_name,
    IF(e.status = 1,'success','danger') status_color
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
    AND a.product_id IS NOT NULL ";

    if (!empty($keyword)) {
      $sql .= " WHERE a.text LIKE '%{$keyword}%' ";
    }

    $sql .= " GROUP BY e.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY e.code ASC ";
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
      $status = "<a href='/product/complete/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light' target='_blank'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['product_code'],
        $row['product_name'],
        $row['income'],
        $row['outcome'],
        $row['remain'],
        $row['issue'],
        $row['purchase'],
        $row['sale'],
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
