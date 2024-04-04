<?php

namespace App\Classes;

use PDO;

class Issue
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "ISSUE CLASS";
  }

  public function issue_last()
  {
    $sql = "SELECT IFNULL(MAX(a.last) + 1,1) last
    FROM inventory.issue a
    WHERE YEAR(a.created) = YEAR(NOW())";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return (isset($row['last']) ? $row['last'] : "");
  }

  public function issue_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue WHERE type = ? AND text = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function approver_count()
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue WHERE status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function issue_insert($data)
  {
    $sql = "INSERT INTO inventory.issue(uuid,last,type,text,user_id) VALUES(uuid(),?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function issue_purchase($data)
  {
    $sql = "INSERT INTO inventory.issue(uuid,last,type,text,user_id,status) VALUES(uuid(),?,1,?,?,2)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue_item WHERE issue_id = ? AND product_id = ? AND location_id = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_item(issue_id,product_id,type,location_id,quantity) VALUES(?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_purchase($data)
  {
    $sql = "INSERT INTO inventory.issue_item(issue_id,product_id,quantity,confirm) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function auth_approve($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue_auth WHERE user_id = ? AND type = 2 AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue_auth WHERE user_id = ? AND type = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_auth(user_id,type) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function text_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_text(issue_id,user_id,text,status) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function issue_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.text,a.type,
    CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,
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
    CONCAT(d.firstname,' ',d.lastname) approver,
    DATE_FORMAT(c.created, '%d/%m/%Y, %H:%i น.') approved,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.issue a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.issue_text c
    ON a.id = c.issue_id
    LEFT JOIN inventory.user d
    ON c.user_id = d.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function item_view($data)
  {
    $sql = "SELECT b.id item_id,a.id product_id,a.uuid product_uuid,
    a.code product_code,a.name product_name,CAST(b.quantity AS DECIMAL(20,2)) quantity,b.confirm,
    a.cost product_cost,a.price product_price,a.min product_min,a.max product_max,
    a.supplier,d.name supplier_name,
    a.unit,e.name unit_name,
    a.brand,f.name brand_name,
    a.category,g.name category_name,
    a.store,CONCAT(h.room,h.floor,h.zone) store_name,
    b.location_id,i.name location_name,
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
    LEFT JOIN inventory.location i
    ON b.location_id = i.id
    WHERE c.uuid = ?
    AND b.status = 1
    GROUP BY a.id,b.location_id
    ORDER BY b.id ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function exchange_view($data)
  {
    $sql = "SELECT IF(c.type = 3,CONCAT(b.issue_id,'-',b.product_id),b.id) item_id,
    a.id product_id,a.uuid product_uuid,a.code product_code,a.name product_name,
    CAST(b.quantity AS DECIMAL(20,2)) quantity,b.confirm,
    MIN(i.name) send,MAX(i.name) receive,
    a.cost product_cost,a.price product_price,a.min product_min,a.max product_max,
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
    LEFT JOIN inventory.location i
    ON b.location_id = i.id
    WHERE c.uuid = ?
    AND b.status = 1
    GROUP BY a.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function exchange_delete($data)
  {
    $sql = "UPDATE inventory.issue_item SET
    status = 2,
    updated = NOW()
    WHERE issue_id = ?
    AND product_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function exchange_confirm($data)
  {
    $sql = "UPDATE inventory.issue_item SET
    confirm = ?,
    updated = NOW()
    WHERE issue_id = ?
    AND product_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function text_view($data)
  {
    $sql = "SELECT b.id,b.product_id,CONCAT('[',c.code,'] ',c.name) product_name,
    CAST(b.quantity AS DECIMAL(20,2)) quantity,
    CAST(b.confirm AS DECIMAL(20,2)) confirm,
    c.unit unit_id,d.name unit_name
    FROM inventory.issue a
    LEFT JOIN inventory.issue_item b
    ON a.id = b.issue_id
    LEFT JOIN inventory.product c
    ON b.product_id = c.id
    LEFT JOIN inventory.unit d
    ON c.unit = d.id
    WHERE b.status = 1
    AND a.uuid = ?
    ORDER BY c.code ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function item_detail($data, $location)
  {
    $sql = "SELECT a.id product_id,a.uuid product_uuid,a.code product_code,a.name product_name,
    SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1 AND b.location_id = {$location},IF(c.status = 1,b.quantity,b.confirm),0)) income,
    SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1 AND b.location_id = {$location},IF(c.status = 1,b.quantity,b.confirm),0)) outcome,
    (
      SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1 AND b.location_id = {$location},IF(c.status = 1,b.quantity,b.confirm),0)) -
      SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1 AND b.location_id = {$location},IF(c.status = 1,b.quantity,b.confirm),0))
    ) remain,
    a.cost product_cost,a.price product_price,a.min product_min,a.max product_max,
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
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function issue_update($data)
  {
    $sql = "UPDATE inventory.issue SET
    text = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_update($data)
  {
    $sql = "UPDATE inventory.issue_item SET
    quantity = ?,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function issue_approve($data)
  {
    $sql = "UPDATE inventory.issue SET
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_confirm($data)
  {
    $sql = "UPDATE inventory.issue_item SET
    confirm = ?,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_delete($data)
  {
    $sql = "UPDATE inventory.issue_item SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function auth_delete($data)
  {
    $sql = "UPDATE inventory.issue_auth SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,CONCAT(d.firstname,' ',d.lastname) username,
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
      WHEN a.status = 3 THEN 'รายการถูกยกเลิก'
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
    ON a.user_id = d.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function request_data()
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
      $status = "<a href='/issue/{$row['page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
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

  public function approve_data()
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

    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,a.text,
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
    ON a.user_id = b.id
    WHERE a.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND a.text LIKE '%{$keyword}%' ";
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
      $status = "<a href='/issue/approve/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
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

  public function auth_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue_auth";
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

    $sql = "SELECT a.id,CONCAT(b.firstname,' ',b.lastname) fullname,
    (
      CASE
        WHEN a.type = 1 THEN 'จัดการระบบ'
        WHEN a.type = 2 THEN 'ผู้อนุมัติ'
        ELSE NULL
      END
    ) type_name,
    (
      CASE
        WHEN a.type = 1 THEN 'primary'
        WHEN a.type = 2 THEN 'warning'
        ELSE NULL
      END
    ) type_color
    FROM inventory.issue_auth a 
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    WHERE a.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND (b.firstname LIKE '%{$keyword}%' OR b.lastname LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY b.firstname ASC ";
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
      $status = "<a href='javascript:void(0)' class='badge badge-danger font-weight-light auth-delete' id='{$row['id']}'>ลบ</a>";
      $type = "<span class='badge badge-{$row['type_color']}'>{$row['type_name']}</span>";
      $data[] = [
        $status,
        $type,
        $row['fullname'],
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

  public function item_all_select($keyword)
  {
    $sql = "SELECT id,CONCAT('[',p.code,'] ',p.name) text
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

  public function item_remain_select($keyword)
  {
    $sql = "SELECT p.id,CONCAT('[',p.code,'] ',p.name) text
    FROM inventory.product p
    LEFT JOIN inventory.issue_item a
    ON p.id = a.product_id
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    WHERE p.status = 1
    AND b.status = 2 ";
    if (!empty($keyword)) {
      $sql .= " AND (p.code LIKE '%{$keyword}%' OR p.name LIKE '%{$keyword}%') ";
    }
    $sql .= " GROUP BY p.id ORDER BY p.code ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function user_select($keyword)
  {
    $sql = "SELECT a.id, CONCAT(a.firstname,' ',a.lastname) text
    FROM inventory.user a
    LEFT JOIN inventory.login b
    ON a.id = b.id
    WHERE b.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.firstname LIKE '%{$keyword}%' OR a.lastname LIKE '%{$keyword}%' OR a.email LIKE '%{$keyword}%' OR a.contact LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.firstname ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function location_select($keyword)
  {
    $sql = "SELECT a.id, a.name text
    FROM inventory.location a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
