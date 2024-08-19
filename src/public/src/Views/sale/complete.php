<?php
$menu = "service";
$page = "service-sale";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Sale;

$SALE = new Sale();

$row = $SALE->sale_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$fullname = (!empty($row['fullname']) ? $row['fullname'] : "");
$customer = (!empty($row['customer']) ? $row['customer'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$text = (!empty($row['text']) ? str_replace("\n", "<br>", $row['text']) : "");
$promotion = (!empty($row['promotion']) ? $row['promotion'] : "");
$promotion_name = (!empty($row['promotion_name']) ? $row['promotion_name'] : "");
$promotion_type = (!empty($row['promotion_type']) ? $row['promotion_type'] : "");
$discount = (!empty($row['discount']) ? $row['discount'] : 0);
$vat = (!empty($row['vat']) ? "{$row['vat']} %" : "");
$amount = (!empty($row['amount']) ? $row['amount'] : 0);
$discount_amount = (!empty($row['discount_amount']) ? $row['discount_amount'] : 0);
$discount_total = (!empty($row['discount_total']) ? $row['discount_total'] : 0);
$vat_total = (!empty($row['vat_total']) ? $row['vat_total'] : 0);
$sale_total = (!empty($row['sale_total']) ? $row['sale_total'] : 0);
$created = (!empty($row['created']) ? $row['created'] : "");

$items = $SALE->item_view([$uuid]);
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">
        <form action="/sale/edit" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-1 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">เลขที่ใบ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $ticket ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ผู้ทำรายการ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $fullname . " - " . $created ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ลูกค้า</label>
            <div class="col-xl-6 text-underline">
              <?php echo $customer ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">รายละเอียด</label>
            <div class="col-xl-6 text-underline">
              <?php echo $text ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ส่งเสริมการขาย</label>
            <div class="col-xl-4 text-underline">
              <?php echo $promotion_name ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ภาษีมูลค่าเพิ่ม</label>
            <div class="col-xl-2 text-underline">
              <?php echo $vat ?>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-10">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr>
                      <th width="10%">#</th>
                      <th width="45%">สินค้า</th>
                      <th width="10%">หน่วยนับ</th>
                      <th width="10%">ราคาขาย</th>
                      <th width="10%">ปริมาณ (ขาย)</th>
                      <th width="15%">รวม</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $key => $item) : $key++ ?>
                      <tr>
                        <td class="text-center"><?php echo $key ?></td>
                        <td><?php echo "[{$item['product_code']}] {$item['product_name']}" ?></td>
                        <td class="text-center"><?php echo $item['unit_name'] ?></td>
                        <td class="text-center"><?php echo number_format($item['price'], 2) ?></td>
                        <td class="text-center"><?php echo number_format($item['amount'], 2) ?></td>
                        <td class="text-right"><?php echo number_format($item['total'], 2) ?></td>
                      </tr>
                    <?php endforeach ?>
                    <tr>
                      <td class="text-right h6" colspan="5">รวมเป็นเงิน</td>
                      <td class="text-right h6">
                        <span class="total-result"><?php echo number_format($amount, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="5">ส่วนลด</td>
                      <td class="text-right h6">
                        <span class="result-discount"><?php echo number_format($discount_amount, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="5">ยอดรวมหลังหักส่วนลด</td>
                      <td class="text-right h6">
                        <span class="total-all"><?php echo number_format($sale_total, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="5">ภาษีมูลค่าเพิ่ม <?php echo $vat ?></span></td>
                      <td class="text-right h6">
                        <span class="total-vat"><?php echo number_format($vat_total, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="5">ราคาไม่รวมภาษีมูลค่าเพิ่ม</td>
                      <td class="text-right h6">
                        <span class="total-discount"><?php echo number_format($discount_total, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="5">จำนวนเงินรวมทั้งสิ้น</td>
                      <td class="text-right h6">
                        <span class="total-all h5"><?php echo number_format($sale_total, 2) ?></span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-xl-3 mb-2">
              <a href="/sale" class="btn btn-sm btn-danger btn-block">
                <i class="fa fa-arrow-left pr-2"></i>กลับ
              </a>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>