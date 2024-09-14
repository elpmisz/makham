<?php

namespace App\Classes;

use PDO;

class Waste
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "WASTE CLASS";
  }

  public function waste_last()
  {
    $sql = "SELECT IFNULL(MAX(a.last) + 1,1) last
    FROM inventory.waste a
    WHERE YEAR(a.created) = YEAR(NOW())";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return (isset($row['last']) ? $row['last'] : "");
  }

  public function waste_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste WHERE name = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function waste_insert($data)
  {
    $sql = "INSERT INTO inventory.waste(uuid,last,purchase_id,text,user_id) VALUES(uuid(),?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function waste_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.text,a.user_id,a.status,
    b.firstname,b.lastname,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    e.uuid purchase_uuid,CONCAT('PO',YEAR(e.created),LPAD(e.last,5,'0')) purchase_ticket,
    CONCAT('WA',YEAR(a.created),LPAD(a.last,5,'0')) ticket,a.status,
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
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created,
    d.firstname approver_firstname,' ',d.lastname approver_lastname,
    CONCAT(d.firstname,' ',d.lastname) approver,c.text approve_text,
    DATE_FORMAT(c.created, '%d/%m/%Y, %H:%i น.') approved
    FROM inventory.waste a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.waste_text c
    ON a.id = c.waste_id
    LEFT JOIN inventory.user d
    ON c.user_id = d.id
    LEFT JOIN inventory.purchase e
    ON a.purchase_id = e.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste_item
    WHERE waste_id = ?
    AND type = ?
    AND item = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.waste_item(waste_id,type,item,quantity,remark) VALUES(?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_view($data)
  {
    $sql = "SELECT b.id,IF(b.type = 1,c.name,d.name) item,b.quantity,b.remark
    FROM inventory.waste a
    LEFT JOIN inventory.waste_item b
    ON a.id = b.waste_id
    LEFT JOIN inventory.product c
    ON b.item = c.id
    LEFT JOIN inventory.waste_other d
    ON b.item = d.id
    WHERE a.uuid = ?
    AND b.type = ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function item_delete($data)
  {
    $sql = "UPDATE inventory.waste_item SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function waste_update($data)
  {
    $sql = "UPDATE inventory.waste SET
    text = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function waste_approve($data)
  {
    $sql = "UPDATE inventory.waste SET
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function text_insert($data)
  {
    $sql = "INSERT INTO inventory.waste_text(waste_id,user_id,text,status) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function text_view($data)
  {
    $sql = "SELECT CONCAT('คุณ',c.firstname,' ',c.lastname) username,b.text,
    (
    CASE 
      WHEN b.status = 1 THEN 'รอเบิกวัถุดิบ'
      WHEN b.status = 2 THEN 'ผ่านการตรวจสอบ'
      WHEN b.status = 3 THEN 'ไม่ผ่านการตรวจสอบ'
      ELSE NULL
    END
    ) status_name,
    (
    CASE 
      WHEN b.status = 1 THEN 'info'
      WHEN b.status = 2 THEN 'success'
      WHEN b.status = 3 THEN 'danger'
      ELSE NULL
    END
    ) status_color,
    DATE_FORMAT(b.created,'%d/%m/%Y, %H:%i น.') created
    FROM inventory.waste a
    LEFT JOIN inventory.waste_text b
    ON a.id = b.waste_id
    LEFT JOIN inventory.`user` c
    ON b.user_id = c.login
    WHERE a.`uuid` = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,CONCAT('WA',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,a.text,
    IF(e.type = 1,f.name,e.item) item,e.quantity,e.remark,
    (
    CASE
      WHEN a.status = 1 THEN 'รอตรวจสอบ'
      WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
      WHEN a.status = 3 THEN 'รายการถูกยกเลิก'
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
    WHERE e.status = 1
    ORDER BY a.created DESC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function approver_count()
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste WHERE status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function auth_approve($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste_auth WHERE user_id = ? AND type = 2 AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste_auth WHERE user_id = ? AND type = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_insert($data)
  {
    $sql = "INSERT INTO inventory.waste_auth(user_id,type) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function auth_delete($data)
  {
    $sql = "UPDATE inventory.waste_auth SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function waste_delete($data)
  {
    $sql = "UPDATE inventory.waste SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function other_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste_other WHERE name = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function other_insert($data)
  {
    $sql = "INSERT INTO inventory.waste_other(name) VALUES(?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function other_delete($data)
  {
    $sql = "UPDATE inventory.waste_other SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function waste_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "b.firstname", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,a.text,CONCAT('WA',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    b.firstname,b.lastname,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    IF(a.status = 1,'edit','complete') page,
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
    FROM inventory.waste a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id ";

    if (!empty($keyword)) {
      $sql .= " WHERE a.text LIKE '%{$keyword}%' ";
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
        $row['firstname'],
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
    $sql = "SELECT COUNT(*) FROM inventory.waste";
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

    $sql = "SELECT a.uuid,a.text,CONCAT('WA',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    b.firstname,b.lastname,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'ไม่ผ่านการตรวจสอบ'
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
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    WHERE a.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND a.text LIKE '%{$keyword}%' ";
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
      $status = "<a href='/waste/approve/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['firstname'],
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

  public function manage_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "b.firstname", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.uuid,a.text,CONCAT('WA',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    b.firstname,b.lastname,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    IF(a.status = 1,'edit','complete') page,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'ไม่ผ่านการตรวจสอบ'
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
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    WHERE a.status != 0 ";

    if (!empty($keyword)) {
      $sql .= " AND a.text LIKE '%{$keyword}%' ";
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
      $status = "<a href='/waste/manage-edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['id']}'>ลบ</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['firstname'],
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
    $sql = "SELECT COUNT(*) FROM inventory.waste_auth";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "", "a.name", "a.text", "a.updated"];

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
    FROM inventory.waste_auth a 
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

  public function other_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.waste_other";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "", "a.name", "a.text", "a.updated"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.name
    FROM inventory.waste_other a 
    WHERE a.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.name ASC ";
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
      $status = "<a href='javascript:void(0)' class='badge badge-danger font-weight-light other-delete' id='{$row['id']}'>ลบ</a>";
      $data[] = [
        $status,
        $row['name'],
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

  public function purchase_select($keyword)
  {
    $sql = "SELECT a.id,CONCAT('PO',YEAR(a.created),LPAD(a.last,5,'0')) `text`
    FROM inventory.purchase a
    WHERE a.status NOT IN (5)  ";
    if (!empty($keyword)) {
      $sql .= " AND (CONCAT('PO',YEAR(a.created),LPAD(a.last,5,'0')) LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.created ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function other_select($keyword)
  {
    $sql = "SELECT a.id,a.name `text`
    FROM inventory.waste_other a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }
}
