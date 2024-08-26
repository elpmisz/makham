<?php
$menu = "service";
$page = "service-issue";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Issue;

$ISSUE = new Issue();

$row = $ISSUE->issue_view([$uuid]);
$items = (intval($row['type']) === 3 ? $ISSUE->exchange_view([$uuid]) : $ISSUE->item_view([$uuid]));
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$fullname = (!empty($row['firstname']) ? $row['firstname'] : "");
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
        <h4 class="text-center">ใบนำสินค้าเข้า - ออก</h4>
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
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียด</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required><?php echo $text ?></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-sm-10">
              <div class="table-responsive">
                <table class="table table-bordered table-sm item-table">
                  <?php if ($type !== 3) : ?>
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
                      <?php foreach ($items as $item) : ?>
                        <tr>
                          <td class="text-center">
                            <a href="javascript:void(0)" class="badge badge-danger font-weight-light item-delete" id="<?php echo $item['item_id'] ?>">ลบ</a>
                          </td>
                          <td><?php echo $item['product_name'] ?></td>
                          <td><?php echo $item['location_name'] ?></td>
                          <td class="text-right"></td>
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
                          <select class="form-control form-control-sm location-select" name="item_location[]"></select>
                          <div class="invalid-feedback">
                            กรุณาเลือกข้อมูล!
                          </div>
                        </td>
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
                        <th width="10%">#</th>
                        <th width="30%">วัตถุดิบ</th>
                        <th width="20%">สถานที่ (ต้นทาง)</th>
                        <th width="20%">สถานที่ (ปลายทาง)</th>
                        <th width="10%">ปริมาณ (คงเหลือ)</th>
                        <th width="10%">ปริมาณ (โอนย้าย)</th>
                        <th width="10%">หน่วยนับ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item) : ?>
                        <tr>
                          <td class="text-center">
                            <a href="javascript:void(0)" class="badge badge-danger font-weight-light item-delete" id="<?php echo $item['item_id'] ?>">ลบ</a>
                          </td>
                          <td><?php echo $item['product_name'] ?></td>
                          <td><?php echo $item['send'] ?></td>
                          <td><?php echo $item['receive'] ?></td>
                          <td class="text-right"></td>
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
                          <select class="form-control form-control-sm location-select" name="item_send[]"></select>
                          <div class="invalid-feedback">
                            กรุณาเลือกข้อมูล!
                          </div>
                        </td>
                        <td class="text-left">
                          <select class="form-control form-control-sm location-select" name="item_receive[]"></select>
                          <div class="invalid-feedback">
                            กรุณาเลือกข้อมูล!
                          </div>
                        </td>
                        <td class="text-right"><span class="item-remain"></span></td>
                        <td>
                          <input type="number" class="form-control form-control-sm text-center item-quantity" name="item_quantity[]" min="0" step="0.01">
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

  $(document).on("change", ".item-select, .location-select", function() {
    let type = parseInt($("input[name='type']").val());
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
            if (type === 2) {
              row.find(".item-quantity").prop("max", result.remain)
            }
            row.find(".item-unit").text(result.unit_name);
          }).catch((error) => {
            console.log(error);
          });
      }
    });
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