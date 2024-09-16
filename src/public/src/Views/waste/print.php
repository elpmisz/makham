<?php
$menu = "service";
$page = "service-purchase";
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Waste;

$WASTE = new Waste();

$row = $WASTE->waste_view([$uuid]);

$items = $WASTE->item_view([$uuid, 1]);
$wastes = $WASTE->item_view([$uuid, 2]);

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
      font-size: 90%;
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
      border: 0;
      border-bottom: 1px solid #000 !important;
    }

    .text-center {
      text-align: center;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }
  </style>
</head>

<body>

  <table>
    <tr>
      <td class="text-left no-border" width="10%"></td>
      <td class="text-center no-border" width="80%">
        <h2>ใบสรุปของเสีย</h2>
      </td>
      <td class="text-right no-border" width="10%"></td>
    </tr>
  </table>

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
      <td class="no-border" width="20%">เลขที่ใบสั่งผลิต</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['purchase_ticket'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">วันที่</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['created'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
  </table>

  <table>
    <tr>
      <td class="no-border" width="20%">รายละเอียด</td>
      <td class="bottom-border" width="80%">
        <?php echo nl2br(htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8')); ?>
      </td>
    </tr>
  </table>

  <span>วัตถุดิบ</span>
  <table>
    <tr>
      <th width="5%">#</th>
      <th width="30%">วัตถุดิบ</th>
      <th width="10%">ปริมาณ</th>
      <th width="40%">หมายเหตุ</th>
    </tr>
    <?php foreach ($items as $key => $item): ?>
      <tr>
        <td class="text-center"><?php echo htmlspecialchars($key + 1, ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($item['item'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($item['remark'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <span>สิ่งแปลกปลอม</span>
  <table>
    <tr>
      <th width="5%">#</th>
      <th width="30%">สิ่งแปลกปลอม</th>
      <th width="10%">ปริมาณ</th>
      <th width="40%">หมายเหตุ</th>
    </tr>
    <?php foreach ($wastes as $key => $waste): ?>
      <tr>
        <td class="text-center"><?php echo htmlspecialchars($key + 1, ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($waste['item'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($waste['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($waste['remark'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'default_font' => 'garuda']);
$mpdf->WriteHTML($html);
$date = date('Ymd');
$mpdf->Output("{$date}_waste.pdf", 'I');
