<?php

namespace App\Classes;

use PDO;

class Sale
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "SALE CLASS";
  }

  public function sale_last()
  {
    $sql = "SELECT IFNULL(MAX(a.last) + 1,1) last
    FROM inventory.sale a
    WHERE YEAR(a.created) = YEAR(NOW())";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return (isset($row['last']) ? $row['last'] : "");
  }

  public function amount_update($data)
  {
    $sql = "UPDATE inventory.sale SET
    amount  = ?
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function sale_insert($data)
  {
    $sql = "INSERT INTO inventory.sale(uuid,last,user_id,text,promotion,vat) VALUES(uuid(),?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_insert($data)
  {
    $sql = "SELECT a.uuid,a.text,a.user_id,CONCAT(b.firstname,' ',b.lastname) username,
    a.promotion,c.name promotion_name,c.discount,a.vat,a.amount,
    ((a.amount * c.discount) / 100) discount_amount,
    (a.amount - ((a.amount * c.discount) / 100)) discount_total,
    (
      ((a.amount - 
      ((a.amount * c.discount) / 100)) * 7) / 100
    ) vat_total,
    (
      (a.amount - ((a.amount * c.discount) / 100)) + 
      (((a.amount - ((a.amount * c.discount) / 100)) * 7) / 100)
    ) sale_total,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.sale a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.promotion c
    ON a.promotion = c.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function sale_view($data)
  {
    $sql = "SELECT a.uuid,a.text,CONCAT('SA',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    a.user_id,CONCAT(b.firstname,' ',b.lastname) fullname,
    a.promotion,c.name promotion_name,c.type promotion_type,c.discount,a.vat,a.amount,
    ((a.amount * c.discount) / 100) discount_amount,
    (a.amount - ((a.amount * c.discount) / 100)) discount_total,
    (
      ((a.amount - 
      ((a.amount * c.discount) / 100)) * 7) / 100
    ) vat_total,
    (
      (a.amount - ((a.amount * c.discount) / 100)) + 
      (((a.amount - ((a.amount * c.discount) / 100)) * 7) / 100)
    ) sale_total,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.sale a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.promotion c
    ON a.promotion = c.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function sale_update($data)
  {
    $sql = "UPDATE inventory.sale SET
    name = ?,
    text = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.sale WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,a.name,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'ใช้งาน'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    DATE_FORMAT(a.updated, '%d/%m/%Y, %H:%i น.') updated
    FROM inventory.sale a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function sale_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.sale";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.name", "a.text", "a.updated"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,a.text,a.user_id,CONCAT(b.firstname,' ',b.lastname) username,
    a.promotion,c.name promotion_name,c.discount,a.vat,a.amount,
    ((a.amount * c.discount) / 100) discount_amount,
    (a.amount - ((a.amount * c.discount) / 100)) discount_total,
    (
      ((a.amount - 
      ((a.amount * c.discount) / 100)) * 7) / 100
    ) vat_total,
    ROUND((
      (a.amount - ((a.amount * c.discount) / 100)) + 
      (((a.amount - ((a.amount * c.discount) / 100)) * 7) / 100)
    ),2) sale_total,
    (
      CASE
        WHEN a.status = 1 THEN 'ทำรายการเรียบร้อยแล้ว'
        WHEN a.status = 2 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'success'
        WHEN a.status = 2 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.sale a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.promotion c
    ON a.promotion = c.id ";

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
      $status = "<a href='/sale/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['username'],
        str_replace("\n", "<br>", $row['text']),
        $row['promotion_name'],
        "{$row['vat']} %",
        $row['sale_total'],
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

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }

  public function promotion_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.promotion a
    WHERE DATE(NOW()) < a.end ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.end ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function promotion_detail($data)
  {
    $sql = "SELECT a.type,IF(a.type = 1,a.discount,(a.discount / 100)) discount
    FROM inventory.promotion a
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function product_select($keyword)
  {
    $sql = "SELECT p.id,CONCAT('[',p.code,'] ',p.name) `text`
    FROM inventory.product p
    LEFT JOIN inventory.issue_item a
    ON p.id = a.product_id
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    WHERE p.status = 1
    AND p.location = 2
    AND b.status = 2 ";
    if (!empty($keyword)) {
      $sql .= " AND (p.code LIKE '%{$keyword}%' OR p.name LIKE '%{$keyword}%') ";
    }
    $sql .= " GROUP BY p.id ORDER BY p.code ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
