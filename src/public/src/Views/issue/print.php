<?php
$menu = "service";
$page = "service-purchase";
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Issue;

$ISSUE = new Issue();

$row = $ISSUE->issue_view([$uuid]);
$items = (intval($row['type']) === 3 ? $ISSUE->exchange_view($uuid) : $ISSUE->item_view([$uuid]));

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

    th,
    td {
      font-size: 90%;
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
      border: 0;
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
        <h2>ใบ<?php echo htmlspecialchars($row['type_name'], ENT_QUOTES, 'UTF-8'); ?>สินค้า</h2>
      </td>
      <td class="text-right no-border" width="10%"></td>
    </tr>
  </table>

  <!-- Information Section -->
  <table>
    <tr>
      <td class="no-border" width="20%">ผู้ทำรายการ</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['fullname'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">เลขที่เอกสาร</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['ticket'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">ประเภท</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['type_name'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">วันที่</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['created'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
    <?php if (intval($row['type']) === 2) : ?>
      <tr>
        <td class="no-border" width="20%">เพื่อ</td>
        <td class="bottom-border" width="30%">
          <?php echo htmlspecialchars($row['group_name'], ENT_QUOTES, 'UTF-8'); ?>
        </td>
        <td class="no-border" width="20%"></td>
        <td class="no-border" width="30%"></td>
      </tr>
    <?php endif; ?>
    <tr>
      <td class="no-border" width="20%">วันที่นำเข้า</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%"></td>
      <td class="no-border" width="30%"></td>
    </tr>
  </table>

  <!-- Details Section -->
  <table>
    <tr>
      <td class="no-border" width="20%">รายละเอียด</td>
      <td class="bottom-border" width="80%">
        <?php echo nl2br(htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8')); ?>
      </td>
    </tr>
  </table>

  <!-- Items Section -->
  <?php if (intval($row['type']) === 3) : ?>
    <table style="margin-top: 20px;">
      <tr>
        <th width="5%">#</th>
        <th width="20%">วัตถุดิบ</th>
        <th width="15%">คลัง<br>(ต้นทาง)</th>
        <th width="15%">ห้อง<br>(ต้นทาง)</th>
        <th width="15%">คลัง<br>(ปลายทาง)</th>
        <th width="15%">ห้อง<br>(ปลายทาง)</th>
        <th width="10%">ปริมาณ<br>(โอนย้าย)</th>
        <th width="10%">ปริมาณ<br>(ตรวจสอบ)</th>
        <th width="10%">หน่วยนับ</th>
      </tr>
      <?php foreach ($items as $key => $item) : ?>
        <tr>
          <td class="text-center"><?php echo htmlspecialchars($key + 1, ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo nl2br(htmlspecialchars($item['send_location'], ENT_QUOTES, 'UTF-8')); ?></td>
          <td><?php echo nl2br(htmlspecialchars($item['send_store'], ENT_QUOTES, 'UTF-8')); ?></td>
          <td><?php echo nl2br(htmlspecialchars($item['receive_location'], ENT_QUOTES, 'UTF-8')); ?></td>
          <td><?php echo nl2br(htmlspecialchars($item['receive_store'], ENT_QUOTES, 'UTF-8')); ?></td>
          <td class="text-right">
            <?php echo htmlspecialchars(number_format($item['quantity'], 0), ENT_QUOTES, 'UTF-8') .
              ($item['unit_id'] === $item['unit'] ? "" : " <br>({$item['product_quantity']} {$item['product_unit']})"); ?>
          </td>
          <td class="text-right">
            <?php echo htmlspecialchars(number_format($item['confirm'], 0), ENT_QUOTES, 'UTF-8') .
              ($item['unit_id'] === $item['unit'] ? "" : " <br>({$item['product_confirm']} {$item['product_unit']})"); ?>
          </td>
          <td class="text-center"><?php echo htmlspecialchars($item['unit_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else : ?>
    <table style="margin-top: 20px;">
      <tr>
        <th width="5%">#</th>
        <th width="20%">วัตถุดิบ</th>
        <th width="30%">คลัง</th>
        <th width="20%">ห้อง</th>
        <th width="10%">ปริมาณ <?php echo htmlspecialchars("({$row['type_name']})", ENT_QUOTES, 'UTF-8'); ?></th>
        <th width="12%">ปริมาณ<br>(ตรวจสอบ)</th>
        <th width="10%">หน่วยนับ</th>
      </tr>
      <?php foreach ($items as $key => $item) : ?>
        <tr>
          <td class="text-center"><?php echo htmlspecialchars($key + 1, ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($item['location_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($item['store_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td class="text-right"><?php echo htmlspecialchars(number_format($item['quantity'], 0, '.', ','), ENT_QUOTES, 'UTF-8'); ?></td>
          <td class="text-right"><?php echo htmlspecialchars(number_format($item['confirm'], 0, '.', ','), ENT_QUOTES, 'UTF-8'); ?></td>
          <td class="text-center"><?php echo htmlspecialchars($item['unit_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

</body>

</html>
<?php
// รับข้อมูล HTML และล้าง buffer
$html = ob_get_contents();
ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'default_font' => 'garuda']);
$mpdf->WriteHTML($html);
$date = date('Ymd');
$mpdf->Output("{$date}_issue.pdf", 'I');
