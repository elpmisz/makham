<?php
$menu = "service";
$page = "service-purchase";
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Purchase;

$PURCHASE = new Purchase();

$row = $PURCHASE->purchase_view([$uuid]);
$items = $PURCHASE->purchase_item_view([$uuid]);

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
      padding: 5px 10px;
    }

    @page {
      margin: 1cm;
    }

    .no-border {
      border: 0 !important;
    }

    .bottom-border {
      border-top: 0;
      border-left: 0;
      border-right: 0;
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

  <!-- Header Section -->
  <table>
    <tr>
      <td class="text-left no-border" width="10%"></td>
      <td class="text-center no-border" width="80%">
        <h2>ใบสั่งผลิต</h2>
      </td>
      <td class="text-right no-border" width="10%"></td>
    </tr>
  </table>

  <!-- Information Section -->
  <table>
    <tr>
      <td class="no-border" width="20%">รายชื่อลูกค้า</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">เลขที่เอกสาร</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['ticket'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">จำนวนที่ผลิต</td>
      <td class="bottom-border" width="30%">
        <?php echo number_format($row['amount'], 0); ?>
      </td>
      <td class="no-border" width="20%">วันที่</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['created'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">จำนวนตู้</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['machine'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">ผู้ทำรายการ</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['fullname'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">ตู้ละ</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['per'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">วันที่ผลิต</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['produce'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">เลขที่ใบเบิก</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['issue_ticket'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">วันที่ส่งลูกค้า</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['delivery'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
  </table>

  <!-- Purpose Section -->
  <table>
    <tr>
      <td class="no-border" width="20%">วัตถุประสงค์</td>
      <td class="bottom-border" width="80%">
        <?php echo nl2br(htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8')); ?>
      </td>
    </tr>
  </table>

  <!-- Items Section -->
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
    <?php foreach ($items as $key => $item) : ?>
      <tr>
        <td class="text-center"><?php echo htmlspecialchars($key + 1, ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($item['location_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($item['store_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($item['confirm'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($item['unit_name'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

</body>

</html>
<?php
// รับข้อมูล HTML และล้าง buffer
$html = ob_get_contents();
ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'default_font' => 'garuda']);
$mpdf->WriteHTML($html);
$date = date('Ymd');
$mpdf->Output("{$date}_purchase.pdf", 'I');
