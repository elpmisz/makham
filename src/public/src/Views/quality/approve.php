<?php
$menu = "service";
$page = "service-quality";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Quality;

$QUALITY = new Quality();
$subject = $QUALITY->subject_view();
$row = $QUALITY->quality_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$user_id = (!empty($row['user_id']) ? $row['user_id'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$date = (!empty($row['date']) ? $row['date'] : "");
$receive = (!empty($row['receive']) ? $row['receive'] : "");
$fullname = (!empty($row['fullname']) ? $row['fullname'] : "");
$product_id = (!empty($row['product_id']) ? $row['product_id'] : "");
$product_name = (!empty($row['product_name']) ? $row['product_name'] : "");
$text = (!empty($row['text']) ? str_replace("\n", "<br>", $row['text']) : "");
$created = (!empty($row['created']) ? $row['created'] : "");

$items = $QUALITY->item_view([$uuid]);
?>
<style>
  .th-100 {
    min-width: 100px !important;
  }
</style>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบตรวจสอบคุณภาพ</h4>
      </div>
      <div class="card-body">
        <form action="/quality/approve" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">USER</label>
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
            <label class="col-xl-2 offset-xl-1 col-form-label">วันที่คัดมะขาม</label>
            <div class="col-xl-3 text-underline">
              <?php echo $date ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">วันที่รับเข้า</label>
            <div class="col-xl-3 text-underline">
              <?php echo $receive ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">วัตถุดิบ</label>
            <div class="col-xl-3 text-underline">
              <?php echo $product_name ?>
            </div>
          </div>
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-1 col-form-label">TOTAL</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm subject-total" value="<?php echo COUNT($subject) ?>" readonly>
            </div>
          </div>
          <div class="row justify-content-center mb-2">
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr>
                      <th width="10%" rowspan="2" class="th-100">#</th>
                      <th width="10%" rowspan="2" class="th-100">นน.ก่อนคัด<br> (kg)</th>
                      <th width="10%" rowspan="2" class="th-100">ผู้คัด</th>
                      <th width="10%" rowspan="2" class="th-100">ที่มาวัตถุดิบ</th>
                      <?php
                      foreach ($subject as $sub) :
                      ?>
                        <th width="10%" colspan="2"><?php echo $sub['name'] ?></th>
                      <?php endforeach; ?>
                      <th width="10%" rowspan="2" class="th-100">คลุก</th>
                      <th width="10%" rowspan="2" class="th-100">น้ำหนักรวม<br>ทั้งหมด (kg)</th>
                      <th width="10%" rowspan="2" class="th-100">%Yield รวม</th>
                    </tr>
                    <tr>
                      <?php
                      foreach ($subject as $sub) :
                      ?>
                        <th width="10%" class="th-100">kg</th>
                        <th width="10%" class="th-100">%Yield</th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($items as $key => $item) :
                      $key++;
                      $quantity = explode(",", $item['quantity']);
                    ?>
                      <tr>
                        <td class="text-center"><?php echo $key ?></td>
                        <td class="text-center"><?php echo $item['start'] ?></td>
                        <td class="text-center"><?php echo $item['user'] ?></td>
                        <td class="text-center"><?php echo $item['supplier_name'] ?></td>
                        <?php
                        $total = 0;
                        foreach ($quantity as $qty) {
                          $yield = (($qty * 100) / $item['start']);
                          $yield = (!empty($yield) ? round($yield, 2) : "");
                          $total += $qty;
                          echo "<td class='text-center'>{$qty}</td> <td class='text-center'>{$yield}</td>";
                        }
                        $kg_total = ($total + $item['end']);
                        $yield_total = (($kg_total * 100) / $item['start']);
                        $yield_total = (!empty($yield_total) ? round($yield_total, 2) : "");
                        ?>
                        <td class="text-center"><?php echo $item['end'] ?></td>
                        <td class="text-center"><?php echo $kg_total  ?></td>
                        <td class="text-center"><?php echo $yield_total  ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">รายละเอียด</label>
            <div class="col-xl-6 text-underline">
              <?php echo $text ?>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ผลการตรวจสอบ</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-3">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="2" required>
                    <span class="text-success">ผ่านการตรวจสอบ</span>
                  </label>
                </div>
                <div class="col-xl-4">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="3" required>
                    <span class="text-danger">ไม่ผ่านการตรวจสอบ</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2 text-div">
            <label class="col-xl-2 offset-xl-1 col-form-label">หมายเหตุ</label>
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
              <a href="/quality" class="btn btn-sm btn-danger btn-block">
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