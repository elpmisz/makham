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
        <h2>ใบสรุปของเสีย</h2>
      </td>
      <td class="text-right no-border" width="10%"></td>
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
        เลขที่ใบสั่งผลิต
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['purchase_ticket'] ?>
      </td>
      <td class="no-border" width="20%">
        วันที่
      </td>
      <td class="bottom-border" width="30%">
        <?php echo $row['created'] ?>
      </td>
    </tr>
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

  <h6>วัตถุดิบ</h6>
  <table>
    <tr>
      <th width="5%">#</th>
      <th width="30%">วัตถุดิบ</th>
      <th width="10%">ปริมาณ</th>
      <th width="40%">หมายเหตุ</th>
    </tr>
    <?php
    foreach ($items as $key => $item) :
      $key++;
    ?>
      <tr>
        <td class="text-center"><?php echo $key ?></td>
        <td><?php echo $item['item'] ?></td>
        <td class="text-center"><?php echo $item['quantity'] ?></td>
        <td><?php echo $item['remark'] ?></td>
      </tr>
    <?php
    endforeach;
    ?>
  </table>

  <h6>สิ่งแปลกปลอม</h6>
  <table>
    <tr>
      <th width="5%">#</th>
      <th width="30%">สิ่งแปลกปลอม</th>
      <th width="10%">ปริมาณ</th>
      <th width="40%">หมายเหตุ</th>
    </tr>
    <?php
    foreach ($wastes as $key => $waste) :
      $key++;
    ?>
      <tr>
        <td class="text-center"><?php echo $key ?></td>
        <td><?php echo $waste['item'] ?></td>
        <td class="text-center"><?php echo $waste['quantity'] ?></td>
        <td><?php echo $waste['remark'] ?></td>
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
$mpdf->Output("{$date}_waste.pdf", 'I');
