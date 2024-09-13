<?php
$menu = "service";
$page = "service-issue";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Issue;

$ISSUE = new Issue();

$row = $ISSUE->issue_view([$uuid]);
$items = (intval($row['type']) === 3 ? $ISSUE->exchange_view($uuid) : $ISSUE->item_view([$uuid]));
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$fullname = (!empty($row['firstname']) ? $row['firstname'] : "");
$date = (!empty($row['date']) ? $row['date'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$type = (!empty($row['type']) ? $row['type'] : "");
$type_name = (!empty($row['type_name']) ? $row['type_name'] : "");
$type_color = (!empty($row['type_color']) ? $row['type_color'] : "");
$group = (!empty($row['group']) ? $row['group'] : "");
$created = (!empty($row['created']) ? $row['created'] : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center"><?php echo "ใบ{$type_name}สินค้า" ?></h4>
      </div>
      <div class="card-body">
        <form action="/issue/<?php echo ($type === 3 ? "update-ex" : "update") ?>" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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
            <label class="col-xl-3 offset-xl-1 col-form-label">TYPE</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="type" value="<?php echo $type ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่ใบ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $ticket ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ผู้ทำรายการ</label>
            <div class="col-xl-4 text-underline">
              <?php echo $fullname . " - " . $created ?>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">ประเภท</label>
            <div class="col-xl-4 text-underline text-<?php echo $type_color ?>">
              <?php echo $type_name ?>
            </div>
          </div>
          <?php if (intval($type === 2)) : ?>
            <div class="row mb-2">
              <label class="col-xl-3 offset-xl-1 col-form-label">เพื่อ</label>
              <div class="col-xl-8">
                <div class="row pb-2">
                  <div class="col-xl-3">
                    <label class="form-check-label px-3 py-2">
                      <input class="form-check-input" type="radio" name="group" value="1" <?php echo (intval($group) === 1 ? "checked" : "") ?> required>
                      <span class="text-info">สั่งผลิต</span>
                    </label>
                  </div>
                  <div class="col-xl-3">
                    <label class="form-check-label px-3 py-2">
                      <input class="form-check-input" type="radio" name="group" value="2" <?php echo (intval($group) === 2 ? "checked" : "") ?> required>
                      <span class="text-primary">รอผลิต</span>
                    </label>
                  </div>
                  <div class="col-xl-3">
                    <label class="form-check-label px-3 py-2">
                      <input class="form-check-input" type="radio" name="group" value="3" <?php echo (intval($group) === 3 ? "checked" : "") ?> required>
                      <span class="text-success">ขาย</span>
                    </label>
                  </div>
                  <div class="col-xl-3">
                    <label class="form-check-label px-3 py-2">
                      <input class="form-check-input" type="radio" name="group" value="4" <?php echo (intval($group) === 4 ? "checked" : "") ?> required>
                      <span class="text-danger">อื่นๆ</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          <?php endif ?>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">วันที่</label>
            <div class="col-xl-3">
              <input type="text" class="form-control form-control-sm date-select" name="date" value="<?php echo $date ?>" required>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียด</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required><?php echo $text ?></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-12">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <?php if ($type !== 3) : ?>
                    <thead>
                      <tr>
                        <th width="10%">#</th>
                        <th width="20%">วัตถุดิบ</th>
                        <th width="20%">คลัง</th>
                        <th width="20%">ห้อง</th>
                        <th width="10%">ปริมาณ (รอตรวจสอบ)</th>
                        <th width="10%">ปริมาณ (คงเหลือ)</th>
                        <th width="20%">ปริมาณ<?php echo $type_name ?></th>
                        <th width="10%">หน่วยนับ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($items as $item) :
                        $quantity_remain = $ISSUE->item_quantity_remain([$item['product_id'], $item['location_id'], $item['store_id'], $item['item_id']]);
                        $confirm_remain = $ISSUE->item_confirm_remain([$item['product_id'], $item['location_id'], $item['store_id'], $item['item_id']]);
                      ?>
                        <tr>
                          <td class="text-center">
                            <a href="javascript:void(0)" class="badge badge-danger font-weight-light item-delete" id="<?php echo $item['item_id'] ?>">ลบ</a>
                          </td>
                          <td><?php echo $item['product_name'] ?></td>
                          <td><?php echo $item['location_name'] ?></td>
                          <td><?php echo $item['store_name'] ?></td>
                          <td class="text-right"><?php echo $quantity_remain ?></td>
                          <td class="text-right"><?php echo $confirm_remain ?></td>
                          <td class="text-right">
                            <?php echo number_format($item['quantity'], 0) . ($item['unit_id'] === $item['unit'] ? "" : " ({$item['product_quantity']} {$item['product_unit']})") ?>
                          </td>
                          <td class="text-center"><?php echo $item['unit_name'] ?></td>
                        </tr>
                      <?php endforeach; ?>
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
                        <td></td>
                        <td class="text-right"><span class="item-remain"></span></td>
                        <td>
                          <input type="number" class="form-control form-control-sm text-center item-quantity" name="item_quantity[]" min="0" step="1">
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
                  <?php endif; ?>
                  <?php if ($type === 3) : ?>
                    <thead>
                      <tr>
                        <th width="5%">#</th>
                        <th width="20%">วัตถุดิบ</th>
                        <th width="10%">คลัง (ต้นทาง)</th>
                        <th width="10%">ห้อง (ต้นทาง)</th>
                        <th width="10%">ปริมาณ (คงเหลือ)</th>
                        <th width="10%">คลัง (ปลายทาง)</th>
                        <th width="10%">ห้อง (ปลายทาง)</th>
                        <th width="10%">ปริมาณ (โอนย้าย)</th>
                        <th width="10%">หน่วยนับ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($items as $item) :
                        $item_id = (intval($item['item_id']) + 1);
                      ?>
                        <tr>
                          <td class="text-center">
                            <a href="javascript:void(0)" class="badge badge-danger font-weight-light item-delete" id="<?php echo $item['item_id'] . "-" . $item_id ?>">ลบ</a>
                          </td>
                          <td><?php echo $item['product_name'] ?></td>
                          <td><?php echo $item['send_location'] ?></td>
                          <td><?php echo $item['send_store'] ?></td>
                          <td class="text-right"></td>
                          <td><?php echo $item['receive_location'] ?></td>
                          <td><?php echo $item['receive_store'] ?></td>
                          <td class="text-right"><?php echo number_format($item['quantity'], 0, '.', ',') ?></td>
                          <td class="text-center"><?php echo $item['unit_name'] ?></td>
                        </tr>
                      <?php endforeach; ?>
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
                          <select class="form-control form-control-sm location-select" name="item_send_location[]"></select>
                          <div class="invalid-feedback">
                            กรุณาเลือกข้อมูล!
                          </div>
                        </td>
                        <td class="text-left">
                          <select class="form-control form-control-sm store-select" name="item_send_store[]"></select>
                          <div class="invalid-feedback">
                            กรุณาเลือกข้อมูล!
                          </div>
                        </td>
                        <td class="text-center"><span class="item-remain"></span></td>
                        <td class="text-left">
                          <select class="form-control form-control-sm location-select" name="item_receive_location[]"></select>
                          <div class="invalid-feedback">
                            กรุณาเลือกข้อมูล!
                          </div>
                        </td>
                        <td class="text-left">
                          <select class="form-control form-control-sm store-select" name="item_receive_store[]"></select>
                          <div class="invalid-feedback">
                            กรุณาเลือกข้อมูล!
                          </div>
                        </td>
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
                  <?php endif; ?>
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
  let type = ($("input[name='type']").val() ? parseInt($("input[name='type']").val()) : "");

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

    if (type === 1) {
      initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/issue/item-all-select");
    } else {
      initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/issue/item-remain-select");
    }

    initializeSelect2($(".location-select"), "-- คลัง --", "/issue/location-select");
    initializeSelect2($(".store-select"), "-- ห้อง --", "/issue/store-select");
    initializeSelect2($(".unit-select"), "-- หน่วยนับ --", "/issue/unit-select");
  });

  if (type === 1) {
    initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/issue/item-all-select");
  } else {
    initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/issue/item-remain-select");
  }

  initializeSelect2($(".location-select"), "-- คลัง --", "/issue/location-select");
  initializeSelect2($(".store-select"), "-- ห้อง --", "/issue/store-select");
  initializeSelect2($(".unit-select"), "-- หน่วยนับ --", "/issue/unit-select");

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
        axios.post("/issue/item-delete", {
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