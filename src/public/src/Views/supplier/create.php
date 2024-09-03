<?php
$menu = "setting";
$page = "setting-supplier";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">เพิ่ม</h4>
      </div>
      <div class="card-body">
        <form action="/supplier/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2">
            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ชื่อ</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="name" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ติดต่อ</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="contact" required>
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
                  <input type="text" class="form-control form-control-sm" name="vat">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">อีเมล</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="email">
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
              <textarea class="form-control form-control-sm" name="address" rows="5"></textarea>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">ค้นหา</label>
            <div class="col-xl-6">
              <select class="form-control form-control-sm address-select" name="sub" required></select>
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
                  <input type="text" class="form-control form-control-sm sub-name" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">อำเภอ</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm district-name" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ละติจูด</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="latitude">
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
                  <input type="text" class="form-control form-control-sm province-name" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">รหัสไปรษณีย์</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm postal" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ลองจิจูด</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="longitude">
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
              <textarea class="form-control form-control-sm" name="text" rows="5"></textarea>
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