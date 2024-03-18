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
    $sql = "INSERT INTO inventory.product(uuid,code,name,cost,price,min,max,bom_id,supplier,unit,brand,category,location,text) VALUES(uuid(),?,?,?,?,?,?,?,?,?,?,?,?,?)";
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
    a.location,f.name location_name,
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
    LEFT JOIN inventory.bom g
    ON a.bom_id = g.id
    WHERE a.uuid = ?";
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
    location = ?,
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

  public function location_id($data)
  {
    $sql = "SELECT id
    FROM inventory.location a
    WHERE a.name LIKE CONCAT('%',?,'%')";
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

  public function location_select($keyword)
  {
    $sql = "SELECT a.id,a.name text
    FROM inventory.location a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC LIMIT 50";
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
    f.name location_name,
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
    LEFT JOIN inventory.location f
    ON a.location = f.id ";
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
      "e.status", "e.code", "e.name", "i.name", "j.name", "e.cost", "e.price", "e.min",
      "
      (
        SUM(IF(b.`type` = 1 AND b.status = 2,a.confirm,0) ) -
        SUM(IF((b.`type` = 2 AND b.status = 2) OR (c.`status` IN (3,4,5)) OR (d.`status` = 1),a.confirm,0))
      )
      ", "IF(MAX(a.created) IS NOT NULL,MAX(a.created),e.created)"
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

    $sql = "SELECT e.id product_id,e.uuid product_uuid,e.code product_code,e.name product_name,
    e.cost,e.price,e.`min`,e.`max`,
    SUM(IF(b.`type` = 1 AND b.status = 2,a.confirm,0)) income,
    SUM(IF((b.`type` = 2 AND b.status = 2) OR (c.`status` IN (3,4,5)) OR (d.`status` = 1),a.confirm,0)) outcome,
    (
    SUM(IF(b.`type` = 1 AND b.status = 2,a.confirm,0) ) -
    SUM(IF((b.`type` = 2 AND b.status = 2) OR (c.`status` IN (3,4,5)) OR (d.`status` = 1),a.confirm,0))
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
    IF(e.status = 1,'success','danger') status_color
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
    WHERE e.status = 1 ";

    if (!empty($keyword)) {
      $sql .= " AND (e.name LIKE '%{$keyword}%' OR e.code LIKE '%{$keyword}%') ";
    }
    if (!empty($category)) {
      $sql .= " AND e.category = '{$category}' ";
    }
    if (!empty($location)) {
      $sql .= " AND e.location = '{$location}' ";
    }

    $sql .= " GROUP BY e.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY e.status ASC, e.code ASC ";
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
        $row['location_name'],
        $row['cost'],
        $row['price'],
        $row['min'],
        $row['remain'],
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
        ELSE NULL
      END
    ) uuid,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN 'issue'
        WHEN b.purchase_id IS NOT NULL THEN 'purchase'
        ELSE NULL
      END
    ) page,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN CONCAT(d.firstname,' ',d.lastname)
        WHEN b.purchase_id IS NOT NULL THEN CONCAT(f.firstname,' ',f.lastname)
        ELSE NULL
      END
    ) username,a.code,a.name product_name,
    IF(c.type = 1,'นำเข้า','เบิกออก') type_name,IF(c.type = 1,'success','danger') type_color,c.status,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN c.text
        WHEN b.purchase_id IS NOT NULL THEN e.text
        ELSE NULL
      END
    ) text,IF(c.status = 2 OR e.status IN (3,4,5),b.confirm,0) quantity,
    (
      CASE 
        WHEN b.issue_id IS NOT NULL THEN DATE_FORMAT(c.created,'%d/%m/%Y, %H:%i น.')
        WHEN b.purchase_id IS NOT NULL THEN DATE_FORMAT(e.created,'%d/%m/%Y, %H:%i น.')
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
