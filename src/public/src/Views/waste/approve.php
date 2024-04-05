<?php
$menu = "service";
$page = "service-waste";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Waste;

$WASTE = new Waste();

$row = $WASTE->waste_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$fullname = (!empty($row['fullname']) ? $row['fullname'] : "");
$text = (!empty($row['text']) ? str_replace("\n", "<br>", $row['text']) : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");

$items = $WASTE->item_view([$uuid, 1]);
$wastes = $WASTE->item_view([$uuid, 2]);
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบสรุปของเสีย</h4>
      </div>
      <div class="card-body">
        <form action="/waste/approve" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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

          <div class="row justify-content-center mb-2">
            <div class="col-sm-10">
              <h6>วัตถุดิบ</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr>
                      <th width="10%">#</th>
                      <th width="40%">วัตถุดิบ</th>
                      <th width="20%">ปริมาณ</th>
                      <th width="30%">หมายเหตุ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $key => $item) : $key++; ?>
                      <tr>
                        <td class="text-center"><?php echo $key ?></td>
                        <td class="text-left"><?php echo $item['item'] ?></td>
                        <td class="text-center"><?php echo $item['quantity'] ?></td>
                        <td class="text-left"><?php echo $item['remark'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <h6>สิ่งแปลกปลอม</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr>
                      <th width="10%">#</th>
                      <th width="40%">สิ่งแปลกปลอม</th>
                      <th width="20%">ปริมาณ</th>
                      <th width="30%">หมายเหตุ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($wastes as $key => $waste) : $key++; ?>
                      <tr>
                        <td class="text-center"><?php echo $key ?></td>
                        <td class="text-left"><?php echo $waste['item'] ?></td>
                        <td class="text-center"><?php echo $waste['quantity'] ?></td>
                        <td class="text-left"><?php echo $waste['remark'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียดเพิ่มเติม</label>
            <div class="col-xl-6 text-underline">
              <?php echo $text ?>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ผลการตรวจสอบ</label>
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
            <label class="col-xl-3 offset-xl-1 col-form-label">หมายเหตุ</label>
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
              <a href="/waste" class="btn btn-sm btn-danger btn-block">
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

</script>