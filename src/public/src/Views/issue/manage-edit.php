<?php
$menu = "service";
$page = "service-issue";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Issue;

$ISSUE = new Issue();

$row = $ISSUE->issue_view([$uuid]);
$items = (intval($row['type']) === 3 ? $ISSUE->exchange_view([$uuid]) : $ISSUE->item_view([$uuid]));
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$fullname = (!empty($row['firstname']) ? $row['firstname'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$type = (!empty($row['type']) ? $row['type'] : "");
$type_name = (!empty($row['type_name']) ? $row['type_name'] : "");
$type_color = (!empty($row['type_color']) ? $row['type_color'] : "");
$created = (!empty($row['created']) ? $row['created'] : "");
$status = (!empty($row['status']) ? $row['status'] : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบนำสินค้าเข้า - ออก</h4>
      </div>
      <div class="card-body">
        <form action="/issue/manage-update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
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
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่ใบ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $ticket ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ผู้ทำรายการ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $fullname . " - " . $created ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ประเภท</label>
            <div class="col-xl-4 text-underline text-<?php echo $type_color ?>">
              <?php echo $type_name ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียด</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required><?php echo $text ?></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-10">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <?php if ($type !== 3) : ?>
                    <thead>
                      <tr>
                        <th width="10%">#</th>
                        <th width="30%">วัตถุดิบ</th>
                        <th width="20%">สถานที่</th>
                        <th width="20%">ปริมาณ <?php echo "({$row['type_name']})" ?></th>
                        <th width="20%">ปริมาณ (ตรวจสอบ)</th>
                        <th width="10%">หน่วยนับ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $key => $item) : $key++; ?>
                        <tr>
                          <td class="text-center">
                            <?php echo $key ?>
                          </td>
                          <td><?php echo $item['product_name'] ?></td>
                          <td><?php echo $item['location_name'] ?></td>
                          <td class="text-right"><?php echo number_format($item['quantity'], 2) ?></td>
                          <td class="text-right"><?php echo number_format($item['confirm'], 2) ?></td>
                          <td class="text-center"><?php echo $item['unit_name'] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  <?php endif; ?>
                  <?php if ($type === 3) : ?>
                    <thead>
                      <tr>
                        <th width="10%">#</th>
                        <th width="30%">วัตถุดิบ</th>
                        <th width="20%">สถานที่ (ต้นทาง)</th>
                        <th width="20%">สถานที่ (ปลายทาง)</th>
                        <th width="10%">ปริมาณ (โอนย้าย)</th>
                        <th width="10%">ปริมาณ (ตรวจสอบ)</th>
                        <th width="10%">หน่วยนับ</th>
                      </tr>
                    </thead>
                    <?php foreach ($items as $key => $item) : $key++; ?>
                      <tr>
                        <td class="text-center">
                          <?php echo $key ?>
                        </td>
                        <td><?php echo $item['product_name'] ?></td>
                        <td><?php echo $item['send'] ?></td>
                        <td><?php echo $item['receive'] ?></td>
                        <td class="text-right"><?php echo number_format($item['quantity'], 2) ?></td>
                        <td class="text-right"><?php echo number_format($item['confirm'], 2) ?></td>
                        <td class="text-center"><?php echo $item['unit_name'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </table>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ผู้ดำเนินการ</label>
            <div class="col-xl-4 text-underline">
              <span class="text-primary"><?php echo $row['approver_firstname'] . " - " . $row['approved'] ?></span>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">สถานะ</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-3">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="2" <?php echo (intval($status) === 2 ? "checked" : "") ?> required>
                    <span class="text-success">ผ่านการตรวจสอบ</span>
                  </label>
                </div>
                <div class="col-xl-3">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="3" <?php echo (intval($status) === 3 ? "checked" : "") ?> required>
                    <span class="text-danger">ระงับการใช้งาน</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียดเพิ่มเติม</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="remark" rows="4" required></textarea>
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
              <a href="/issue/manage" class="btn btn-sm btn-danger btn-block">
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
<script>
  $(".text-div").hide();
  $(document).on("click", "input[name='status']:checked", function() {
    let status = parseInt($(this).val());
    if (status === 3) {
      $(".text-div").show();
      $("textarea[name='remark']").prop("required", true);
    } else {
      $(".text-div").hide();
      $("textarea[name='remark']").prop("required", false);
    }
  });
</script>