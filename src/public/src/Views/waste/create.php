<?php
$menu = "service";
$page = "service-waste";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบสรุปของเสีย</h4>
      </div>
      <div class="card-body">
        <form action="/waste/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
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
                      <td>
                        <input type="number" class="form-control form-control-sm text-center" name="item_quantity[]" min="0" step="0.01" required>
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <input type="text" class="form-control form-control-sm text-left" name="item_remark[]">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                    </tr>
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
                    <tr class="waste-tr">
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success waste-increase">+</button>
                        <button type="button" class="btn btn-sm btn-danger waste-decrease">-</button>
                      </td>
                      <td>
                        <input type="text" class="form-control form-control-sm text-left" name="waste_product[]">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center" name="waste_quantity[]" min="0" step="0.01">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <input type="text" class="form-control form-control-sm text-left" name="waste_remark[]">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียดเพิ่มเติม</label>
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
  $(".item-decrease, .waste-decrease").hide();
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

    $(".item-select").select2({
      placeholder: "-- วัตถุดิบ --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/bom/item-select",
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

  $(document).on("click", ".waste-increase", function() {
    let row = $(".waste-tr:last");
    let clone = row.clone();
    clone.find("input, select, span").val("").empty();
    clone.find(".waste-increase").hide();
    clone.find(".waste-decrease").show();
    clone.find(".waste-decrease").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();
  });

  $(".item-select").select2({
    placeholder: "-- วัตถุดิบ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/bom/item-select",
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