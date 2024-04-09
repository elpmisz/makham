<?php

namespace App\Classes;

use PDO;

class Bom
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "BOM CLASS";
  }

  public function bom_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.bom WHERE name = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function bom_insert($data)
  {
    $sql = "INSERT INTO inventory.bom(uuid,name,text) VALUES(uuid(),?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.bom_item(bom_id,product_id,quantity) VALUES(?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function bom_view($data)
  {
    $sql = "SELECT id,uuid,name,text,status 
    FROM inventory.bom 
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function item_view($data)
  {
    $sql = "SELECT i.id,p.name product_name,u.name unit_name,CAST(i.quantity AS DECIMAL(20,2)) quantity
    FROM inventory.bom_item i
    LEFT JOIN inventory.bom b
    ON i.bom_id = b.id
    LEFT JOIN inventory.product p
    ON i.product_id = p.id
    LEFT JOIN inventory.unit u
    ON p.unit = u.id
    WHERE b.uuid = ?
    AND i.status = 1
    ORDER BY i.id ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function bom_update($data)
  {
    $sql = "UPDATE inventory.bom SET
    name = ?,
    text = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_update($data)
  {
    $sql = "UPDATE inventory.bom_item SET
    quantity = ?,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_delete($data)
  {
    $sql = "UPDATE inventory.bom_item SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.bom WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.bom_item WHERE bom_id = ? AND product_id = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function bom_id($data)
  {
    $sql = "SELECT id
    FROM inventory.bom a
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function item_id($data)
  {
    $sql = "SELECT id
    FROM inventory.bom_item a
    WHERE bom_id = ?
    AND product_id = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function product_id($data)
  {
    $sql = "SELECT id
    FROM inventory.product a
    WHERE a.name LIKE CONCAT('%',?,'%')";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function download()
  {
    $sql = "SELECT b.uuid,b.name bom_name,b.text,c.name product_name,a.quantity,d.name unit_name,
    (
    CASE
      WHEN a.status = 1 THEN 'ใช้งาน'
      WHEN a.status = 2 THEN 'ระงับการใช้งาน'
      ELSE NULL
    END
    ) status_name,
    DATE_FORMAT(b.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.bom_item a
    LEFT JOIN inventory.bom b
    ON a.bom_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    LEFT JOIN inventory.unit d
    ON c.unit = d.id
    AND a.status = 1
    ORDER BY a.id ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function item_select($keyword)
  {
    $sql = "SELECT id,CONCAT('[',p.code,'] ',p.name) `text`
    FROM inventory.product p
    WHERE p.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (p.code LIKE '%{$keyword}%' OR p.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY p.code ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function item_unit($data)
  {
    $sql = "SELECT u.name
    FROM inventory.product p
    LEFT JOIN inventory.unit u
    ON p.unit = u.id
    WHERE p.id = ? ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['name']) ? $row['name'] : "");
  }

  public function bom_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.bom";
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

    $sql = "SELECT a.uuid,a.name,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'ใช้งาน'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
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
    FROM inventory.bom a ";

    if (!empty($keyword)) {
      $sql .= " WHERE a.name LIKE '%{$keyword}%' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.name ASC ";
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
      $status = "<a href='/bom/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['name'],
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

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }
}
