<?php

namespace App\Classes;

use PDO;

class Product
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "UNIT CLASS";
  }

  public function product_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.product WHERE code = ? AND name = ? AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function product_insert($data)
  {
    $sql = "INSERT INTO inventory.product(uuid,code,name,cost,price,min,max,bom_id,supplier,unit,brand,category,store,text) VALUES(uuid(),?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function product_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.code,a.name product_name,a.cost,a.price,a.min,a.max,a.text,a.status,
    a.bom_id,g.uuid bom_uuid, g.name bom_name,
    a.supplier,b.name supplier_name,
    a.unit,c.name unit_name,
    a.brand,d.name brand_name,
    a.category,e.name category_name,
    a.store,CONCAT(f.room,f.floor,f.zone) store_name,
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
    LEFT JOIN inventory.store f
    ON a.store = f.id
    LEFT JOIN inventory.bom g
    ON a.bom_id = g.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function stock_view($data)
  {
    $sql = "SELECT a.id product_id,a.uuid product_uuid,a.code product_code,a.name product_name,
    a.cost product_cost,a.price product_price,a.min product_min,a.max product_max,
    SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0)) income,
    SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0)) outcome,
    (
      SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0)) -
      SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0))
    ) remain,
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
    WHERE a.uuid = ?
    GROUP BY b.location_id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function issue_count($data)
  {
    $sql = "SELECT COUNT(*) FROM	inventory.issue_item a WHERE a.product_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function product_update($data)
  {
    $sql = "UPDATE inventory.product SET
    code = ?,
    name = ?,
    cost = ?,
    price = ?,
    min = ?,
    max = ?,
    bom_id = ?,
    supplier = ?,
    unit = ?,
    brand = ?,
    category = ?,
    store = ?,
    text = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function image_insert($data)
  {
    $sql = "INSERT INTO inventory.product_image(product,name) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function image_delete($data)
  {
    $sql = "UPDATE inventory.product_image SET
    status = 2,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function image_view($data)
  {
    $sql = "SELECT b.id,b.name 
    FROM inventory.product a
    LEFT JOIN inventory.product_image b
    ON a.id = b.product
    WHERE a.uuid = ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function uuid_count($data)
  {
    $sql = "SELECT COUNT(*) FROM inventory.product WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function supplier_id($data)
  {
    $sql = "SELECT id
    FROM inventory.customer a
    WHERE a.name LIKE CONCAT('%',?,'%')";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function unit_id($data)
  {
    $sql = "SELECT id
    FROM inventory.unit a
    WHERE a.name LIKE CONCAT('%',?,'%')";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function brand_id($data)
  {
    $sql = "SELECT id
    FROM inventory.brand a
    WHERE a.name LIKE CONCAT('%',?,'%')";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function category_id($data)
  {
    $sql = "SELECT id
    FROM inventory.category a
    WHERE a.name LIKE CONCAT('%',?,'%')";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function store_id($data)
  {
    $sql = "SELECT id
    FROM inventory.store a
    WHERE CONCAT(a.room,a.floor,a.zone) LIKE CONCAT('%',?,'%')";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['id']) ? $row['id'] : "");
  }

  public function bom_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.bom a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function supplier_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.customer a
    WHERE a.type = 1
    AND a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.email LIKE '%{$keyword}%' OR a.contact LIKE '%{$keyword}%' OR a.address LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function unit_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.unit a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function brand_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.brand a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function category_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.category a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function store_select($keyword)
  {
    $sql = "SELECT a.id,CONCAT(a.room,a.floor,a.zone) text
    FROM inventory.store a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.room LIKE '%{$keyword}%' OR a.floor LIKE '%{$keyword}%' OR a.zone LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY CONCAT(a.room,a.floor,a.zone) ASC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function download()
  {
    $sql = "SELECT a.uuid,a.code,a.name product_name,a.cost,a.price,a.min,a.max,a.text,
    b.name supplier_name,
    c.name unit_name,
    d.name brand_name,
    e.name category_name,
    CONCAT(f.room,f.floor,f.zone) store_name,
    (
      CASE
        WHEN a.status = 1 THEN 'ใช้งาน'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
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
    LEFT JOIN inventory.store f
    ON a.store = f.id ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function product_data($category, $location)
  {
    $sql = "SELECT COUNT(*) FROM inventory.product";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = [
      "a.status", "a.code", "a.name", "g.name", "CONCAT(h.room,h.floor,h.zone)", "a.cost", "a.price", "a.min",
      "
      (
        SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0)) -
        SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0))
      )
      "
    ];

    $category = (!empty($category) ? $category : "");
    $location = (!empty($location) ? $location : "");
    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.id product_id,a.uuid product_uuid,a.code product_code,a.name product_name,
    a.cost product_cost,a.price product_price,a.min product_min,a.max product_max,
    SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0)) income,
    SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0)) outcome,
    (
      SUM(IF(c.status IN (1,2) AND b.type = 1 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0)) -
      SUM(IF(c.status IN (1,2) AND b.type = 2 AND b.status = 1,IF(c.status = 1,b.quantity,b.confirm),0))
    ) remain,
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
    WHERE a.id != '' ";

    if (!empty($keyword)) {
      $sql .= " AND (e.name LIKE '%{$keyword}%' OR e.code LIKE '%{$keyword}%') ";
    }
    if (!empty($category)) {
      $sql .= " AND e.category = '{$category}' ";
    }
    if (!empty($location)) {
      $sql .= " AND e.location = '{$location}' ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.code ASC ";
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
      $status = "<a href='/product/edit/{$row['product_uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $status,
        $row['product_code'],
        $row['product_name'],
        $row['category_name'],
        $row['store_name'],
        $row['product_cost'],
        $row['product_price'],
        $row['product_min'],
        $row['remain'],
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

  public function issue_data($uuid)
  {
    $sql = "SELECT COUNT(*) FROM inventory.product";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.status", "a.code", "a.name", "e.name", "f.name", "a.cost", "a.min", "a.cost", "a.updated"];

    $uuid = (!empty($uuid) ? $uuid : "");

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT 
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN c.uuid
        WHEN b.purchase_id IS NOT NULL THEN e.uuid
        WHEN b.sale_id IS NOT NULL THEN g.`uuid`
        ELSE NULL
      END
    ) uuid,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN 'issue'
        WHEN b.purchase_id IS NOT NULL THEN 'purchase'
        WHEN b.sale_id IS NOT NULL THEN 'sale'
        ELSE NULL
      END
    ) page,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN CONCAT(d.firstname,' ',d.lastname)
        WHEN b.purchase_id IS NOT NULL THEN CONCAT(f.firstname,' ',f.lastname)
        WHEN b.sale_id IS NOT NULL THEN CONCAT(h.firstname,' ',h.lastname)
        ELSE NULL
      END
    ) username,a.code,a.name product_name,
    IF(c.type = 1,'นำเข้า','เบิกออก') type_name,IF(c.type = 1,'success','danger') type_color,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN c.text
        WHEN b.purchase_id IS NOT NULL THEN e.text
        WHEN b.sale_id IS NOT NULL THEN g.text
        ELSE NULL
      END
    ) text,IF(c.status = 2 OR e.status IN (3,4,5) OR g.`status` = 1,b.confirm,0) quantity,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN DATE_FORMAT(c.created,'%d/%m/%Y, %H:%i น.')
        WHEN b.purchase_id IS NOT NULL THEN DATE_FORMAT(e.created,'%d/%m/%Y, %H:%i น.')
        WHEN b.sale_id IS NOT NULL THEN DATE_FORMAT(g.created,'%d/%m/%Y, %H:%i น.')
        ELSE NULL
      END
    ) created
    FROM inventory.product a
    LEFT JOIN inventory.issue_item b
    ON a.id = b.product_id
    LEFT JOIN inventory.issue c
    ON b.issue_id = c.id
    LEFT JOIN inventory.user d
    ON c.user_id = d.id
    LEFT JOIN inventory.purchase e
    ON b.purchase_id = e.id
    LEFT JOIN inventory.user f
    ON e.user_id = f.id
    LEFT JOIN inventory.sale g
    ON b.sale_id = g.id
    LEFT JOIN inventory.`user` h
    ON g.user_id = h.id
    WHERE a.uuid = '{$uuid}'  ";

    if (!empty($keyword)) {
      $sql .= " AND a.name LIKE '%{$keyword}%' ";
    }
    if (!empty($category)) {
      $sql .= " AND a.category = '{$category}' ";
    }
    if (!empty($location)) {
      $sql .= " AND a.location = '{$location}' ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY b.created DESC ";
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
      $status = "<a href='/{$row['page']}/complete/{$row['uuid']}' class='badge badge-primary font-weight-light' target='_blank'>รายละเอียด</a>";
      $type = "<span class='badge badge-{$row['type_color']} font-weight-light'>{$row['type_name']}</span>";
      $data[] = [
        $status,
        $row['username'],
        $type,
        str_replace("\n", "<br>", $row['text']),
        number_format($row['quantity'], 2),
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
}
