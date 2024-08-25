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
    WHERE customer_id = ? AND amount = ? AND machine = ? AND per = ? AND date_produce = ? AND date_delivery = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function purchase_insert($data)
  {
    $sql = "INSERT INTO inventory.purchase(`uuid`, `last`, `user_id`, `customer_id`, `amount`, `machine`, `per`, `date_produce`, `date_delivery`, `text`) VALUES(uuid(),?,?,?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_update($data)
  {
    $sql = "UPDATE inventory.purchase SET
    customer_id = ?,
    amount = ?,
    machine = ?,
    per = ?,
    date_produce = ?,
    date_delivery = ?,
    `text` = ?,
    issue_id = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_process($data)
  {
    $sql = "UPDATE inventory.purchase SET
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_view($data)
  {
    $sql = "SELECT a.id,a.`uuid`,CONCAT('PR',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    a.user_id,CONCAT(c.firstname,' ',c.lastname) fullname,
    a.customer_id,CONCAT('คุณ',d.name) customer_name,
    a.amount,a.machine,a.per,
    DATE_FORMAT(a.date_produce,'%d/%m/%Y') produce,
    DATE_FORMAT(a.date_delivery,'%d/%m/%Y') delivery, 
    a.`text`,b.uuid issue_uuid,CONCAT('RE',YEAR(b.created),LPAD(b.last,5,'0')) issue_ticket,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created 
    FROM inventory.purchase a  
    LEFT JOIN inventory.issue b 
    ON a.issue_id = b.id 
    LEFT JOIN inventory.`user` c 
    ON a.user_id = c.login 
    LEFT JOIN inventory.customer d 
    ON a.customer_id = d.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function purchase_delete($data)
  {
    $sql = "UPDATE inventory.purchase SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_item_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase_item 
    WHERE purchase_id = ? AND product_id = ? AND location_id = ? AND quantity = ? AND unit_id = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function purchase_item_insert($data)
  {
    $sql = "INSERT INTO inventory.purchase_item(`purchase_id`, `product_id`, `location_id`, `quantity`, `unit_id`) VALUES(?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_item_update($data)
  {
    $sql = "UPDATE inventory.purchase_item SET
    confirm = ?,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function purchase_item_view($data)
  {
    $sql = "SELECT b.id,b.product_id,c.name product_name,
    b.location_id,d.name location_name,b.quantity,b.confirm,e.name unit_name
    FROM inventory.purchase a 
    LEFT JOIN inventory.purchase_item b 
    ON a.id  = b.purchase_id 
    LEFT JOIN inventory.product c 
    ON b.product_id = c.id 
    LEFT JOIN inventory.location d 
    ON b.location_id = d.id
    LEFT JOIN inventory.unit e 
    ON b.unit_id = e.id 
    WHERE a.`uuid` = ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function purchase_item_delete($data)
  {
    $sql = "UPDATE inventory.purchase_item SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_item(purchase_id,product_id,quantity,confirm) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function approver_count()
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase WHERE status IN (3)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  public function auth_approve($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase_auth WHERE user_id = ? AND type = 1 AND status = 1";
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

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.purchase WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,CONCAT('PR',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,c.name bom_name,DATE_FORMAT(a.date, '%d/%m/%Y') date,
    a.machine,a.amount,a.text,
    (
    CASE
      WHEN a.status = 1 THEN 'รอการอนุมัติ'
      WHEN a.status = 2 THEN 'รอเบิกวัตถุดิบ'
      WHEN a.status = 3 THEN 'กำลังผลิต'
      WHEN a.status = 4 THEN 'รอตรวจสอบ'
      WHEN a.status = 5 THEN 'ผ่านการตรวจสอบ'
      WHEN a.status = 6 THEN 'รายการถูกยกเลิก'
      ELSE NULL
    END
    ) status_name,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.purchase a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.bom c
    ON a.bom = c.id
    ORDER BY a.created DESC";
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
    $sql = "SELECT CONCAT('[',c.code,'] ',c.name) product_name,b.quantity,d.name unit_name
    FROM inventory.bom a
    LEFT JOIN inventory.bom_item b
    ON a.id = b.bom_id
    LEFT JOIN inventory.product c
    ON b.product_id = c.id
    LEFT JOIN inventory.unit d
    ON c.unit = d.id
    WHERE a.id = ?";
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

    $column = ["a.status", "a.last", "c.name", "a.machine", "a.amount", "a.confirm", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.`uuid`,CONCAT('PR',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(c.firstname,' ',c.lastname) fullname,
    a.customer_id,CONCAT('คุณ',d.name) customer_name,
    a.amount,a.machine,a.per,
    DATE_FORMAT(a.date_produce,'%d/%m/%Y') produce,
    DATE_FORMAT(a.date_delivery,'%d/%m/%Y') delivery,
    GROUP_CONCAT(e.name) products, 
    a.`text`,
    (
    CASE 
      WHEN a.status = 1 THEN 'view'
      WHEN a.status = 2 THEN 'process'
      WHEN a.status IN (3,4,5) THEN 'complete'
      ELSE NULL
    END
    ) page,
    (
    CASE 
      WHEN a.status = 1 THEN 'รอเบิกวัถุดิบ'
      WHEN a.status = 2 THEN 'กำลังผลิต'
      WHEN a.status = 3 THEN 'รอตรวจสอบ'
      WHEN a.status = 4 THEN 'ดำเนินการเรียบร้อย'
      WHEN a.status = 5 THEN 'รายการถูกยกเลิก'
      ELSE NULL
    END
    ) status_name,
    (
    CASE 
      WHEN a.status = 1 THEN 'info'
      WHEN a.status = 2 THEN 'primary'
      WHEN a.status = 3 THEN 'warning'
      WHEN a.status = 4 THEN 'success'
      WHEN a.status = 5 THEN 'danger'
      ELSE NULL
    END
    ) status_color,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created 
    FROM inventory.purchase a 
    LEFT JOIN inventory.purchase_item b 
    ON a.id  = b.purchase_id  
    LEFT JOIN inventory.`user` c 
    ON a.user_id = c.login 
    LEFT JOIN inventory.customer d 
    ON a.customer_id = d.id 
    LEFT JOIN inventory.product e 
    ON b.product_id = e.id
    WHERE b.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND a.name LIKE '%{$keyword}%' ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.date_produce DESC ";
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
        $row['ticket'],
        $row['customer_name'],
        $row['amount'],
        $row['produce'],
        $row['delivery'],
        str_replace(",", "<br>", $row['products']),
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

    $column = ["a.status", "a.last", "c.name", "a.machine", "a.amount", "a.confirm", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.`uuid`,CONCAT('PR',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(c.firstname,' ',c.lastname) fullname,
    a.customer_id,CONCAT('คุณ',d.name) customer_name,
    a.amount,a.machine,a.per,
    DATE_FORMAT(a.date_produce,'%d/%m/%Y') produce,
    DATE_FORMAT(a.date_delivery,'%d/%m/%Y') delivery,
    GROUP_CONCAT(e.name) products, 
    a.`text`,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created 
    FROM inventory.purchase a 
    LEFT JOIN inventory.purchase_item b 
    ON a.id  = b.purchase_id  
    LEFT JOIN inventory.`user` c 
    ON a.user_id = c.login 
    LEFT JOIN inventory.customer d 
    ON a.customer_id = d.id 
    LEFT JOIN inventory.product e 
    ON b.product_id = e.id
    WHERE a.status = 3
    AND b.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND a.name LIKE '%{$keyword}%' ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.date_produce DESC ";
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
      $status = "<a href='/purchase/check/{$row['uuid']}' class='badge badge-warning font-weight-light'>รอตรวจสอบ</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['customer_name'],
        $row['amount'],
        $row['produce'],
        $row['delivery'],
        str_replace(",", "<br>", $row['products']),
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
    $sql = "SELECT COUNT(*) FROM inventory.purchase";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "c.name", "a.machine", "a.amount", "a.confirm", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.uuid,CONCAT('PR',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) fullname,
    a.bom,c.name bom_name,a.machine,
    a.amount,a.date,a.text,
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
        WHEN a.status = 5 THEN 'ผ่านการตรวจสอบ'
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
    WHERE a.status != 0 ";

    if (!empty($keyword)) {
      $sql .= " AND a.name LIKE '%{$keyword}%' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.date DESC ";
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
      $status = "<a href='/purchase/manage-edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['id']}'>ลบ</a>";
      $data[] = [
        $status,
        $row['ticket'],
        $row['bom_name'],
        $row['machine'],
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
        WHEN a.type = 1 THEN 'ผู้ตรวจสอบ'
        ELSE NULL
      END
    ) type_name,
    (
      CASE
        WHEN a.type = 1 THEN 'primary'
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

  public function customer_select($keyword)
  {
    $sql = "SELECT a.id,CONCAT('คุณ',a.name) `text`
    FROM inventory.customer a
    WHERE a.type = 2
    AND a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.contact LIKE '%{$keyword}%' OR a.address LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
