<?php

namespace App\Classes;

use PDO;

class DashboardSale
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function hello()
  {
    return "DASHBOARD CLASS";
  }

  public function sale_card()
  {
    $sql = "SELECT  
    FORMAT(SUM(IF(DATE(a.created) = DATE(NOW()),(a.amount - ((a.amount * c.discount) / 100)),0)),2) dd,
    FORMAT(SUM(IF(YEAR(a.created) = YEAR(NOW()) AND MONTH(a.created) = MONTH(NOW()),(a.amount - ((a.amount * c.discount) / 100)),0)),2) mm,
    FORMAT(SUM(IF(YEAR(a.created) = YEAR(NOW()),(a.amount - ((a.amount * c.discount) / 100)),0)),2) yy,
    FORMAT(SUM((a.amount - ((a.amount * c.discount) / 100))),2) total
    FROM inventory.sale a
    LEFT JOIN inventory.user b
    ON a.user_id = b.id
    LEFT JOIN inventory.promotion c
    ON a.promotion = c.id
    WHERE a.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function sale_month()
  {
    $sql = "SELECT CONCAT('[',c.code,'] ',c.name) product,c.name product_name,SUM(a.confirm) amount,
    ROUND(((a.price * SUM(a.confirm))  - IF(d.type = 1,d.discount,((a.price * SUM(a.confirm)) * (d.discount/100)))),2) total
    FROM inventory.issue_item a
    LEFT JOIN inventory.sale b
    ON a.sale_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    LEFT JOIN inventory.promotion d
    ON b.promotion = d.id
    WHERE b.status = 1
    AND YEAR(b.created) = YEAR(NOW())
    AND MONTH(b.created) = MONTH(NOW())
    GROUP BY a.product_id
    ORDER BY ROUND(((a.price * SUM(a.confirm))  - IF(d.type = 1,d.discount,((a.price * SUM(a.confirm)) * (d.discount/100)))),2) DESC LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function sale_year()
  {
    $sql = "SELECT CONCAT('[',c.code,'] ',c.name) product,c.name product_name,SUM(a.confirm) amount,
    ROUND(((a.price * SUM(a.confirm))  - IF(d.type = 1,d.discount,((a.price * SUM(a.confirm)) * (d.discount/100)))),2) total
    FROM inventory.issue_item a
    LEFT JOIN inventory.sale b
    ON a.sale_id = b.id
    LEFT JOIN inventory.product c
    ON a.product_id = c.id
    LEFT JOIN inventory.promotion d
    ON b.promotion = d.id
    WHERE b.status = 1
    AND YEAR(b.created) = YEAR(NOW())
    GROUP BY a.product_id
    ORDER BY ROUND(((a.price * SUM(a.confirm))  - IF(d.type = 1,d.discount,((a.price * SUM(a.confirm)) * (d.discount/100)))),2) DESC LIMIT 10";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
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
    IF(c.type = 1,c.discount,(a.amount * (c.discount/100))) discount_amount,
    ROUND((a.amount - IF(c.type = 1,c.discount,(a.amount * (c.discount/100)))),2) sale_total,
    ROUND((
      ((a.amount - IF(c.type = 1,c.discount,(a.amount * (c.discount/100)))) * 7) / 107
    ),2) vat_total,
    ROUND((
      (a.amount - IF(c.type = 1,c.discount,(a.amount * (c.discount/100)))) -
      (((a.amount - IF(c.type = 1,c.discount,(a.amount * (c.discount/100)))) * 7) / 107)
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
      $status = "<a href='/sale/complete/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light' target='_blank'>{$row['status_name']}</a>";
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
}
