<?php
$menu = "setting";
$page = "setting-supplier";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Supplier;

$SUPPLIER = new Supplier();

$row = $SUPPLIER->supplier_view([$uuid]);
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$customer_name = (!empty($row['customer_name']) ? $row['customer_name'] : "");
$vat = (!empty($row['vat']) ? $row['vat'] : "");
$email = (!empty($row['email']) ? $row['email'] : "");
$contact = (!empty($row['contact']) ? $row['contact'] : "");
$address = (!empty($row['address']) ? $row['address'] : "");
$sub_name = (!empty($row['sub_name']) ? $row['sub_name'] : "");
$district_name = (!empty($row['district_name']) ? $row['district_name'] : "");
$province_name = (!empty($row['province_name']) ? $row['province_name'] : "");
$postal = (!empty($row['postal']) ? $row['postal'] : "");
$latitude = (!empty($row['latitude']) ? $row['latitude'] : "");
$longitude = (!empty($row['longitude']) ? $row['longitude'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");
$subcode = (!empty($row['subcode']) ? $row['subcode'] : "");
$subname = (!empty($row['subname']) ? $row['subname'] : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">
        <form action="/supplier/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-2 col-form-label">UUID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $uuid ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ชื่อ</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="name" value="<?php echo $customer_name ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ติดต่อ</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $contact ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">เลขผู้เสียภาษี</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="vat" value="<?php echo $vat ?>">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">อีเมล</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="email" value="<?php echo $email ?>">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">ที่อยู่</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="address" rows="5"><?php echo $address ?></textarea>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">ค้นหา</label>
            <div class="col-xl-6">
              <select class="form-control form-control-sm address-select" name="sub" required>
                <?php echo "<option value='{$subcode}' selected>{$subname}</option>"; ?>
              </select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ตำบล</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm sub-name" value="<?php echo $sub_name ?>" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">อำเภอ</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm district-name" value="<?php echo $district_name ?>" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ละติจูด</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="latitude" value="<?php echo $latitude ?>">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">จังหวัด</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm province-name" value="<?php echo $province_name ?>" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">รหัสไปรษณีย์</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm postal" value="<?php echo $postal ?>" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ลองจิจูด</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="longitude" value="<?php echo $longitude ?>">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">รายละเอียดเพิ่มเติม</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5"><?php echo $text ?></textarea>
            </div>
          </div>
          <?php if (!empty($latitude) && !empty($longitude)) : ?>
            <div class="row mb-2">
              <label class="col-xl-2 col-form-label">แผนที่</label>
              <div class="col-xl-7">
                <iframe width="600" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo $latitude . "," . $longitude ?>&hl=es;z=8&amp;output=embed"></iframe>
              </div>
            </div>
          <?php endif; ?>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">สถานะ</label>
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
              <a href="/supplier" class="btn btn-sm btn-danger btn-block">
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
  $(".address-select").select2({
    placeholder: "-- ค้นหาจาก ตำบล อำเภอ จังหวัด หรือรหัสไปรษณีย์ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/supplier/address-select",
      method: "POST",
      dataType: "json",
      delay: 100,
      processResults: function(data) {
        return {
          results: data
        };
      },
      cache: true
    }
  });

  $(document).on("change", ".address-select", function() {
    let sub = $(this).val();

    if (sub) {
      axios.post("/supplier/address-view", {
          sub: sub,
        })
        .then(function(res) {
          let result = res.data;
          $(".sub-name").val(result.sub_name);
          $(".district-name").val(result.district_name);
          $(".province-name").val(result.province_name);
          $(".postal").val(result.postal);
        }).catch(function(error) {
          console.log(error);
        });
    } else {
      $(".sub-name, .district-name, .province-name, .postal").val("");
    }
  });
</script>