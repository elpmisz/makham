<?php
$menu = "setting";
$page = "setting-store";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Store;

$STORE = new Store();

$row = $STORE->store_view([$uuid]);
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$room = (!empty($row['room']) ? $row['room'] : "");
$zone = (!empty($row['zone']) ? $row['zone'] : "");
$floor = (!empty($row['floor']) ? $row['floor'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">
        <form action="/store/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-2 col-form-label">UUID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $uuid ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">ห้อง</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="room" value="<?php echo $room ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">ชั้น</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="floor" value="<?php echo $floor ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">โซน</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="zone" value="<?php echo $zone ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">รายละเอียดเพิ่มเติม</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5"><?php echo $text ?></textarea>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">สถานะ</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-3">
                  <label class="form-check-label px-3">
                    <input class="form-check-input" type="radio" name="status" value="1" <?php echo $active ?> required>
                    <span class="text-success">ใช้งาน</span>
                  </label>
                </div>
                <div class="col-xl-3">
                  <label class="form-check-label px-3">
                    <input class="form-check-input" type="radio" name="status" value="2" <?php echo $inactive ?> required>
                    <span class="text-danger">ระงับการใช้งาน</span>
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
              <a href="/store" class="btn btn-sm btn-danger btn-block">
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