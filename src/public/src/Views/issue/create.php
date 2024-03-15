<?php
$menu = "service";
$page = "service-issue";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">เพิ่ม</h4>
      </div>
      <div class="card-body">
        <form action="/issue/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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
            <label class="col-xl-2 offset-xl-2 col-form-label">ประเภท</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-3">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="type" value="1" required>
                    <span class="text-success">นำเข้า</span>
                  </label>
                </div>
                <div class="col-xl-3">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="type" value="2" required>
                    <span class="text-danger">เบิกออก</span>
                  </label>
                </div>
              </div>
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
            <div class="col-sm-10">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <thead>
                    <tr>
                      <th width="10%">#</th>
                      <th width="50%">วัตถุดิบ</th>
                      <th width="20%">ปริมาณ (คงเหลือ)</th>
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
                        <select class="form-control form-control-sm item-select" name="product_id[]" required></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td class="text-center"><span class="item-remain"></span></td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center item-quantity" name="product_quantity[]" min="0" step="0.01" required>
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
    $(".item-select").select2('destroy');
    let row = $(".item-tr:last");
    let clone = row.clone();
    clone.find("input, select, span").val("").empty();
    clone.find(".item-increase").hide();
    clone.find(".item-decrease").show();
    clone.find(".item-decrease").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();

    let type = parseInt($("input[name='type']:checked").val());

    if (type === 1) {
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
    } else {
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
    }
  });

  $(document).on("click", "input[name='type']", function() {
    let type = parseInt($(this).val());
    $(".item-quantity").val("");

    if (type === 1) {
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
    } else {
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
    }
  });

  $(document).on("change", ".item-select", function() {
    let item = $(this).val();
    let type = parseInt($("input[name='type']:checked").val());
    let row = $(this).closest("tr");

    axios.post("/issue/item-detail", {
        item: item
      })
      .then((res) => {
        let result = res.data;
        row.find(".item-remain").text(parseFloat(result.issue_remain).toLocaleString("en-US", {
          minimumFractionDigits: 2
        }));
        row.find(".item-unit").text(result.unit_name);
        if (type === 2) {
          row.find(".item-quantity").prop("max", result.issue_remain);
        }
      }).catch((error) => {
        console.log(error);
      });
  });
</script>