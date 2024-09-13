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
    $sql = "INSERT INTO inventory.issue(uuid,last,type,`group`,date,text,user_id) VALUES(uuid(),?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function issue_purchase($data)
  {
    $sql = "INSERT INTO inventory.issue(uuid,last,type,date,text,user_id,status) VALUES(uuid(),?,1,NOW(),?,?,2)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.issue_item 
    WHERE issue_id = ? 
    AND product_id = ? 
    AND location_id = ? 
    AND store_id = ? 
    AND unit_id = ? 
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_item(issue_id,product_id,type,`group`,location_id,store_id,quantity,unit_id) VALUES(?,?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_import($data)
  {
    $sql = "INSERT INTO inventory.issue_item(`issue_id`, `product_id`, `type`, `location_id`, `store_id`, `quantity`, `confirm`,`unit_id`) VALUES(?,?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }


  public function item_purchase($data)
  {
    $sql = "INSERT INTO inventory.issue_item(issue_id,product_id,type,location_id,quantity,confirm,unit_id) VALUES(?,?,1,?,?,?,1)";
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
    $sql = "SELECT a.id,a.uuid,a.text,a.type,a.group,a.status,
    CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    DATE_FORMAT(a.date,'%d/%m/%Y') date,
    b.firstname,b.lastname,
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
        WHEN a.group = 1 THEN 'สั่งผลิต'
        WHEN a.group = 2 THEN 'รอผลิต'
        WHEN a.group = 3 THEN 'ขาย'
        WHEN a.group = 4 THEN 'อื่นๆ'
        ELSE NULL
      END
    ) group_name,
    (
      CASE
        WHEN a.group = 1 THEN 'info'
        WHEN a.group = 2 THEN 'primary'
        WHEN a.group = 3 THEN 'success'
        WHEN a.group = 4 THEN 'danger'
        ELSE NULL
      END
    ) group_color,
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
    d.firstname approver_firstname,d.lastname approver_lastname,
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
    $sql = "SELECT b.id item_id,b.product_id,c.`name` product_name,b.location_id,d.`name` location_name,
    b.store_id,CONCAT('ห้อง ',e.room,' ชั้น ',e.floor,' โซน ',e.zone) store_name,
    b.quantity,b.confirm,
    IF(b.unit_id != c.unit,FORMAT((b.quantity/c.per),0),FORMAT(b.quantity,0)) product_quantity,
    IF(b.unit_id != c.unit,FORMAT((b.confirm/c.per),0),FORMAT(b.confirm,0)) product_confirm,
    b.unit_id,f.`name` unit_name,
    c.unit,g.name product_unit
    FROM inventory.issue a
    LEFT JOIN inventory.issue_item b
    ON a.id = b.issue_id
    LEFT JOIN inventory.product c
    ON b.product_id = c.id
    LEFT JOIN inventory.location d
    ON b.location_id = d.id
    LEFT JOIN inventory.store e
    ON b.store_id = e.id
    LEFT JOIN inventory.unit f
    ON b.unit_id = f.id
    LEFT JOIN inventory.unit g
    ON c.unit = g.id
    WHERE a.`uuid` = ?
    ORDER BY b.id ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function exchange_view($uuid)
  {
    $sql = "SELECT x.issue_id,x.item_id,x.product_id,x.product_name,
    x.send_location_id,x.send_location,x.send_store_id,x.send_store,
    y.receive_location_id,y.receive_location,y.receive_store_id,y.receive_store,
    x.quantity,x.confirm,x.product_quantity,x.product_confirm,
    x.unit_name,x.unit_id,x.unit,x.product_unit
    FROM
    (
      SELECT a.id issue_id, b.id item_id,b.product_id,c.`name` product_name,
      b.location_id send_location_id,d.`name` send_location,
      b.store_id send_store_id,CONCAT('ห้อง ',e.room,' ชั้น ',e.floor,' โซน ',e.zone) send_store,
      b.quantity,b.confirm,
      IF(b.unit_id != c.unit,FORMAT((b.quantity/c.per),0),FORMAT(b.quantity,0)) product_quantity,
      IF(b.unit_id != c.unit,FORMAT((b.confirm/c.per),0),FORMAT(b.confirm,0)) product_confirm,
      b.unit_id,c.unit,f.`name` unit_name,
      g.name product_unit,b.`group`
      FROM inventory.issue a
      LEFT JOIN inventory.issue_item b
      ON a.id = b.issue_id
      LEFT JOIN inventory.product c
      ON b.product_id = c.id
      LEFT JOIN inventory.location d
      ON b.location_id = d.id
      LEFT JOIN inventory.store e
      ON b.store_id = e.id
      LEFT JOIN inventory.unit f
      ON b.unit_id = f.id
      LEFT JOIN inventory.unit g
      ON c.unit = g.id
      WHERE a.`uuid` = '{$uuid}'
      AND b.`status` = 1
      AND b.`type` = 2
    ) x 
    LEFT JOIN 
    (
      SELECT a.id issue_id, b.id item_id,b.product_id,c.`name` product_name,
      b.location_id receive_location_id,d.`name` receive_location,
      b.store_id receive_store_id,CONCAT('ห้อง ',e.room,' ชั้น ',e.floor,' โซน ',e.zone) receive_store,
      b.quantity,b.confirm,
      IF(b.unit_id != c.unit,FORMAT((b.quantity/c.per),0),FORMAT(b.quantity,0)) product_quantity,
      IF(b.unit_id != c.unit,FORMAT((b.confirm/c.per),0),FORMAT(b.confirm,0)) product_confirm,
      f.`name` unit_name,
      g.name product_unit,b.`group`
      FROM inventory.issue a
      LEFT JOIN inventory.issue_item b
      ON a.id = b.issue_id
      LEFT JOIN inventory.product c
      ON b.product_id = c.id
      LEFT JOIN inventory.location d
      ON b.location_id = d.id
      LEFT JOIN inventory.store e
      ON b.store_id = e.id
      LEFT JOIN inventory.unit f
      ON b.unit_id = f.id
      LEFT JOIN inventory.unit g
      ON c.unit = g.id
      WHERE a.`uuid` = '{$uuid}'
      AND b.`status` = 1
      AND b.`type` = 1
    ) y
    ON x.issue_id = y.issue_id
    AND x.group = y.group
    GROUP BY x.item_id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function exchange_key($data)
  {
    $sql = "SELECT MAX(b.`group` ) `group`
    FROM inventory.issue a
    LEFT JOIN inventory.issue_item b
    ON a.id = b.issue_id
    WHERE a.`uuid` = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['group']) ? $row['group'] : "");
  }

  public function exchange_confirm($data)
  {
    $sql = "UPDATE inventory.issue_item SET
    confirm = ?,
    updated = NOW()
    WHERE id = ?";
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

  public function item_detail($data, $location, $store)
  {
    $sql = "SELECT a.id product_id,a.uuid product_uuid,a.code product_code,a.name product_name,
    SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1 AND b.location_id = {$location} AND b.store_id = {$store},IF(c.status = 1,IF(a.unit != b.unit_id,(b.quantity/a.per),b.quantity),IF(a.unit != b.unit_id,(b.confirm/a.per),b.confirm)),0)) income,
    SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1 AND b.location_id = {$location} AND b.store_id = {$store},IF(c.status = 1,IF(a.unit != b.unit_id,(b.quantity/a.per),b.quantity),IF(a.unit != b.unit_id,(b.confirm/a.per),b.confirm)),0)) outcome,
    (
      SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1 AND b.location_id = {$location} AND b.store_id = {$store},IF(c.status = 1,IF(a.unit != b.unit_id,(b.quantity/a.per),b.quantity),IF(a.unit != b.unit_id,(b.confirm/a.per),b.confirm)),0)) -
      SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1 AND b.location_id = {$location} AND b.store_id = {$store},IF(c.status = 1,IF(a.unit != b.unit_id,(b.quantity/a.per),b.quantity),IF(a.unit != b.unit_id,(b.confirm/a.per),b.confirm)),0))
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

  public function item_quantity_remain($data)
  {
    $sql = "SELECT a.product_id,
    SUM(qty_in) qty_in,SUM(qty_out) qty_out,FORMAT((SUM(qty_in) - SUM(qty_out)),0) qty_remain,
    SUM(cf_in) cf_in,SUM(cf_out) cf_out,FORMAT((SUM(cf_in) - SUM(cf_out)),0) cf_remain,
    unit_name
    FROM (
      SELECT b.product_id,d.name unit_name,
      IF(a.`status` = 1 AND b.`type` = 1,(IF(c.unit != b.unit_id,(b.quantity/c.per),b.quantity)),0) qty_in,
      IF(a.`status` = 1 AND b.`type` = 2,(IF(c.unit != b.unit_id,(b.quantity/c.per),b.quantity)),0) qty_out,
      IF(a.`status` = 2 AND b.`type` = 1,(IF(c.unit != b.unit_id,(b.confirm/c.per),b.confirm)),0) cf_in,
      IF(a.`status` = 2 AND b.`type` = 2,(IF(c.unit != b.unit_id,(b.confirm/c.per),b.confirm)),0) cf_out
      FROM inventory.issue a
      LEFT JOIN inventory.issue_item b
      ON a.id = b.issue_id
      LEFT JOIN inventory.product c
      ON b.product_id = c.id
      LEFT JOIN inventory.unit d
      ON c.unit = d.id
      WHERE b.product_id = ?
      AND b.location_id = ?
      AND b.store_id = ?
      AND b.id != ?
      AND b.status = 1
    ) a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (empty($row['qty_remain']) && intval($row['qty_remain']) === 0 ? "0 ลัง" : "{$row['qty_remain']} {$row['unit_name']}");
  }

  public function item_confirm_remain($data)
  {
    $sql = "SELECT a.product_id,
    SUM(qty_in) qty_in,SUM(qty_out) qty_out,FORMAT((SUM(qty_in) - SUM(qty_out)),0) qty_remain,
    SUM(cf_in) cf_in,SUM(cf_out) cf_out,FORMAT((SUM(cf_in) - SUM(cf_out)),0) cf_remain,
    unit_name
    FROM (
      SELECT b.product_id,d.name unit_name,
      IF(a.`status` = 1 AND b.`type` = 1,(IF(c.unit != b.unit_id,(b.quantity/c.per),b.quantity)),0) qty_in,
      IF(a.`status` = 1 AND b.`type` = 2,(IF(c.unit != b.unit_id,(b.quantity/c.per),b.quantity)),0) qty_out,
      IF(a.`status` = 2 AND b.`type` = 1,(IF(c.unit != b.unit_id,(b.confirm/c.per),b.confirm)),0) cf_in,
      IF(a.`status` = 2 AND b.`type` = 2,(IF(c.unit != b.unit_id,(b.confirm/c.per),b.confirm)),0) cf_out
      FROM inventory.issue a
      LEFT JOIN inventory.issue_item b
      ON a.id = b.issue_id
      LEFT JOIN inventory.product c
      ON b.product_id = c.id
      LEFT JOIN inventory.unit d
      ON c.unit = d.id
      WHERE b.product_id = ?
      AND b.location_id = ?
      AND b.store_id = ?
      AND b.id != ?
      AND b.status = 1
    ) a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (empty($row['cf_remain']) && intval($row['cf_remain']) === 0 ? "0 ลัง" : "{$row['cf_remain']} {$row['unit_name']}");
  }

  public function issue_update($data)
  {
    $sql = "UPDATE inventory.issue SET
    `group` = ?,
    date = ?,
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

  public function issue_delete($data)
  {
    $sql = "UPDATE inventory.issue SET
    status = 0,
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

  public function product_count($data)
  {
    $sql = "SELECT COUNT(*)
    FROM inventory.product a
    WHERE a.code = ? AND a.name  = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function product_last_id()
  {
    $sql = "SELECT id FROM inventory.product a ORDER BY id DESC LIMIT 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return (!empty($row['id']) ? $row['id'] : "");
  }

  public function product_id($data)
  {
    $sql = "SELECT id FROM inventory.product WHERE code = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (!empty($row['id']) ? $row['id'] : "");
  }

  public function product_insert($data)
  {
    $sql = "INSERT INTO inventory.product(`uuid`, `code`, `name`, `per`, `unit`) VALUES(uuid(),?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function download()
  {
    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    CONCAT(d.firstname,' ',d.lastname) username,
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
    ON a.user_id = d.id
    WHERE b.status = 1
    ORDER BY a.created DESC";
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

    $column = ["a.status", "a.last", "a.type", "b.firstname", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    b.firstname,b.lastname,
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
        WHEN a.type = 1 THEN 'primary'
        WHEN a.type = 2 THEN 'success'
        WHEN a.type = 3 THEN 'warning'
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
    $sql = "SELECT COUNT(*) FROM inventory.issue";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "a.type", "b.firstname", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id,a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    b.firstname,b.lastname,
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
        WHEN a.type = 1 THEN 'primary'
        WHEN a.type = 2 THEN 'success'
        WHEN a.type = 3 THEN 'warning'
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
    WHERE a.status != 0 ";

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
      $status = "<a href='/issue/manage-edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['id']}'>ลบ</a>";
      $type = "<span class='badge badge-{$row['type_color']}'>{$row['type_name']}</span>";
      $data[] = [
        $status,
        $row['ticket'],
        $type,
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
    $sql = "SELECT COUNT(*) FROM inventory.issue";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.last", "a.type", "b.firstname", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) ticket,
    b.firstname,b.lastname,
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
        WHEN a.type = 1 THEN 'primary'
        WHEN a.type = 2 THEN 'success'
        WHEN a.type = 3 THEN 'warning'
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

  public function item_50_select($keyword)
  {
    $sql = "SELECT id,CONCAT('[',p.code,'] ',p.name) text
    FROM inventory.product p
    WHERE p.status = 1
    AND p.code LIKE '50%' ";
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

  public function location_id($data)
  {
    $sql = "SELECT a.id 
    FROM inventory.location a
    WHERE a.name = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function store_id($data)
  {
    $sql = "SELECT a.id 
    FROM inventory.store a
    WHERE CONCAT('ห้อง ',a.room,' ชั้น ',a.`floor`,' โซน ',a.`zone`) = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function product_per($data)
  {
    $sql = "SELECT per
    FROM inventory.product a
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['per']) ? $row['per'] : "");
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

  public function store_select($keyword)
  {
    $sql = "SELECT a.id, CONCAT('ห้อง ',a.room,' ชั้น ',a.`floor`,' โซน ',a.`zone`) `text`
    FROM inventory.store a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.room ASC, a.floor ASC, a.zone ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function unit_select($keyword)
  {
    $sql = "SELECT a.id, a.name text
    FROM inventory.unit a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function issue_select($keyword)
  {
    $sql = "SELECT a.id,CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) `text`
    FROM inventory.issue a
    WHERE a.status IN (1,2)
    AND a.type = 2 ";
    if (!empty($keyword)) {
      $sql .= " AND (CONCAT('RE',YEAR(a.created),LPAD(a.last,5,'0')) LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.created ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
