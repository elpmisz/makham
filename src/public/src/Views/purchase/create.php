<?php
$menu = "service";
$page = "service-purchase";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">เพิ่ม</h4>
      </div>
      <div class="card-body">
        <form action="/purchase/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-2 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">ผู้ทำรายการ</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" value="<?php echo $user['fullname'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form label">สูตรการผลิต</label>
            <div class="col-xl-4">
              <select class="form-control form-control-sm bom-select" name="bom" required></select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">จำนวนที่ผลิต (เป้าหมาย)</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center item-amount" name="amount" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">เครื่องจักร</label>
            <div class="col-xl-4">
              <select class="form-control form-control-sm machine-select" name="machine" required></select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">วันที่ผลิต</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">วัตถุประสงค์</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>

          <div class="row mb-2 justify-content-center item-div">
            <div class="col-xl-10">
              <div class="table-responsive">
                <table class="table table-bordered item-table"></table>
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
  $(".bom-select").select2({
    placeholder: "-- สูตรการผลิต --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/purchase/bom-select",
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

  $(".machine-select").select2({
    placeholder: "-- เครื่องจักร --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/purchase/machine-select",
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

  $(document).on("change", ".bom-select", function() {
    $(".item-amount").val("");
    $(".item-div").hide();
  });

  $(document).on("blur", ".item-amount", function() {
    let bom = $(".bom-select").val();
    let amount = $(this).val();

    axios.post("/purchase/bom-item", {
        bom: bom
      })
      .then((res) => {
        let result = res.data;
        if (amount) {
          $(".item-div").show();
          let table = '';
          table += '<tr class="table-primary">';
          table += '<th width="10%">#</th>';
          table += '<th width="40%">วัตถุดิบ</th>';
          table += '<th width="10%">ปริมาณ/หน่วย</th>';
          table += '<th width="10%">ปริมาณ (ที่ใช้)</th>';
          table += '<th width="10%">ปริมาณ (คงเหลือ)</th>';
          table += '<th width="10%">หน่วยนับ</th>';
          table += '</tr>';
          result.forEach(function(v, k) {
            k++;
            let quantity = parseFloat(v.product_used);
            let remain = parseFloat(v.issue_remain);
            let color = (quantity <= remain ? "success" : "danger");
            let total = (amount * quantity);
            let unit = (v.unit_name ? v.unit_name : "");
            table += '<tr class="table-' + color + '">';
            table += '<td class="text-center">' + k + '</td>';
            table += '<td class="text-left"><input type="hidden" name="product_id[]" value="' + v.product_id + '">' + v.product_name + '</td>';
            table += '<td class="text-right">' + quantity.toLocaleString("en-US", {
              minimumFractionDigits: 2
            }) + '</td>';
            table += '<td class="text-right"><input type="hidden" name="product_quantity[]" value="' + total + '">' + total.toLocaleString("en-US", {
              minimumFractionDigits: 2
            }) + '</td>';
            table += '<td class="text-right">' + remain.toLocaleString("en-US", {
              minimumFractionDigits: 2
            }) + '</td>';
            table += '<td class="text-center">' + unit + '</td>';
            table += '</tr>';
          });
          $(".item-table").empty().html(table);
        } else {
          $(".item-div").hide();
        }
      }).catch((error) => {
        console.log(error);
      });
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
</script>