<?php

namespace App\Classes;

use PDO;

class DashboardPurchase
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

  public function purchase_card()
  {
    $sql = "SELECT 
    FORMAT(SUM(
      CASE 
        WHEN DATE(a.created) = DATE(NOW()) THEN a.confirm ELSE 0
      END
    ),2) dd,
    FORMAT(SUM(
      CASE 
        WHEN YEAR(a.created) = YEAR(NOW()) AND MONTH(a.created) = MONTH(NOW()) THEN a.confirm ELSE 0
      END
    ),2) mm,
    FORMAT(SUM(
      CASE 
        WHEN YEAR(a.created) = YEAR(NOW()) THEN a.confirm ELSE 0
      END
    ),2) yy,
    FORMAT(SUM(a.confirm ),2) total
    FROM inventory.purchase a
    WHERE a.status IN (3,4,5)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function machine_purchase()
  {
    $sql = "SELECT a.machine,b.name machine_name,
    FORMAT(SUM(
    CASE 
      WHEN DATE(a.created) = DATE(NOW()) THEN a.confirm ELSE 0
    END
    ),2) dd,
    FORMAT(SUM(
    CASE 
      WHEN YEAR(a.created) = YEAR(NOW()) AND MONTH(a.created) = MONTH(NOW()) THEN a.confirm ELSE 0
    END
    ),2) mm,
    FORMAT(SUM(
    CASE 
      WHEN YEAR(a.created) = YEAR(NOW()) THEN a.confirm ELSE 0
    END
    ),2) yy,
    FORMAT(SUM(a.confirm ),2) total
    FROM inventory.purchase a
    LEFT JOIN inventory.machine b
    ON a.machine = b.id
    GROUP BY a.machine
    ORDER BY b.name";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function bom_purchase()
  {
    $sql = "SELECT a.bom,b.name bom_name,
    FORMAT(SUM(
    CASE 
      WHEN DATE(a.created) = DATE(NOW()) THEN a.confirm ELSE 0
    END
    ),2) dd,
    FORMAT(SUM(
    CASE 
      WHEN YEAR(a.created) = YEAR(NOW()) AND MONTH(a.created) = MONTH(NOW()) THEN a.confirm ELSE 0
    END
    ),2) mm,
    FORMAT(SUM(
    CASE 
      WHEN YEAR(a.created) = YEAR(NOW()) THEN a.confirm ELSE 0
    END
    ),2) yy,
    FORMAT(SUM(a.confirm ),2) total
    FROM inventory.purchase a
    LEFT JOIN inventory.bom b
    ON a.bom = b.id
    GROUP BY a.bom";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
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
    ORDER BY ROUND(((a.price * SUM(a.confirm))  - IF(d.type = 1,d.discount,((a.price * SUM(a.confirm)) * (d.discount/100)))),2) DESC";
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
    ORDER BY ROUND(((a.price * SUM(a.confirm))  - IF(d.type = 1,d.discount,((a.price * SUM(a.confirm)) * (d.discount/100)))),2) DESC";
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
      $status = "<a href='/purchase/complete/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'  target='_blank'>{$row['status_name']}</a>";
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
}
