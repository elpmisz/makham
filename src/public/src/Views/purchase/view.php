<?php
$menu = "service";
$page = "service-purchase";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Purchase;

$PURCHASE = new Purchase();
$row = $PURCHASE->purchase_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$fullname = (!empty($row['fullname']) ? $row['fullname'] : "");
$customer_id = (!empty($row['customer_id']) ? $row['customer_id'] : "");
$customer_name = (!empty($row['customer_name']) ? $row['customer_name'] : "");
$amount = (!empty($row['amount']) ? $row['amount'] : "");
$machine = (!empty($row['machine']) ? $row['machine'] : "");
$per = (!empty($row['per']) ? $row['per'] : "");
$produce = (!empty($row['produce']) ? $row['produce'] : "");
$delivery = (!empty($row['delivery']) ? $row['delivery'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$created = (!empty($row['created']) ? $row['created'] : "");

$items = $PURCHASE->purchase_item_view([$uuid]);
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">
        <form action="/purchase/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่เอกสาร</label>
            <div class="col-xl-4 text-underline">
              <?php echo $ticket ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ผู้ทำรายการ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $fullname ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form label">รายชื่อลูกค้า</label>
            <div class="col-xl-6">
              <select class="form-control form-control-sm customer-select" name="customer" required>
                <?php
                if (!empty($customer_id)) {
                  echo "<option value='{$customer_id}'>{$customer_name}</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">จำนวนที่ผลิต</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center" name="amount" value="<?php echo $amount ?>" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">จำนวนตู้</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center" name="machine" value="<?php echo $machine ?>" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ตู้ละ</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center" name="per" value="<?php echo $per ?>" min="0" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">วันที่ผลิต</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date_produce" value="<?php echo $produce ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">วันที่ส่งลูกค้า</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date_delivery" value="<?php echo $delivery ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">วัตถุประสงค์</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required><?php echo $text ?></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่ใบเบิก</label>
            <div class="col-xl-4">
              <select class="form-control form-control-sm issue-select" name="issue_id" required></select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
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
                      <th width="20%">วัตถุดิบ</th>
                      <th width="20%">คลัง</th>
                      <th width="20%">ห้อง</th>
                      <th width="10%">ปริมาณ (คงเหลือ)</th>
                      <th width="10%">ปริมาณ (เป้าหมาย)</th>
                      <th width="10%">หน่วยนับ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $item) : ?>
                      <tr>
                        <td class="text-center">
                          <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='<?php echo $item['id'] ?>'>ลบ</a>
                        </td>
                        <td><?php echo $item['product_name'] ?></td>
                        <td><?php echo $item['location_name'] ?></td>
                        <td><?php echo $item['store_name'] ?></td>
                        <td></td>
                        <td class="text-center"><?php echo $item['quantity'] ?></td>
                        <td class="text-center"><?php echo $item['unit_name'] ?></td>
                      </tr>
                    <?php endforeach ?>
                    <tr class="item-tr">
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success item-increase">+</button>
                        <button type="button" class="btn btn-sm btn-danger item-decrease">-</button>
                      </td>
                      <td class="text-left">
                        <select class="form-control form-control-sm item-select" name="item_product[]"></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td class="text-left">
                        <select class="form-control form-control-sm location-select" name="item_location[]"></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td class="text-left">
                        <select class="form-control form-control-sm store-select" name="item_store[]"></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td class="text-center"><span class="item-remain"></span></td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center item-quantity" name="item_quantity[]" min="0" step="0.01">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td class="text-left">
                        <select class="form-control form-control-sm unit-select" name="item_unit[]"></select>
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

          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">สถานะ</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-4">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="1" required>
                    <span class="text-info">รอเบิกวัตถุดิบ</span>
                  </label>
                </div>
                <div class="col-xl-4">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="status" value="2" required>
                    <span class="text-primary">ดำเนินการผลิต</span>
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
              <a href="/purchase" class="btn btn-sm btn-danger btn-block">
                <i class="fa fa-arrow-left pr-2"></i>กลับ
              </a>
            </div>
            <div class="col-xl-3 mb-2">
              <a href="/purchase/print/<?php echo $uuid ?>" class="btn btn-sm btn-primary btn-block">
                <i class="fa fa-print pr-2"></i>พิมพ์
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
  $(document).on("click", "input[name='status']", function() {
    let status = ($(this).val() ? parseInt($(this).val()) : "");
    if (status === 2) {
      $(".issue-select").prop("required", false);
    } else {
      $(".issue-select").prop("required", true);
    }
  });

  $("form").on("submit", function(event) {
    $(".store-select").each(function() {
      if ($(this).val() === null || $(this).val() === "") {
        $(this).after('<input type="hidden" name="' + $(this).prop('name') + '" value="0">');
      }
    });
  });

  $(".item-decrease").hide();
  $(document).on("click", ".item-increase", function() {
    let row = $(".item-tr:last");
    let clone = row.clone();
    clone.find("input, select").val("");
    clone.find("span").text("");
    clone.find(".item-increase").hide();
    clone.find(".item-decrease").show();
    clone.find(".item-decrease").off("click").on("click", function() {
      $(this).closest("tr").remove();
    });

    row.after(clone);

    clone.find(".store-select").val("0");

    initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/issue/item-50-select");
    initializeSelect2($(".location-select"), "-- คลัง --", "/issue/location-select");
    initializeSelect2($(".store-select"), "-- ห้อง --", "/issue/store-select");
    initializeSelect2($(".unit-select"), "-- หน่วยนับ --", "/issue/unit-select");
  });

  initializeSelect2($(".customer-select"), "-- รายชื่อลูกค้า --", "/purchase/customer-select");
  initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/issue/item-50-select");
  initializeSelect2($(".location-select"), "-- คลัง --", "/issue/location-select");
  initializeSelect2($(".store-select"), "-- ห้อง --", "/issue/store-select");
  initializeSelect2($(".unit-select"), "-- หน่วยนับ --", "/issue/unit-select");
  initializeSelect2($(".issue-select"), "-- ใบเบิกวัตถุดิบ --", "/issue/issue-select");

  $(document).on("change", ".item-select, .location-select, .store-select", function() {
    $(".unit-select").empty();
    $(".item-select").each(function() {
      let row = $(this).closest("tr");
      let item = row.find(".item-select").val();
      let location = row.find(".location-select").val();
      let store = row.find(".store-select").val();

      if (item && location) {
        axios.post("/issue/item-detail", {
            item: item,
            location: location,
            store: store,
          })
          .then((res) => {
            let result = res.data;
            row.find(".item-remain").text(parseFloat(result.remain).toLocaleString("en-US", {
              minimumFractionDigits: 2
            }));

            let selected = new Option(result.unit_name, result.unit, true, true);
            row.find(".unit-select").append(selected).trigger("change");

          }).catch((error) => {
            console.log(error);
          });
      }
    });
  });

  $(document).on("click", ".btn-delete", function(e) {
    let id = ($(this).prop("id") ? $(this).prop("id") : "");

    e.preventDefault();
    Swal.fire({
      title: "ยืนยันที่จะทำรายการ?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "ยืนยัน",
      cancelButtonText: "ปิด",
    }).then((result) => {
      if (result.value) {
        axios.post("/purchase/item-delete", {
          id: id
        }).then((res) => {
          let result = res.data;
          if (parseInt(result) === 200) {
            Swal.fire({
              title: "ดำเนินการเรียบร้อย!",
              icon: "success"
            }).then((result) => {
              if (result.value) {
                location.reload();
              } else {
                return false;
              }
            })
          } else {
            location.reload();
          }
        }).catch((error) => {
          console.log(error);
        });
      } else {
        return false;
      }
    })
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