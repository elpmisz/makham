<?php

namespace App\Classes;

use PDO;

class Quality
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "QUALITY CLASS";
  }

  public function quality_last()
  {
    $sql = "SELECT IFNULL(MAX(a.last) + 1,1) last
    FROM inventory.quality a
    WHERE YEAR(a.created) = YEAR(NOW())";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return (isset($row['last']) ? $row['last'] : "");
  }

  public function quality_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality 
    WHERE user_id = ? AND `date` = ? AND product_id = ? AND `text` = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function quality_insert($data)
  {
    $sql = "INSERT INTO inventory.quality(`uuid`, `last`, `user_id`, `date`, `receive`, `product_id`, `text`) VALUES(uuid(),?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function quality_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.user_id,CONCAT('QC',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    DATE_FORMAT(a.date,'%d/%m/%Y') `date`,
    DATE_FORMAT(a.receive,'%d/%m/%Y') receive,
    b.firstname,b.lastname,
    CONCAT('คุณ',b.firstname,' ',b.lastname) fullname,
    a.product_id,c.`name` product_name,a.text,a.status,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'ระงับการใช้งาน'
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
    f.firstname approver_firstname,
    DATE_FORMAT(d.created, '%d/%m/%Y, %H:%i น.') approved
    FROM inventory.quality a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    LEFT JOIN inventory.quality_text d
    ON a.id = d.quality_id
    LEFT JOIN inventory.`user` f
    ON d.user_id = f.login
    WHERE a.`uuid` = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function quality_update($data)
  {
    $sql = "UPDATE inventory.quality SET
    `date` = ?,
    `receive` = ?,
    product_id = ?,
    `text` = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function quality_approve($data)
  {
    $sql = "UPDATE inventory.quality SET
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function quality_delete($data)
  {
    $sql = "UPDATE inventory.quality SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality_item 
    WHERE quality_id = ? AND `start` = ? AND user = ? AND supplier_id = ? AND quantity = ? AND `end` = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.quality_item( `quality_id`, `start`, `user`, `supplier_id`, `quantity`, `end`) VALUES(?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_view($data)
  {
    $sql = "SELECT b.id,b.`start`,b.`user`,b.supplier_id,c.`name` supplier_name,b.quantity,b.`end`
    FROM inventory.quality a
    LEFT JOIN inventory.quality_item b
    ON a.id = b.quality_id
    LEFT JOIN inventory.customer c
    ON b.supplier_id = c.id
    WHERE a.`uuid` = ?
    AND b.`status` = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function item_delete($data)
  {
    $sql = "UPDATE inventory.quality_item SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function approver_count()
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality WHERE status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function auth_approve($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality_auth WHERE user_id = ? AND type = 2 AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality_auth WHERE user_id = ? AND type = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_insert($data)
  {
    $sql = "INSERT INTO inventory.quality_auth(user_id,type) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function auth_delete($data)
  {
    $sql = "UPDATE inventory.quality_auth SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function subject_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality_subject WHERE name = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function subject_insert($data)
  {
    $sql = "INSERT INTO inventory.quality_subject(`name`) VALUES(?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function subject_view()
  {
    $sql = "SELECT *
    FROM inventory.quality_subject a
    WHERE a.`status` = 1
    ORDER BY a.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function subject_delete($data)
  {
    $sql = "UPDATE inventory.quality_subject SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function text_insert($data)
  {
    $sql = "INSERT INTO inventory.quality_text(quality_id,user_id,text,status) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function download()
  {
    $sql = "SELECT CONCAT('QC',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(c.firstname,' ',c.lastname) fullname,
    DATE_FORMAT(a.date,'%d/%m/%Y') `date`,DATE_FORMAT(a.receive,'%d/%m/%Y') receive,
    b.name product_name,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.quality a
    LEFT JOIN inventory.product b
    ON a.product_id = b.id
    LEFT JOIN inventory.`user` c
    ON a.user_id = c.login";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function auth_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality_auth";
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
    FROM inventory.quality_auth a 
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

  public function subject_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality_subject";
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
    FROM inventory.quality_subject a 
    WHERE a.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.id ASC ";
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
      $status = "<a href='javascript:void(0)' class='badge badge-danger font-weight-light subject-delete' id='{$row['id']}'>ลบ</a>";
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

  public function quality_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.quality";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "b.firstname", "c.name", "a.date", "a.receive", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT('QC',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    DATE_FORMAT(a.date,'%d/%m/%Y') `date`,
    DATE_FORMAT(a.receive,'%d/%m/%Y') receive,
    b.firstname,b.lastname,
    CONCAT('คุณ',b.firstname,' ',b.lastname) fullname,
    CONCAT('[',c.code,'] ',c.`name`) product_name,a.text,
    IF(a.status = 1,'edit','complete') page,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'ระงับการใช้งาน'
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
    FROM inventory.quality a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id ";

    if (!empty($keyword)) {
      $sql .= " WHERE (a.text LIKE '%{$keyword}%' OR DATE_FORMAT(a.date,'%d/%m/%Y') LIKE '%{$keyword}%' OR DATE_FORMAT(a.receive,'%d/%m/%Y') LIKE '%{$keyword}%' OR b.first_name LIKE '%{$keyword}%' OR c.code LIKE '%{$keyword}%' OR c.name LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.date ASC ";
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
      $status = "<a href='/quality/{$row['page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['fullname'],
        $row['product_name'],
        $row['date'],
        $row['receive'],
        str_replace("\n", "<br>", $row['text']),
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
    $sql = "SELECT COUNT(*) FROM inventory.quality WHERE status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "b.firstname", "c.name", "a.date", "a.receive", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT('QC',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    DATE_FORMAT(a.date,'%d/%m/%Y') `date`,
    DATE_FORMAT(a.receive,'%d/%m/%Y') receive,
    b.firstname,b.lastname,
    CONCAT('คุณ',b.firstname,' ',b.lastname) fullname,
    c.`name` product_name,a.text,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.quality a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    WHERE a.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND a.text LIKE '%{$keyword}%' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.date ASC ";
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
      $status = "<a href='/quality/approve/{$row['uuid']}' class='badge badge-primary font-weight-light'>รอตรวจสอบ</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['fullname'],
        $row['product_name'],
        $row['date'],
        $row['receive'],
        str_replace("\n", "<br>", $row['text']),
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
    $sql = "SELECT COUNT(*) FROM inventory.quality";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "b.firstname", "c.name", "a.date", "a.receive", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.uuid,CONCAT('QC',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    DATE_FORMAT(a.date,'%d/%m/%Y') `date`,
    DATE_FORMAT(a.receive,'%d/%m/%Y') receive,
    b.firstname,b.lastname,
    CONCAT('คุณ',b.firstname,' ',b.lastname) fullname,
    CONCAT('[',c.code,'] ',c.`name`) product_name,a.text,
    IF(a.status = 1,'edit','complete') page,
    (
      CASE
        WHEN a.status = 1 THEN 'รอตรวจสอบ'
        WHEN a.status = 2 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 3 THEN 'ระงับการใช้งาน'
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
    FROM inventory.quality a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    WHERE a.status != 0 ";

    if (!empty($keyword)) {
      $sql .= " AND (a.text LIKE '%{$keyword}%' OR DATE_FORMAT(a.date,'%d/%m/%Y') LIKE '%{$keyword}%' OR DATE_FORMAT(a.receive,'%d/%m/%Y') LIKE '%{$keyword}%' OR b.first_name LIKE '%{$keyword}%' OR c.code LIKE '%{$keyword}%' OR c.name LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.date ASC ";
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
      $status = "<a href='/quality/manage-edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['id']}'>ลบ</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['fullname'],
        $row['product_name'],
        $row['date'],
        $row['receive'],
        str_replace("\n", "<br>", $row['text']),
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

  public function sup_select($keyword)
  {
    $sql = "SELECT a.id,a.`name` `text`
    FROM inventory.customer a
    WHERE a.`type` = 1
    AND a.`status` = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE  '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.`name` ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function product_select($keyword)
  {
    $sql = "SELECT a.id,CONCAT('[',a.`code`,'] ',a.`name`) `text`
    FROM inventory.product a
    WHERE a.`status` = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.code LIKE '%{$keyword}%' OR a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.`name` ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }
}
