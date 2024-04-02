<?php

namespace App\Classes;

use PDO;

class Store
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "STORE CLASS";
  }

  public function store_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.store WHERE room = ? AND zone = ? AND floor = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function store_insert($data)
  {
    $sql = "INSERT INTO inventory.store(uuid,room,zone,floor,text) VALUES(uuid(),?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function store_view($data)
  {
    $sql = "SELECT uuid,room,zone,floor,text,status 
    FROM inventory.store 
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function store_update($data)
  {
    $sql = "UPDATE inventory.store SET
    room = ?,
    zone = ?,
    floor = ?,
    text = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.store WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,a.room,a.floor,a.zone,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'ใช้งาน'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    DATE_FORMAT(a.updated, '%d/%m/%Y, %H:%i น.') updated
    FROM inventory.store a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function store_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.store";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "CONCAT(a.room,a.floor,a.zone)", "a.text", "a.updated"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT(a.room,a.floor,a.zone) name,a.room,a.zone,a.floor,a.text,
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
    DATE_FORMAT(a.updated, '%d/%m/%Y, %H:%i น.') updated
    FROM inventory.store a ";

    if (!empty($keyword)) {
      $sql .= " WHERE a.room LIKE '%{$keyword}%' OR a.zone LIKE '%{$keyword}%' OR a.floor LIKE '%{$keyword}%' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, CONCAT(a.room,a.floor,a.zone) ASC ";
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
      $status = "<a href='/store/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['name'],
        str_replace("\n", "<br>", $row['text']),
        $row['updated'],
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
