<?php
$menu = "setting";
$page = "setting-product";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">เพิ่ม</h4>
      </div>
      <div class="card-body">
        <form action="/product/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">รูปวัตถุดิบ / สินค้า</label>
            <div class="col-xl-6">
              <table class="table table-borderless">
                <tr class="tr-file">
                  <td class="text-center" width="5%">
                    <button type="button" class="btn btn-sm btn-success increase-file">+</button>
                    <button type="button" class="btn btn-sm btn-danger decrease-file">-</button>
                  </td>
                  <td>
                    <input type="file" class="form-control-file" name="file[]" accept=".jpeg, .png, .jpg">
                  </td>
                </tr>
              </table>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">รหัสสินค้า</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="code" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ชื่อสินค้า</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="name" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ราคาซื้อ</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="cost" step="0.01" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ราคาขาย</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="price" step="0.01" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">MIN</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="min" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">MAX</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="max" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">หน่วยนับ</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm unit-select" name="unit" required></select>
                  <div class="invalid-feedback">
                    กรุณาเลือกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">กิโลกรัม/หน่วย</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-center" name="per" min="0" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ยี่ห้อ</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm brand-select" name="brand"></select>
                  <div class="invalid-feedback">
                    กรุณาเลือกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ผู้จัดจำหน่าย</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm supplier-select" name="supplier"></select>
                  <div class="invalid-feedback">
                    กรุณาเลือกข้อมูล!
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
              <a href="/product" class="btn btn-sm btn-danger btn-block">
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
  $(".bom-select").select2({
    placeholder: "-- สูตรการผลิต --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/bom-select",
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

  $(".supplier-select").select2({
    placeholder: "-- ผู้จัดจำหน่าย --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/supplier-select",
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

  $(".unit-select").select2({
    placeholder: "-- หน่วยนับ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/unit-select",
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

  $(".brand-select").select2({
    placeholder: "-- ยี่ห้อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/brand-select",
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

  $(".category-select").select2({
    placeholder: "-- หมวดหมู่ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/category-select",
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

  $(".store-select").select2({
    placeholder: "-- สถานที่จัดเก็บ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/store-select",
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

  $(".decrease-file").hide();
  $(document).on("click", ".increase-file", function() {
    let row = $(".tr-file:last");
    let clone = row.clone();
    clone.find("input").val("");
    clone.find(".increase-file").hide();
    clone.find(".decrease-file").show();
    clone.find(".decrease-file").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();
  });

  $(document).on("change", "input[name='file[]']", function() {
    let file = $(this).val();
    let size = ($(this)[0].files[0].size / (1024 * 1024)).toFixed(2);
    let extension = file.split(".").pop().toLowerCase();
    let allow = ["png", "jpeg", "jpg"];
    if (size > 5) {
      Swal.fire({
        icon: "error",
        title: "ขนาดรูปไม่เกิน 5MB!",
      })
      $(this).val("");
    }

    if ($.inArray(extension, allow) === -1) {
      Swal.fire({
        icon: "error",
        title: "เฉพาะไฟล์รูป PNG JPG or JPEG",
      })
      $(this).val("");
    }
  });
</script>