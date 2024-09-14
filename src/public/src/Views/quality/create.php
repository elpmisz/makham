<?php
$menu = "service";
$page = "service-quality";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Quality;

$QUALITY = new Quality();
$subject = $QUALITY->subject_view();
?>
<style>
  .th-100 {
    min-width: 100px !important;
  }
</style>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบตรวจสอบคุณภาพ</h4>
      </div>
      <div class="card-body">
        <form action="/waste/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-1 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">วันที่</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date" required>
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
                      <th width="10%" rowspan="2" class="th-100">#</th>
                      <th width="10%" rowspan="2" class="th-100">นน.ก่อนคัด<br> (kg)</th>
                      <th width="10%" rowspan="2" class="th-100">ผู้คัด</th>
                      <th width="10%" rowspan="2" class="th-100">ที่มาวัตถุดิบ</th>
                      <?php
                      foreach ($subject as $sub) :
                      ?>
                        <th width="10%" colspan="2"><?php echo $sub['name'] ?></th>
                      <?php endforeach; ?>
                      <th width="10%" rowspan="2" class="th-100">คลุก</th>
                      <th width="10%" rowspan="2" class="th-100">น้ำหนักรวม<br>ทั้งหมด (kg)</th>
                      <th width="10%" rowspan="2" class="th-100">%Yield รวม</th>
                    </tr>
                    <tr>
                      <?php
                      foreach ($subject as $sub) :
                      ?>
                        <th width="10%" class="th-100">kg</th>
                        <th width="10%" class="th-100">%Yield</th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="item-tr">
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success item-increase">+</button>
                        <button type="button" class="btn btn-sm btn-danger item-decrease">-</button>
                      </td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center weight-start" name="item_quantity[]" min="0" step="0.01" required>
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
                      <td>
                        <input type="text" class="form-control form-control-sm text-left" name="item_remark[]">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <?php
                      foreach ($subject as $sub) :
                      ?>
                        <td>
                          <input type="number" class="form-control form-control-sm text-center kg-<?php echo $sub['id'] ?>" name="item_quantity[]" min="0" step="0.01" required>
                          <div class="invalid-feedback">
                            กรุณากรอกข้อมูล!
                          </div>
                        </td>
                        <td>
                          <span class="yield-<?php echo $sub['id'] ?>"></span>
                        </td>
                      <?php endforeach; ?>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center kg-end" name="item_quantity[]" min="0" step="0.01" required>
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <span class="weight-total"></span>
                      </td>
                      <td>
                        <span class="yield-total"></span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียด</label>
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
              <a href="/quality" class="btn btn-sm btn-danger btn-block">
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