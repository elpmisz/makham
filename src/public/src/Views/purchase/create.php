<?php
$menu = "service";
$page = "service-purchase";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบสั่งผลิต</h4>
      </div>
      <div class="card-body">
        <form action="/purchase/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ผู้ทำรายการ</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" value="<?php echo $user['fullname'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form label">รายชื่อลูกค้า</label>
            <div class="col-xl-6">
              <select class="form-control form-control-sm customer-select" name="customer" required></select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">จำนวนที่ผลิต</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center" name="amount" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">จำนวนตู้</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center" name="machine" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ตู้ละ</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center" name="per" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">วันที่ผลิต</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date_produce" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">วันที่ส่งลูกค้า</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date_delivery" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">วัตถุประสงค์</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
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
                      <th width="10%">ปริมาณ (คงเหลือ)</th>
                      <th width="20%">ปริมาณ</th>
                      <th width="10%">หน่วยนับ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="item-tr">
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success item-increase">+</button>
                        <button type="button" class="btn btn-sm btn-danger item-decrease">-</button>
                      </td>
                      <td class="text-left">
                        <select class="form-control form-control-sm item-select" name="item_product[]" required></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td class="text-left">
                        <select class="form-control form-control-sm location-select" name="item_location[]" required></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td class="text-center"><span class="item-remain"></span></td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center item-quantity" name="item_quantity[]" min="0" step="0.01" required>
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td class="text-left">
                        <select class="form-control form-control-sm unit-select" name="item_unit[]" required></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
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
<script>
  $(".customer-select").select2({
    placeholder: "-- รายชื่อลูกค้า --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/purchase/customer-select",
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

  $(".date-select").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minDate: new Date(),
    locale: {
      "format": "DD/MM/YYYY",
      "daysOfWeek": [
        "อา", "จ", "อ", "พ", "พฤ", "ศ", "ส"
      ],
      "monthNames": [
        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
      ]
    },
    "applyButtonClasses": "btn-success",
    "cancelClass": "btn-danger"
  });

  $(".date-select").on("apply.daterangepicker", function(ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY'));
  });

  $(".date-select").on("keydown paste", function(e) {
    e.preventDefault();
  });

  $(".item-decrease").hide();
  $(document).on("click", ".item-increase", function() {
    $(".item-select, .location-select, .unit-select").select2('destroy');
    let row = $(".item-tr:last");
    let clone = row.clone();
    clone.find("input, select").val("").empty();
    clone.find("span").text("");
    clone.find(".item-increase").hide();
    clone.find(".item-decrease").show();
    clone.find(".item-decrease").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();

    $(".item-select").select2({
      placeholder: "-- วัตถุดิบ --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/issue/item-all-select",
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

    $(".location-select").select2({
      placeholder: "-- สถานที่ --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/issue/location-select",
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
        url: "/issue/unit-select",
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
  });

  $(document).on("change", ".item-select, .location-select", function() {
    $(".item-select").each(function() {
      let row = $(this).closest("tr");
      let item = row.find(".item-select").val();
      let location = row.find(".location-select").val();

      if (item && location) {
        axios.post("/issue/item-detail", {
            item: item,
            location: location,
          })
          .then((res) => {
            let result = res.data;
            row.find(".item-remain").text(parseFloat(result.remain).toLocaleString("en-US", {
              minimumFractionDigits: 2
            }));
            row.find(".item-unit").text(result.unit_name);
          }).catch((error) => {
            console.log(error);
          });
      }
    });
  });

  $(".item-select").select2({
    placeholder: "-- วัตถุดิบ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/issue/item-50-select",
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

  $(".location-select").select2({
    placeholder: "-- สถานที่ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/issue/location-select",
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
      url: "/issue/unit-select",
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
</script>