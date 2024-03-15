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
    $sql = "SELECT COUNT(*) FROM inventory.issue_item WHERE issue_id = ? AND product_id = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_item(issue_id,product_id,quantity) VALUES(?,?,?)";
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
    $sql = "SELECT a.id,a.uuid,a.text,
    IF(a.type = 1,'นำเข้า','เบิกออก') type_name,
    IF(a.type = 1,'primary','danger') type_color,
    CONCAT('RE',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,
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

  public function item_detail($data)
  {
    $sql = "SELECT a.id,a.uuid,a.code,a.name product_name,a.cost,a.price,a.min,a.max,
    a.supplier,b.name supplier_name,
    a.unit,c.name unit_name,
    a.brand,d.name brand_name,
    a.category,e.name category_name,
    a.location,f.name location_name,
    SUM(IF(h.type = 1 AND h.status = 2,g.confirm,0)) issue_input,
    SUM(IF(h.type = 2 AND h.status = 2,g.confirm,0)) issue_output,
    SUM(IF(i.status IN (3,4,5) AND g.purchase_id IS NOT NULL,g.confirm,0)) purchase_output,
    (
      SUM(IF(h.type = 1 AND h.status = 2,g.confirm,0)) - 
      (
        SUM(IF(h.type = 2 AND h.status = 2,g.confirm,0)) +
        SUM(IF(i.status IN (3,4,5) AND g.purchase_id IS NOT NULL,g.confirm,0))
      )
    ) issue_remain,
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
    FROM inventory.product a
    LEFT JOIN inventory.customer b
    ON a.supplier = b.id
    LEFT JOIN inventory.unit c
    ON a.unit = c.id
    LEFT JOIN inventory.brand d
    ON a.brand = d.id
    LEFT JOIN inventory.category e
    ON a.category = e.id
    LEFT JOIN inventory.location f
    ON a.location = f.id
    LEFT JOIN inventory.issue_item g
    ON a.id = g.product_id
    LEFT JOIN inventory.issue h
    ON g.issue_id = h.id
    LEFT JOIN inventory.purchase i
    ON g.purchase_id = i.id
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

    $column = ["a.status", "a.name", "a.text", "a.updated"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,a.text,
    IF(a.status = 1,'edit','complete') page,
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
      $status = "<a href='/issue/{$row['page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
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

  public function approve_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue";
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

  public function item_remain_select($keyword)
  {
    $sql = "SELECT p.id,CONCAT('[',p.code,'] ',p.name) `text`
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
}
