<?php
$menu = "service";
$page = "service-quality";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Quality;

$QUALITY = new Quality();
$subject = $QUALITY->subject_view();
$row = $QUALITY->quality_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$user_id = (!empty($row['user_id']) ? $row['user_id'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$date = (!empty($row['date']) ? $row['date'] : "");
$receive = (!empty($row['receive']) ? $row['receive'] : "");
$fullname = (!empty($row['firstname']) ? $row['firstname'] : "");
$product_id = (!empty($row['product_id']) ? $row['product_id'] : "");
$product_name = (!empty($row['product_name']) ? $row['product_name'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$created = (!empty($row['created']) ? $row['created'] : "");

$items = $QUALITY->item_view([$uuid]);
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
        <form action="/quality/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="id" value="<?php echo $id ?>" readonly>
            </div>
          </div>
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">UUID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $uuid ?>" readonly>
            </div>
          </div>
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">USER</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user_id ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">เลขที่ใบ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $ticket ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ผู้ทำรายการ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $fullname . " - " . $created ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">วันที่คัดมะขาม</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date" value="<?php echo $date ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">วันที่รับเข้า</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="receive" value="<?php echo $receive ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">วัตถุดิบ</label>
            <div class="col-xl-4">
              <select class="form-control form-control-sm product-select" name="product_id">
                <?php
                if (!empty($product_id)) {
                  echo "<option value='{$product_id}'>{$product_name}</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-1 col-form-label">TOTAL</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm subject-total" value="<?php echo COUNT($subject) ?>" readonly>
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
                    <?php
                    foreach ($items as $item) :
                      $quantity = explode(",", $item['quantity']);
                    ?>
                      <tr>
                        <td class="text-center">
                          <a href="javascript:void(0)" class="badge badge-danger font-weight-light item-delete" id="<?php echo $item['id'] ?>">ลบ</a>
                        </td>
                        <td class="text-center"><?php echo $item['start'] ?></td>
                        <td class="text-center"><?php echo $item['user'] ?></td>
                        <td class="text-center"><?php echo $item['supplier_name'] ?></td>
                        <?php
                        $total = 0;
                        foreach ($quantity as $qty) {
                          $yield = (($qty * 100) / $item['start']);
                          $yield = (!empty($yield) ? round($yield, 2) : "");
                          $total += $qty;
                          echo "<td class='text-center'>{$qty}</td> <td class='text-center'>{$yield}</td>";
                        }
                        $kg_total = ($total + $item['end']);
                        $yield_total = (($kg_total * 100) / $item['start']);
                        $yield_total = (!empty($yield_total) ? round($yield_total, 2) : "");
                        ?>
                        <td class="text-center"><?php echo $item['end'] ?></td>
                        <td class="text-center"><?php echo $kg_total  ?></td>
                        <td class="text-center"><?php echo $yield_total  ?></td>
                      </tr>
                    <?php endforeach; ?>
                    <tr class="item-tr">
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success item-increase">+</button>
                        <button type="button" class="btn btn-sm btn-danger item-decrease">-</button>
                      </td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center weight-start" name="item_start[]" min="0" step="0.01">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <input type="text" class="form-control form-control-sm text-left" name="item_user[]">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <select class="form-control form-control-sm sup-select" name="item_sup[]"></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <?php
                      foreach ($subject as $k => $sub) :
                      ?>
                        <td>
                          <input type="number" class="form-control form-control-sm text-center kg-<?php echo $sub['id'] ?>" name="item_quantity[<?php echo $k ?>][]" min="0" step="0.01">
                          <div class="invalid-feedback">
                            กรุณากรอกข้อมูล!
                          </div>
                        </td>
                        <td class="text-center">
                          <span class="yield-<?php echo $sub['id'] ?>"></span>
                        </td>
                      <?php endforeach; ?>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center kg-end" name="item_end[]" min="0" step="0.01">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td class="text-center">
                        <span class="weight-total"></span>
                      </td>
                      <td class="text-center">
                        <span class="yield-total"></span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">รายละเอียด</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5"><?php echo $text ?></textarea>
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
  $(".date-select").daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    //minDate: new Date(),
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

  function calculateYieldsAndTotals() {
    document.querySelectorAll('.item-tr').forEach(row => {
      const weightStart = parseFloat(row.querySelector('.weight-start').value) || 0;

      let weightTotal = 0;
      let yieldTotal = 0;

      const subject = parseInt(document.querySelector('.subject-total').value, 10) || 0;

      for (let i = 1; i <= subject; i++) {
        const kgField = row.querySelector(`.kg-${i}`);
        const yieldField = row.querySelector(`.yield-${i}`);

        if (kgField) {
          const kgValue = parseFloat(kgField.value) || 0;
          const yieldValue = weightStart ? (kgValue * 100 / weightStart).toFixed(2) : 0;

          if (yieldField) {
            yieldField.textContent = yieldValue;
          }

          weightTotal += kgValue;
          yieldTotal += kgValue;
        }
      }

      const kgEndField = row.querySelector('.kg-end');
      const kgEndValue = parseFloat(kgEndField.value) || 0;
      weightTotal += kgEndValue;

      const finalYieldTotal = weightStart ? ((yieldTotal + kgEndValue) * 100 / weightStart).toFixed(2) : 0;

      row.querySelector('.weight-total').textContent = weightTotal.toFixed(2);
      row.querySelector('.yield-total').textContent = finalYieldTotal;
    });
  }

  function bindInputEvents() {
    document.querySelectorAll('input').forEach(input => {
      input.addEventListener('input', calculateYieldsAndTotals);
    });
  }

  window.addEventListener('load', () => {
    calculateYieldsAndTotals();
    bindInputEvents();
  });

  document.querySelectorAll('.item-decrease').forEach(element => {
    element.style.display = 'none';
  });
  document.addEventListener('click', function(event) {
    if (event.target.classList.contains('item-increase')) {
      const row = document.querySelector('.item-tr:last-of-type');
      const clone = row.cloneNode(true);
      clone.querySelectorAll('input, select, span').forEach(element => {
        if (element.tagName === 'INPUT' || element.tagName === 'SELECT') {
          element.value = '';
        } else if (element.tagName === 'SPAN') {
          element.textContent = '';
        }
      });
      clone.querySelector('.item-increase').style.display = 'none';
      clone.querySelector('.item-decrease').style.display = 'inline';
      clone.querySelector('.item-decrease').addEventListener('click', function() {
        this.closest('tr').remove();
        calculateYieldsAndTotals();
      });

      row.parentNode.insertBefore(clone, row.nextSibling);
      bindInputEvents();
      calculateYieldsAndTotals();
      initializeSelect2($(".sup-select"), "-- ผู้จัดจำหน่าย --", "/quality/sup-select");
    }
  });

  initializeSelect2($(".sup-select"), "-- ผู้จัดจำหน่าย --", "/quality/sup-select");
  initializeSelect2($(".product-select"), "-- วัตถุดิบ --", "/quality/product-select");

  $(document).on("click", ".item-delete", function(e) {
    let id = $(this).prop("id");
    e.preventDefault();
    Swal.fire({
      title: "ยืนยันที่จะทำรายการ?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "ตกลง",
      cancelButtonText: "ปิด",
    }).then((result) => {
      if (result.value) {
        axios.post("/quality/item-delete", {
          id: id
        }).then((res) => {
          let result = res.data;
          if (result === 200) {
            location.reload()
          } else {
            location.reload()
          }
        }).catch((error) => {
          console.log(error);
        });
      } else {
        return false;
      }
    })
  });
</script>