<?php
$menu = "service";
$page = "service-purchase";
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Purchase;

$PURCHASE = new Purchase();

$row = $PURCHASE->purchase_view([$uuid]);
$items = $PURCHASE->purchase_item_view([$uuid]);

use Spipu\Html2Pdf\Html2Pdf;

ob_start();
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Inventory</title>
  <style>
    table {
      width: 100%;
      vertical-align: middle;
      border-collapse: collapse;
      font-size: 75%;
    }

    td,
    th {
      border: 1px solid #000;
      padding: 5px 5px 5px 10px;
    }

    @page {
      margin-top: 1cm;
      margin-bottom: 1cm;
      margin-left: 1cm;
      margin-right: 1cm;
    }

    .no-border {
      border: 0 !important;
    }

    .bottom-border {
      border-top: 0px;
      border-left: 0px;
      border-right: 0px;
      border-bottom: 1px solid #000 !important;
    }

    .text-center {
      vertical-align: middle;
      text-align: center;
    }

    .text-left {
      vertical-align: middle;
      text-align: left;
    }

    .text-right {
      vertical-align: middle;
      text-align: right;
    }
  </style>
</head>

<body>

  <table>
    <tr>
      <td class="text-left no-border" width="10%"></td>
      <td class="text-center no-border" width="80%">
        <h2>ใบสั่งผลิต</h2>
      </td>
      <td class="text-right no-border" width="10%"></td>
    </tr>
  </table>

  <table>
    <tr>
      <td class="no-border" width="20%">
        รายชื่อลูกค้า
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['customer_name'] ?>
      </td>
      <td class="no-border" width="20%">
        เลขที่เอกสาร
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['ticket'] ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">
        จำนวนที่ผลิต
      </td>
      <td class="bottom-border" width="30%">
        <?php echo number_format($row['amount'], 0) ?>
      </td>
      <td class="no-border" width="20%">
        วันที่
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['created'] ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">
        จำนวนตู้
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['machine'] ?>
      </td>
      <td class="no-border" width="20%">
        ผู้ทำรายการ
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['fullname'] ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">
        ตู้ละ
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['per'] ?>
      </td>
      <td class="no-border" width="20%">
        วันที่ผลิต
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['produce'] ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">
        เลขที่ใบเบิก
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['issue_ticket'] ?>
      </td>
      <td class="no-border" width="20%">
        วันที่ส่งลูกค้า
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['delivery'] ?>
      </td>
    </tr>
  </table>

  <table>
    <tr>
      <td class="no-border" width="20%">
        วัตถุประสงค์
      </td>
      <td class="bottom-border" width="80%">
        <?php echo str_replace("\n", "<br>", $row['text']) ?>
      </td>
    </tr>
  </table>

  <table style="margin-top: 20px;">
    <tr>
      <th width="5%">#</th>
      <th width="20%">วัตถุดิบ</th>
      <th width="30%">คลัง</th>
      <th width="20%">ห้อง</th>
      <th width="10%">ปริมาณ (เป้าหมาย)</th>
      <th width="10%">ปริมาณ (ผลิต)</th>
      <th width="10%">หน่วยนับ</th>
    </tr>
    <?php
    foreach ($items as $key => $item) :
      $key++;
    ?>
      <tr>
        <td class="text-center"><?php echo $key ?></td>
        <td><?php echo $item['product_name'] ?></td>
        <td><?php echo $item['location_name'] ?></td>
        <td><?php echo $item['store_name'] ?></td>
        <td class="text-center"><?php echo $item['quantity'] ?></td>
        <td class="text-center"><?php echo $item['confirm'] ?></td>
        <td class="text-center"><?php echo $item['unit_name'] ?></td>
      </tr>
    <?php
    endforeach;
    ?>
  </table>

</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'default_font' => 'garuda']);
$mpdf->WriteHTML($html);
$date = date('Ymd');
$mpdf->Output("{$date}_purchase.pdf", 'I');
