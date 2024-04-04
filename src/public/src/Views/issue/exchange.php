<?php
$menu = "service";
$page = "service-issue";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">โอนย้าย</h4>
      </div>
      <div class="card-body">
        <form action="/issue/exchange" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-2 col-form-label">TYPE</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="type" value="3" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-2 col-form-label">รายละเอียด</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr>
                      <th width="10%">#</th>
                      <th width="30%">วัตถุดิบ</th>
                      <th width="20%">สถานที่ (ต้นทาง)</th>
                      <th width="10%">ปริมาณ (คงเหลือ)</th>
                      <th width="20%">สถานที่ (ปลายทาง)</th>
                      <th width="10%">ปริมาณ (โอนย้าย)</th>
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
                        <select class="form-control form-control-sm location-select" name="item_send[]" required></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td class="text-center"><span class="item-remain"></span></td>
                      <td class="text-left">
                        <select class="form-control form-control-sm location-select" name="item_receive[]" required></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center item-quantity" name="item_quantity[]" min="0" step="0.01" required>
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td class="text-center"><span class="item-unit"></span></td>
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
              <a href="/issue" class="btn btn-sm btn-danger btn-block">
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
  $(".item-decrease").hide();
  $(document).on("click", ".item-increase", function() {
    $(".item-select, .location-select").select2('destroy');
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
        url: "/issue/item-remain-select",
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
            row.find(".item-quantity").prop("max", result.remain)
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
      url: "/issue/item-remain-select",
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
</script>