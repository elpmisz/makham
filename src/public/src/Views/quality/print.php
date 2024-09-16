<?php
$menu = "service";
$page = "service-quality";
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Quality;

$QUALITY = new Quality();
$subject = $QUALITY->subject_view();
$row = $QUALITY->quality_view([$uuid]);
$items = $QUALITY->item_view([$uuid]);

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

  <!-- Header Section -->
  <table>
    <tr>
      <td class="text-left no-border"></td>
      <td class="text-center no-border" width="80%">
        <h2>ใบตรวจสอบคุณภาพ</h2>
      </td>
      <td class="text-right no-border"></td>
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
      <td class="no-border" width="20%">วันที่คัดมะขาม</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%">วันที่</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['created'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
    </tr>
    <tr>
      <td class="no-border" width="20%">วันที่รับเข้า</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['receive'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%"></td>
      <td class="no-border" width="30%"></td>
    </tr>
    <tr>
      <td class="no-border" width="20%">วัตถุดิบ</td>
      <td class="bottom-border" width="30%">
        <?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?>
      </td>
      <td class="no-border" width="20%"></td>
      <td class="no-border" width="30%"></td>
    </tr>
  </table>

  <!-- Detailed Table Section -->
  <table style="margin-top: 20px;">
    <tr>
      <th rowspan="2">#</th>
      <th rowspan="2">นน.ก่อนคัด<br> (kg)</th>
      <th rowspan="2">ผู้คัด</th>
      <th rowspan="2">ที่มาวัตถุดิบ</th>
      <?php foreach ($subject as $sub): ?>
        <th colspan="2"><?php echo htmlspecialchars($sub['name'], ENT_QUOTES, 'UTF-8'); ?></th>
      <?php endforeach; ?>
      <th rowspan="2">คลุก</th>
      <th rowspan="2">น้ำหนักรวม<br>ทั้งหมด (kg)</th>
      <th rowspan="2">%Yield<br>รวม</th>
    </tr>
    <tr>
      <?php foreach ($subject as $sub): ?>
        <th>kg</th>
        <th>%Yield</th>
      <?php endforeach; ?>
    </tr>
    <?php foreach ($items as $key => $item): ?>
      <?php
      $key++;
      $quantity = explode(",", $item['quantity']);
      $total = 0;
      ?>
      <tr>
        <td class="text-center"><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-right"><?php echo htmlspecialchars($item['start'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($item['user'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-left"><?php echo htmlspecialchars($item['supplier_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <?php foreach ($quantity as $qty): ?>
          <?php
          $yield = ($qty * 100) / $item['start'];
          $yield = round($yield, 2);
          $total += $qty;
          ?>
          <td class="text-right"><?php echo htmlspecialchars($qty, ENT_QUOTES, 'UTF-8'); ?></td>
          <td class="text-right"><?php echo htmlspecialchars($yield, ENT_QUOTES, 'UTF-8'); ?></td>
        <?php endforeach; ?>
        <?php
        $kg_total = $total + $item['end'];
        $yield_total = ($kg_total * 100) / $item['start'];
        $yield_total = round($yield_total, 2);
        ?>
        <td class="text-right"><?php echo htmlspecialchars($item['end'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-right"><?php echo htmlspecialchars($kg_total, ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-right"><?php echo htmlspecialchars($yield_total, ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
    <?php endforeach; ?>
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

</body>

</html>
<?php
// รับข้อมูล HTML และล้าง buffer
$html = ob_get_contents();
ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'default_font' => 'garuda']);
$mpdf->WriteHTML($html);
$date = date('Ymd');
$mpdf->Output("{$date}_quality.pdf", 'I');
