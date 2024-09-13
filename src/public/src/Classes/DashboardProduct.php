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
    $sql = "SELECT COUNT(*) product,
    (SELECT COUNT(*) FROM inventory.location WHERE `status` = 1) location,
    (SELECT COUNT(*) FROM inventory.store WHERE `status` = 1) store,
    (SELECT COUNT(*) FROM inventory.customer WHERE `type` = 2 AND `status` = 1) customer,
    (SELECT COUNT(*) FROM inventory.customer WHERE `type` = 1 AND `status` = 1) supplier
    FROM inventory.product a
    WHERE a.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function category()
  {
    $sql = "SELECT IFNULL(b.name,'ยังไม่ได้จัดหมวดหมู่') category,COUNT(*) total
    FROM inventory.product a
    LEFT JOIN inventory.category b
    ON a.category = b.id
    GROUP BY a.category
    ORDER BY COUNT(*) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function location()
  {
    $sql = "SELECT d.name location, COUNT(DISTINCT a.id) total
    FROM inventory.product a
    LEFT JOIN inventory.issue_item b
    ON a.id = b.product_id 
    LEFT JOIN inventory.issue c
    ON b.issue_id = c.id
    LEFT JOIN inventory.location d
    ON b.location_id = d.id
    WHERE c.status IN (1,2)
    AND b.status = 1
    GROUP BY b.location_id
    ORDER BY COUNT(DISTINCT a.id) DESC
    LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function product_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.product";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = [
      "a.status",
      "a.code",
      "a.name",
      "a.min",
      "
      (
        SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0))
      )
      ",
      "
      (
        SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0))
      )
      ",
      "
      (
        SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0)) -
        SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0))
      ) 
      ",
      "e.name"
    ];

    $category = (!empty($location) ? $location : "");
    $store = (!empty($store) ? $store : "");
    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id product_id,a.uuid product_uuid,a.code product_code,a.name product_name,
    a.cost product_cost,a.price product_price,a.min product_min,a.max product_max,
    (
      SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0))
    ) income,
    (
      SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0))
    ) outcome,
    (
      SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0)) -
      SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,IF(a.unit = b.unit_id,b.quantity,(b.quantity/a.per)),IF(a.unit = b.unit_id,b.confirm,(b.confirm/a.per))),0))
    ) remain,
    a.supplier,d.name supplier_name,
    a.unit,e.name unit_name,
    a.brand,f.name brand_name,
    a.category,g.name category_name,
    a.store,CONCAT(h.room,h.floor,h.zone) store_name,
    IF(a.status = 1,'ใช้งาน','ระงับการใช้งาน') status_name,
    IF(a.status = 1,'success','danger') status_color,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM inventory.product a
    LEFT JOIN inventory.issue_item b
    ON a.id = b.product_id
    LEFT JOIN inventory.issue c
    ON b.issue_id = c.id
    LEFT JOIN inventory.customer d
    ON a.supplier = d.id
    LEFT JOIN inventory.unit e
    ON a.unit = e.id
    LEFT JOIN inventory.brand f
    ON a.brand = f.id 
    LEFT JOIN inventory.category g
    ON a.category = g.id 
    LEFT JOIN inventory.store h
    ON a.store = h.id
    WHERE a.id != '' ";

    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.code LIKE '%{$keyword}%' OR d.name LIKE '%{$keyword}%' OR e.name LIKE '%{$keyword}%' OR f.name LIKE '%{$keyword}%' OR g.name LIKE '%{$keyword}%' OR CONCAT(h.room,h.floor,h.zone) LIKE '%{$keyword}%') ";
    }
    if (!empty($location)) {
      $sql .= " AND b.location_id = '{$location}' ";
    }
    if (!empty($store)) {
      $sql .= " AND a.store = '{$store}' ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.code ASC ";
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
      $status = "<a href='/product/edit/{$row['product_uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['product_code'],
        $row['product_name'],
        $row['product_min'],
        $row['income'],
        $row['outcome'],
        $row['remain'],
        $row['unit_name'],
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
