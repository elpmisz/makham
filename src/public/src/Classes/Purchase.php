<?php

namespace App\Classes;

use PDO;

class Purchase
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "PURCHASE CLASS";
  }

  public function purchase_last()
  {
    $sql = "SELECT IFNULL(MAX(a.last) + 1,1) last
    FROM inventory.purchase a
    WHERE YEAR(a.created) = YEAR(NOW())";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return (isset($row['last']) ? $row['last'] : "");
  }

  public function purchase_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase 
    WHERE bom = ? AND machine = ? AND amount = ? AND date = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function approver_count()
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase WHERE status IN (1,4)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function productor_count()
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase WHERE status = 2";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function purchase_insert($data)
  {
    $sql = "INSERT INTO inventory.purchase(uuid,last,user_id,bom,machine,amount,date,text) VALUES(uuid(),?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_item(purchase_id,product_id,quantity,confirm) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function text_insert($data)
  {
    $sql = "INSERT INTO inventory.purchase_text(purchase_id,user_id,text,status) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function auth_approve($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase_auth WHERE user_id = ? AND type = 2 AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_product($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase_auth WHERE user_id = ? AND type = 3 AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase_auth WHERE user_id = ? AND type = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_insert($data)
  {
    $sql = "INSERT INTO inventory.purchase_auth(user_id,type) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function auth_delete($data)
  {
    $sql = "UPDATE inventory.purchase_auth SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }


  public function purchase_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.user_id requester,e.id product_id,
    CONCAT('PR',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    a.bom,c.name bom_name,
    a.machine,d.name machine_name,
    a.amount,a.confirm,a.date,a.text,
    DATE_FORMAT(a.date, '%d/%m/%Y') date,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.bom c
    ON a.bom = c.id
    LEFT JOIN inventory.machine d
    ON a.machine = d.id
    LEFT JOIN inventory.product e
    ON a.bom = e.bom_id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function text_view($data)
  {
    $sql = "SELECT 
    (
      CASE
        WHEN a.status = 2 THEN 'ผ่านการอนุมัติ'
        WHEN a.status = 3 THEN 'ผ่านการจ่ายวัตถุดิบ'
        WHEN a.status = 4 THEN 'ผลิตเสร็จเรียบร้อย'
        WHEN a.status = 5 THEN 'ผ่านการตรวจสอบ'
        WHEN a.status = 6 THEN 'ไม่ผ่านการอนุมัติ'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'info'
        WHEN a.status = 4 THEN 'success'
        WHEN a.status = 5 THEN 'primary'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    CONCAT(c.firstname,' ',c.lastname) username,
    a.text,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase_text a
    LEFT JOIN inventory.purchase b
    ON a.purchase_id = b.id
    LEFT JOIN inventory.user c
    ON a.user_id = c.id
    WHERE b.uuid = ?
    ORDER BY a.created DESC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function purchase_update($data)
  {
    $sql = "UPDATE inventory.purchase SET
    machine = ?,
    date = ?,
    text = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_approve($data)
  {
    $sql = "UPDATE inventory.purchase SET
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_process($data)
  {
    $sql = "UPDATE inventory.purchase SET
    confirm = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,CONCAT(f.firstname,' ',f.lastname) username,e.name bom_name,d.name machine_name,a.amount,a.confirm,
    DATE_FORMAT(a.date,'%d/%m/%Y') plan_date,
    c.name product_name,b.quantity,a.text,
    (
    CASE
      WHEN a.status = 1 THEN 'รอการอนุมัติ'
      WHEN a.status = 2 THEN 'รอเบิกวัตถุดิบ'
      WHEN a.status = 3 THEN 'กำลังผลิต'
      WHEN a.status = 4 THEN 'รอตรวจสอบ'
      WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
      WHEN a.status = 6 THEN 'รายการถูกยกเลิก'
      ELSE NULL
    END
    ) status_name,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase a
    LEFT JOIN inventory.purchase_item b
    ON a.id = b.purchase_id
    LEFT JOIN inventory.product c
    ON b.product_id = c.id
    LEFT JOIN inventory.machine d
    ON a.machine = d.id
    LEFT JOIN inventory.bom e
    ON a.bom = e.id
    LEFT JOIN inventory.user f
    ON a.user_id = f.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function bom_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.bom a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function bom_item($data)
  {
    $sql = "SELECT a.product_id,CONCAT('[',b.code,'] ',b.name) product_name,a.quantity product_used,c.name unit_name,
    SUM(IF(e.type = 1 AND e.status = 2,d.confirm,0)) issue_in,
    SUM(IF(e.type = 2 AND e.status = 2,d.confirm,0)) issue_out,
    SUM(IF(d.purchase_id IS NOT NULL AND f.status IN (3,4,5),d.confirm,0)) purchase_out,
    (
      SUM(IF(e.type = 1 AND e.status = 2,d.confirm,0)) -
      (
        SUM(IF(e.type = 2 AND e.status = 2,d.confirm,0)) +
        SUM(IF(d.purchase_id IS NOT NULL AND f.status IN (3,4,5),d.confirm,0))
      )
    ) issue_remain
    FROM inventory.bom_item a
    LEFT JOIN inventory.product b
    ON a.product_id = b.id
    LEFT JOIN inventory.unit c
    ON b.unit = c.id
    LEFT JOIN inventory.issue_item d
    ON a.product_id = d.product_id
    LEFT JOIN inventory.issue e
    ON d.issue_id = e.id
    LEFT JOIN inventory.purchase f
    ON d.purchase_id = f.id
    WHERE a.bom_id = ?
    AND a.status = 1
    GROUP BY a.product_id
    ORDER BY b.code ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function machine_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.machine a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function purchase_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "b.firstname", "c.name", "d.name", "a.amount", "a.date", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    a.bom,c.name bom_name,
    a.machine,d.name machine_name,
    a.amount,a.confirm,a.date,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'edit'
        WHEN a.status = 3 THEN 'process'
        ELSE 'complete'
      END
    ) page,
    (
      CASE
        WHEN a.status = 1 THEN 'รอการอนุมัติ'
        WHEN a.status = 2 THEN 'รอเบิกวัตถุดิบ'
        WHEN a.status = 3 THEN 'กำลังผลิต'
        WHEN a.status = 4 THEN 'รอตรวจสอบ'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'primary'
        WHEN a.status = 2 THEN 'info'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.date, '%d/%m/%Y') date,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.bom c
    ON a.bom = c.id
    LEFT JOIN inventory.machine d
    ON a.machine = d.id ";

    if (!empty($keyword)) {
      $sql .= " WHERE a.name LIKE '%{$keyword}%' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.machine ASC, a.date ASC ";
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
      $status = "<a href='/purchase/{$row['page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['fullname'],
        $row['bom_name'],
        $row['machine_name'],
        $row['amount'],
        $row['confirm'],
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
    $sql = "SELECT COUNT(*) FROM inventory.purchase";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "b.firstname", "c.name", "d.name", "a.amount", "a.date", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    a.bom,c.name bom_name,
    a.machine,d.name machine_name,
    a.amount,a.confirm,a.date,a.text,
    IF(a.status = 1,'approve','check') page,
    (
      CASE
        WHEN a.status = 1 THEN 'รอการอนุมัติ'
        WHEN a.status = 2 THEN 'รอเบิกวัตถุดิบ'
        WHEN a.status = 3 THEN 'กำลังผลิต'
        WHEN a.status = 4 THEN 'รอตรวจสอบ'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'primary'
        WHEN a.status = 2 THEN 'info'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.date, '%d/%m/%Y') date,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.bom c
    ON a.bom = c.id
    LEFT JOIN inventory.machine d
    ON a.machine = d.id
    WHERE a.status IN (1,4) ";

    if (!empty($keyword)) {
      $sql .= " AND a.name LIKE '%{$keyword}%' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.machine ASC, a.date ASC ";
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
      $status = "<a href='/purchase/{$row['page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['fullname'],
        $row['bom_name'],
        $row['machine_name'],
        $row['amount'],
        $row['confirm'],
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

  public function product_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "b.firstname", "c.name", "d.name", "a.amount", "a.date", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    a.bom,c.name bom_name,
    a.machine,d.name machine_name,
    a.amount,a.confirm,a.date,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'รอการอนุมัติ'
        WHEN a.status = 2 THEN 'รอเบิกวัตถุดิบ'
        WHEN a.status = 3 THEN 'กำลังผลิต'
        WHEN a.status = 4 THEN 'รอตรวจสอบ'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'primary'
        WHEN a.status = 2 THEN 'info'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.date, '%d/%m/%Y') date,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.bom c
    ON a.bom = c.id
    LEFT JOIN inventory.machine d
    ON a.machine = d.id
    WHERE a.status = 2 ";

    if (!empty($keyword)) {
      $sql .= " AND a.name LIKE '%{$keyword}%' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.machine ASC, a.date ASC ";
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
      $status = "<a href='/purchase/product/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['fullname'],
        $row['bom_name'],
        $row['machine_name'],
        $row['amount'],
        $row['confirm'],
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
    $sql = "SELECT COUNT(*) FROM inventory.purchase_auth";
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
        WHEN a.type = 3 THEN 'ผู้จัดการวัตถุดิบ'
        ELSE NULL
      END
    ) type_name,
    (
      CASE
        WHEN a.type = 1 THEN 'primary'
        WHEN a.type = 2 THEN 'warning'
        WHEN a.type = 3 THEN 'info'
        ELSE NULL
      END
    ) type_color
    FROM inventory.purchase_auth a 
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
