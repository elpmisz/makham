<?php

namespace App\Classes;

use PDO;

class Sale
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "SALE CLASS";
  }

  public function sale_last()
  {
    $sql = "SELECT IFNULL(MAX(a.last) + 1,1) last
    FROM inventory.sale a
    WHERE YEAR(a.created) = YEAR(NOW())";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    return (isset($row['last']) ? $row['last'] : "");
  }

  public function amount_update($data)
  {
    $sql = "UPDATE inventory.sale SET
    amount  = ?
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function sale_insert($data)
  {
    $sql = "INSERT INTO inventory.sale(uuid,last,user_id,customer_id,text,promotion,vat) VALUES(uuid(),?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_insert($data)
  {
    $sql = "INSERT INTO inventory.issue_item(sale_id,product_id,price,quantity,confirm) VALUES(?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function sale_view($data)
  {
    $sql = "SELECT a.uuid,a.text,CONCAT('SA',YEAR(a.created),LPAD(a.last,4,'0')) ticket,
    a.user_id,CONCAT(b.firstname,' ',b.lastname) fullname,
    a.customer_id,d.name customer,
    a.promotion,c.name promotion_name,c.type promotion_type,
    IF(c.type = 1,c.discount,(c.discount / 100)) discount,a.vat,a.amount,
    ROUND(((a.amount * c.discount) / 100),2) discount_amount,
    ROUND((a.amount - ((a.amount * c.discount) / 100)),2) sale_total,
    ROUND((
      ((a.amount - ((a.amount * c.discount) / 100)) * 7) / 107
    ),2) vat_total,
    ROUND((
      (a.amount - ((a.amount * c.discount) / 100)) -
      (((a.amount - ((a.amount * c.discount) / 100)) * 7) / 107)
    ),2) discount_total,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM inventory.sale a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.promotion c
    ON a.promotion = c.id
    LEFT JOIN inventory.customer d
    ON a.customer_id = d.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function item_view($data)
  {
    $sql = "SELECT a.product_id,c.code product_code,c.name product_name,d.name unit_name,
    c.price,a.confirm amount,(c.price * a.confirm) total
    FROM inventory.issue_item a
    LEFT JOIN inventory.sale b
    ON a.sale_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    LEFT JOIN inventory.unit d
    ON c.unit = d.id
    WHERE b.uuid = ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function sale_update($data)
  {
    $sql = "UPDATE inventory.sale SET
    name = ?,
    text = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.sale WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,a.name,a.text,
    (
      CASE
        WHEN a.status = 1 THEN 'ใช้งาน'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    DATE_FORMAT(a.updated, '%d/%m/%Y, %H:%i น.') updated
    FROM inventory.sale a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function sale_data()
  {
    $sql = "SELECT COUNT(*) FROM inventory.sale";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "b.firstname", "d.name", "a.text", "c.name", "a.vat", "ROUND((a.amount - ((a.amount * c.discount) / 100)),2)", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.uuid,a.text,a.user_id,CONCAT(b.firstname,' ',b.lastname) fullname,
    a.customer_id,d.name customer,
    a.promotion,c.name promotion_name,c.discount,a.vat,a.amount,
    ROUND(((a.amount * c.discount) / 100),2) discount_amount,
    ROUND((a.amount - ((a.amount * c.discount) / 100)),2) sale_total,
    ROUND((
      ((a.amount - ((a.amount * c.discount) / 100)) * 7) / 107
    ),2) vat_total,
    ROUND((
      (a.amount - ((a.amount * c.discount) / 100)) -
      (((a.amount - ((a.amount * c.discount) / 100)) * 7) / 107)
    ),2) discount_total,
    (
      CASE
        WHEN a.status = 1 THEN 'ทำรายการเรียบร้อยแล้ว'
        WHEN a.status = 2 THEN 'รายการถูกยกเลิก'
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
    FROM inventory.sale a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.promotion c
    ON a.promotion = c.id
    LEFT JOIN inventory.customer d
    ON a.customer_id = d.id ";

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
      $status = "<a href='/sale/complete/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['fullname'],
        $row['customer'],
        str_replace("\n", "<br>", $row['text']),
        $row['promotion_name'],
        "{$row['vat']} %",
        $row['sale_total'],
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

  public function customer_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.customer a
    WHERE a.type = 2
    AND a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.email LIKE '%{$keyword}%' OR a.contact LIKE '%{$keyword}%' OR a.address LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function promotion_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.promotion a
    WHERE DATE(NOW()) < a.end ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.end ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function promotion_detail($data)
  {
    $sql = "SELECT a.type,IF(a.type = 1,a.discount,(a.discount / 100)) discount
    FROM inventory.promotion a
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function product_select($keyword)
  {
    $sql = "SELECT e.id id,CONCAT('[',e.code,'] ',e.name) text
    FROM inventory.issue_item a
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    LEFT JOIN inventory.purchase c
    ON a.purchase_id = c.id
    LEFT JOIN inventory.sale d
    ON a.sale_id = d.id
    RIGHT JOIN inventory.product e
    ON a.product_id = e.id
    LEFT JOIN inventory.customer f
    ON e.supplier = f.id
    LEFT JOIN inventory.unit g
    ON e.unit = g.id
    LEFT JOIN inventory.brand h
    ON e.brand = h.id 
    LEFT JOIN inventory.category i
    ON e.category = i.id 
    LEFT JOIN inventory.location j
    ON e.location = j.id
    WHERE e.status = 1
    AND j.id = 2 ";
    if (!empty($keyword)) {
      $sql .= " AND (e.code LIKE '%{$keyword}%' OR e.name LIKE '%{$keyword}%') ";
    }
    $sql .= " GROUP BY e.id
    HAVING (
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    ) > 0 ORDER BY e.code ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function product_show()
  {
    $sql = "SELECT e.id product_id,e.uuid product_uuid,e.code product_code,e.name product_name,
    e.cost,e.price,e.min,e.max,
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0)) income,
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0)) outcome,
    (
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    ) remain,
    e.supplier,f.name supplier_name,
    e.unit,g.name unit_name,
    e.brand,h.name brand_name,
    e.category,i.name category_name,
    e.location,j.name location_name,
    IF(MAX(a.created) IS NOT NULL,
      DATE_FORMAT(MAX(a.created),'%d/%m/%Y, %H:%i น.'),
      DATE_FORMAT(e.created,'%d/%m/%Y, %H:%i น.')
    ) created,
    IF(e.status = 1,'ใช้งาน','ระงับการใช้งาน') status_name,
    IF(e.status = 1,'success','danger') status_color,
    (SELECT `name` FROM inventory.product_image WHERE product = e.id AND status = 1 ORDER BY id ASC LIMIT 1) image
    FROM inventory.issue_item a
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    LEFT JOIN inventory.purchase c
    ON a.purchase_id = c.id
    LEFT JOIN inventory.sale d
    ON a.sale_id = d.id
    RIGHT JOIN inventory.product e
    ON a.product_id = e.id
    LEFT JOIN inventory.customer f
    ON e.supplier = f.id
    LEFT JOIN inventory.unit g
    ON e.unit = g.id
    LEFT JOIN inventory.brand h
    ON e.brand = h.id 
    LEFT JOIN inventory.category i
    ON e.category = i.id 
    LEFT JOIN inventory.location j
    ON e.location = j.id
    WHERE e.status = 1
    AND j.id = 2
    GROUP BY e.id
    HAVING (
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    ) > 0";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function product_detail($data)
  {
    $sql = "SELECT e.id product_id,e.uuid product_uuid,e.code product_code,e.name product_name,
    e.cost,e.price,e.min,e.max,
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0)) income,
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0)) outcome,
    (
    SUM(IF(b.type = 1 AND b.status = 2,a.confirm,0) ) -
    SUM(IF((b.type = 2 AND b.status = 2) OR (c.status IN (3,4,5)) OR (d.status = 1),a.confirm,0))
    ) remain,
    e.supplier,f.name supplier_name,
    e.unit,g.name unit_name,
    e.brand,h.name brand_name,
    e.category,i.name category_name,
    e.location,j.name location_name,
    IF(MAX(a.created) IS NOT NULL,
      DATE_FORMAT(MAX(a.created),'%d/%m/%Y, %H:%i น.'),
      DATE_FORMAT(e.created,'%d/%m/%Y, %H:%i น.')
    ) created,
    IF(e.status = 1,'ใช้งาน','ระงับการใช้งาน') status_name,
    IF(e.status = 1,'success','danger') status_color,
    (SELECT `name` FROM inventory.product_image WHERE product = e.id AND status = 1 ORDER BY id ASC LIMIT 1) image
    FROM inventory.issue_item a
    LEFT JOIN inventory.issue b
    ON a.issue_id = b.id
    LEFT JOIN inventory.purchase c
    ON a.purchase_id = c.id
    LEFT JOIN inventory.sale d
    ON a.sale_id = d.id
    RIGHT JOIN inventory.product e
    ON a.product_id = e.id
    LEFT JOIN inventory.customer f
    ON e.supplier = f.id
    LEFT JOIN inventory.unit g
    ON e.unit = g.id
    LEFT JOIN inventory.brand h
    ON e.brand = h.id 
    LEFT JOIN inventory.category i
    ON e.category = i.id 
    LEFT JOIN inventory.location j
    ON e.location = j.id
    WHERE e.id = ?
    GROUP BY e.id
    ORDER BY e.code ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }
}
