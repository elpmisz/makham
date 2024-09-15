<?php
$menu = "service";
$page = "service-purchase";
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Quality;

$QUALITY = new Quality();
$subject = $QUALITY->subject_view();
$row = $QUALITY->quality_view([$uuid]);
$items = $QUALITY->item_view([$uuid]);

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
      <td class="text-left no-border"></td>
      <td class="text-center no-border" width="80%">
        <h2>ใบสรุปของเสีย</h2>
      </td>
      <td class="text-right no-border"></td>
    </tr>
  </table>

  <table>
    <tr>
      <td class="no-border" width="20%">
        ผู้ทำรายการ
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['fullname'] ?>
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
        วันที่คัดมะขาม
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['date'] ?>
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
        วันที่รับเข้า
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['receive'] ?>
      </td>
      <td class="no-border" width="20%"></td>
      <td class="no-border" width="30%"></td>
    </tr>
    <tr>
      <td class="no-border" width="20%">
        วัตถุดิบ
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['product_name'] ?>
      </td>
      <td class="no-border" width="20%"></td>
      <td class="no-border" width="30%"></td>
    </tr>
  </table>

  <table style="margin-top: 20px;">
    <tr>
      <th rowspan="2">#</th>
      <th rowspan="2">นน.ก่อนคัด<br> (kg)</th>
      <th rowspan="2">ผู้คัด</th>
      <th rowspan="2">ที่มาวัตถุดิบ</th>
      <?php
      foreach ($subject as $sub) :
      ?>
        <th colspan="2"><?php echo $sub['name'] ?></th>
      <?php endforeach; ?>
      <th rowspan="2">คลุก</th>
      <th rowspan="2">น้ำหนักรวม<br>ทั้งหมด (kg)</th>
      <th rowspan="2">%Yield<br>รวม</th>
    </tr>
    <tr>
      <?php
      foreach ($subject as $sub) :
      ?>
        <th>kg</th>
        <th>%Yield</th>
      <?php endforeach; ?>
    </tr>
    <?php
    foreach ($items as $key => $item) :
      $key++;
      $quantity = explode(",", $item['quantity']);
    ?>
      <tr>
        <td class="text-center"><?php echo $key ?></td>
        <td class="text-right"><?php echo $item['start'] ?></td>
        <td class="text-center"><?php echo $item['user'] ?></td>
        <td class="text-left"><?php echo $item['supplier_name'] ?></td>
        <?php
        $total = 0;
        foreach ($quantity as $qty) {
          $yield = (($qty * 100) / $item['start']);
          $yield = (!empty($yield) ? round($yield, 2) : 0);
          $total += $qty;
          echo "<td class='text-right'>{$qty}</td> <td class='text-right'>{$yield}</td>";
        }
        $kg_total = ($total + $item['end']);
        $yield_total = (($kg_total * 100) / $item['start']);
        $yield_total = (!empty($yield_total) ? round($yield_total, 2) : "");
        ?>
        <td class="text-right"><?php echo $item['end'] ?></td>
        <td class="text-right"><?php echo $kg_total  ?></td>
        <td class="text-right"><?php echo $yield_total  ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <table>
    <tr>
      <td class="no-border" width="20%">
        รายละเอียด
      </td>
      <td class="bottom-border" width="80%">
        <?php echo str_replace("\n", "<br>", $row['text']) ?>
      </td>
    </tr>
  </table>

</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'default_font' => 'garuda']);
$mpdf->WriteHTML($html);
$date = date('Ymd');
$mpdf->Output("{$date}_waste.pdf", 'I');
