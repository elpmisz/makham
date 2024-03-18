<?php
$menu = "service";
$page = "service-purchase";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Purchase;

$PURCHASE = new Purchase();

$row = $PURCHASE->purchase_view([$uuid]);
$texts = $PURCHASE->text_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$fullname = (!empty($row['fullname']) ? $row['fullname'] : "");
$bom = (!empty($row['bom']) ? $row['bom'] : "");
$bom_name = (!empty($row['bom_name']) ? $row['bom_name'] : "");
$machine_id = (!empty($row['machine']) ? $row['machine'] : "");
$machine_name = (!empty($row['machine_name']) ? $row['machine_name'] : "");
$amount = (!empty($row['amount']) ? $row['amount'] : "");
$date = (!empty($row['date']) ? $row['date'] : "");
$text = (!empty($row['text']) ? str_replace("\n", "<br>", $row['text']) : "");
$type_name = (!empty($row['type_name']) ? $row['type_name'] : "");

$items = $PURCHASE->bom_item([$bom]);
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
            <label class="col-xl-2 offset-xl-2 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-2 col-form-label">ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="id" value="<?php echo $id ?>" readonly>
            </div>
          </div>
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-2 col-form-label">UUID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $uuid ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form label">สูตรการผลิต</label>
            <div class="col-xl-4 text-underline">
              <?php echo $bom_name ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">จำนวนที่ผลิต (เป้าหมาย)</label>
            <div class="col-xl-2 text-underline">
              <?php echo $amount ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">จำนวนที่ผลิต (จริง)</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center" name="confirm" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">เครื่องจักร</label>
            <div class="col-xl-3 text-underline">
              <?php echo $machine_name ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">วันที่ผลิต</label>
            <div class="col-xl-3 text-underline">
              <?php echo $date ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">วัตถุประสงค์</label>
            <div class="col-xl-6 text-underline">
              <?php echo $text ?>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-10">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr class="table-primary">
                      <th width="10%">#</th>
                      <th width="40%">วัตถุดิบ</th>
                      <th width="10%">ปริมาณ/หน่วย</th>
                      <th width="10%">ปริมาณ (ที่ใช้)</th>
                      <th width="10%">หน่วยนับ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $key => $item) :
                      $key++;
                      $purchase_used = ($item['product_used'] * $amount);
                    ?>
                      <tr>
                        <td class="text-center"><?php echo $key ?></td>
                        <td class="text-left"><?php echo $item['product_name'] ?></td>
                        <td class="text-right item-used"><?php echo number_format($item['product_used'], 2) ?></td>
                        <td class="text-right item-quantity"><?php echo number_format($purchase_used, 2) ?></td>
                        <td class="text-center"><?php echo $item['unit_name'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-10">
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <thead>
                    <tr class="table-warning">
                      <th width="10%">สถานะ</th>
                      <th width="10%">ผู้ดำเนินการ</th>
                      <th width="40%">รายละเอียด</th>
                      <th width="10%">วันที่</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($texts as $key => $txt) : ?>
                      <tr>
                        <td class="text-center">
                          <span class="badge badge-<?php echo $txt['status_color'] ?> font-weight-light">
                            <?php echo $txt['status_name'] ?>
                          </span>
                        </td>
                        <td><?php echo $txt['username'] ?></td>
                        <td><?php echo str_replace("\n", "<br>", $txt['text']) ?></td>
                        <td><?php echo $txt['created'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">ผลการดำเนินการ</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-3">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="3" required>
                    <span class="text-primary">กำลังผลิต</span>
                  </label>
                </div>
                <div class="col-xl-3">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="4" required>
                    <span class="text-success">ผลิตเสร็จเรียบร้อย</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2 text-div">
            <label class="col-xl-2 offset-xl-2 col-form-label">รายละเอียดเพิ่มเติม</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="remark" rows="4"></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
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