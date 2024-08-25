<?php
$menu = "service";
$page = "service-purchase";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Purchase;

$PURCHASE = new Purchase();
$row = $PURCHASE->purchase_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$fullname = (!empty($row['fullname']) ? $row['fullname'] : "");
$customer_id = (!empty($row['customer_id']) ? $row['customer_id'] : "");
$customer_name = (!empty($row['customer_name']) ? $row['customer_name'] : "");
$amount = (!empty($row['amount']) ? $row['amount'] : "");
$machine = (!empty($row['machine']) ? $row['machine'] : "");
$per = (!empty($row['per']) ? $row['per'] : "");
$produce = (!empty($row['produce']) ? $row['produce'] : "");
$delivery = (!empty($row['delivery']) ? $row['delivery'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$issue_uuid = (!empty($row['issue_uuid']) ? $row['issue_uuid'] : "");
$issue_ticket = (!empty($row['issue_ticket']) ? $row['issue_ticket'] : "");
$created = (!empty($row['created']) ? $row['created'] : "");

$items = $PURCHASE->purchase_item_view([$uuid]);
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">
        <form action="/purchase/process" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="id" value="<?php echo $id ?>" readonly>
            </div>
          </div>
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">UUID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $uuid ?>" readonly>
            </div>
          </div>

          <div class="row">
            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form label">รายชื่อลูกค้า</label>
                <div class="col-xl-8 text-underline">
                  <?php echo $customer_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">จำนวนที่ผลิต</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $amount ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">จำนวนตู้</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $machine ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ตู้ละ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $per ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่ใบเบิก</label>
                <div class="col-xl-6 text-underline">
                  <a href="/issue/complete/<?php echo $issue_uuid ?>" target="_blank"><?php echo $issue_ticket ?></a>
                </div>
              </div>
            </div>

            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่เอกสาร</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $ticket ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">วันที่</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $created ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ผู้ทำรายการ</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $fullname ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">วันที่ผลิต</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $produce ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">วันที่ส่งลูกค้า</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $delivery ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">วัตถุประสงค์</label>
            <div class="col-xl-6 text-underline">
              <?php echo str_replace("\n", "<br>", $text) ?>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-11">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr>
                      <th width="10%">#</th>
                      <th width="30%">วัตถุดิบ</th>
                      <th width="20%">สถานที่</th>
                      <th width="10%">ปริมาณ (เป้าหมาย)</th>
                      <th width="20%">ปริมาณ (ผลิต)</th>
                      <th width="10%">หน่วยนับ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $i => $item) : $i++ ?>
                      <tr>
                        <td class="text-center">
                          <?php echo $i ?>
                          <input type="hidden" class="form-control form-control-sm" name="item_id[]" value="<?php echo $item['id'] ?>">
                        </td>
                        <td><?php echo $item['product_name'] ?></td>
                        <td><?php echo $item['location_name'] ?></td>
                        <td class="text-center"><?php echo $item['quantity'] ?></td>
                        <td>
                          <input type="number" class="form-control form-control-sm text-center" name="item_confirm[]" min="0" step="1" required>
                          <div class="invalid-feedback">
                            กรุณากรอกข้อมูล!
                          </div>
                        </td>
                        <td class="text-center"><?php echo $item['unit_name'] ?></td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">สถานะ</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-4">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="3" required>
                    <span class="text-info">ดำเนินการเรียบร้อย</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-xl-3 mb-2">
              <button type="submit" class="btn btn-sm btn-success btn-block">
                <i class="fas fa-check pr-2"></i>ตกลง
              </button>
            </div>
            <div class="col-xl-3 mb-2">
              <a href="/purchase" class="btn btn-sm btn-danger btn-block">
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