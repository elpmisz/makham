<?php

namespace App\Classes;

use PDO;

class Supplier
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "SUPPLIER CLASS";
  }

  public function supplier_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.customer WHERE name = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function supplier_insert($data)
  {
    $sql = "INSERT INTO inventory.customer(uuid,name,type,vat,email,contact,address,subcode,text) VALUES(uuid(),?,1,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function supplier_view($data)
  {
    $sql = "SELECT a.uuid,a.name customer_name,a.vat,a.email,a.contact,a.address,b.name_th sub_name,
    c.name_th district_name,d.name_th province_name,a.latitude,a.longitude,
    b.postal,a.text,a.subcode,CONCAT(IF(d.code = 10,'แขวง','ตำบล'),b.name_th,IF(d.code = 10,' ',' อำเภอ'),c.name_th,' จังหวัด',d.name_th,' ',b.postal) subname,a.status
    FROM inventory.customer a
    LEFT JOIN inventory.subdistrict b
    ON a.subcode = b.code
    LEFT JOIN inventory.district c
    ON b.district = c.code
    LEFT JOIN inventory.province d
    ON c.province = d.code
    WHERE a.uuid =  ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function supplier_update($data)
  {
    $sql = "UPDATE inventory.customer SET
    name = ?,
    vat = ?,
    email = ?,
    contact = ?,
    address = ?,
    subcode = ?,
    text = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.customer WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function subcode($data)
  {
    $sql = "SELECT a.code subcode,a.name_th sub_name,b.name_th district_name,c.name_th province_name,a.postal
    FROM inventory.subdistrict a
    LEFT JOIN inventory.district b
    ON a.district = b.code
    LEFT JOIN inventory.province c
    ON b.province = c.code
    WHERE a.name_th LIKE CONCAT('%',?,'%')";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,a.name customer_name,a.vat,a.email,a.contact,a.address,b.name_th sub_name,c.name_th district_name,d.name_th province_name,
    b.postal,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'ใช้งาน'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    DATE_FORMAT(a.updated, '%d/%m/%Y, %H:%i น.') updated
    FROM inventory.customer a
    LEFT JOIN inventory.subdistrict b
    ON a.subcode = b.code
    LEFT JOIN inventory.district c
    ON b.district = c.code
    LEFT JOIN inventory.province d
    ON c.province = d.code
    WHERE a.type = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function address_select($keyword)
  {
    $sql = "SELECT a.code id,CONCAT(IF(c.code = 10,'แขวง','ตำบล'),a.name_th,IF(c.code = 10,' ',' อำเภอ'),b.name_th,' จังหวัด',c.name_th,' ',a.postal) text
    FROM inventory.subdistrict a
    LEFT JOIN inventory.district b
    ON a.district = b.code
    LEFT JOIN inventory.province c
    ON b.province = c.code ";
    if (!empty($keyword)) {
      $sql .= " WHERE (a.name_th LIKE '%{$keyword}%' OR b.name_th LIKE '%{$keyword}%' OR c.name_th LIKE '%{$keyword}%' OR a.postal LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY c.code ASC, b.code ASC, a.code ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function address_view($data)
  {
    $sql = "SELECT a.code,a.name_th sub_name,b.name_th district_name,c.name_th province_name,a.postal
    FROM inventory.subdistrict a
    LEFT JOIN inventory.district b
    ON a.district = b.code
    LEFT JOIN inventory.province c
    ON b.province = c.code
    WHERE a.code = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function supplier_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.customer WHERE type = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.name", "a.contact", "a.address", "a.updated"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,a.name customer_name,a.vat,CONCAT(a.contact,'\n',a.email) contact,CONCAT(a.address,IF(d.code = 10,'\nแขวง','\nตำบล'),b.name_th,IF(d.code = 10,' ',' อำเภอ'),c.name_th,' จังหวัด',d.name_th,' ',b.postal) address,a.text,
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
    FROM inventory.customer a
    LEFT JOIN inventory.subdistrict b
    ON a.subcode = b.code
    LEFT JOIN inventory.district c
    ON b.district = c.code
    LEFT JOIN inventory.province d
    ON c.province = d.code
    WHERE a.type = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.email LIKE '%{$keyword}%' OR a.contact LIKE '%{$keyword}%' OR a.address LIKE '%{$keyword}%' OR b.name_th LIKE '%{$keyword}%' OR c.name_th LIKE '%{$keyword}%' OR d.name_th LIKE '%{$keyword}%' OR b.postal LIKE '%{$keyword}%') ";
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
      $status = "<a href='/supplier/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['customer_name'],
        str_replace("\n", "<br>", $row['contact']),
        str_replace("\n", "<br>", $row['address']),
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
