<?php
$menu = "service";
$page = "service-waste";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Waste;

$WASTE = new Waste();

$row = $WASTE->waste_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$purchase_ticket = (!empty($row['purchase_ticket']) ? $row['purchase_ticket'] : "");
$fullname = (!empty($row['firstname']) ? $row['firstname'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");
$created = (!empty($row['created']) ? $row['created'] : "");

$items = $WASTE->item_view([$uuid, 1]);
$others = $WASTE->item_view([$uuid, 2]);
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบสรุปของเสีย</h4>
      </div>
      <div class="card-body">
        <form action="/waste/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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
            <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่ใบสั่งผลิต</label>
            <div class="col-xl-4 text-underline">
              <?php echo $purchase_ticket ?>
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
                    <?php foreach ($items as $key => $item) : $key++; ?>
                      <tr>
                        <td class="text-center">
                          <a href="javascript:void(0)" class="badge badge-danger font-weight-light item-delete" id="<?php echo $item['id'] ?>">ลบ</a>
                        </td>
                        <td class="text-left"><?php echo $item['item'] ?></td>
                        <td class="text-center"><?php echo $item['quantity'] ?></td>
                        <td class="text-left"><?php echo $item['remark'] ?></td>
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
                      <td>
                        <input type="number" class="form-control form-control-sm text-center" name="item_quantity[]" min="0" step="0.01">
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
                    <?php foreach ($others as $key => $other) : $key++; ?>
                      <tr>
                        <td class="text-center">
                          <a href="javascript:void(0)" class="badge badge-danger font-weight-light item-delete" id="<?php echo $other['id'] ?>">ลบ</a>
                        </td>
                        <td class="text-left"><?php echo $other['item'] ?></td>
                        <td class="text-center"><?php echo $other['quantity'] ?></td>
                        <td class="text-left"><?php echo $other['remark'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                    <tr class="other-tr">
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success other-increase">+</button>
                        <button type="button" class="btn btn-sm btn-danger other-decrease">-</button>
                      </td>
                      <td>
                        <select class="form-control form-control-sm other-select" name="other_product[]" required></select>
                        <div class="invalid-feedback">
                          กรุณาเลือกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <input type="number" class="form-control form-control-sm text-center" name="other_quantity[]" min="0" step="0.01">
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </td>
                      <td>
                        <input type="text" class="form-control form-control-sm text-left" name="other_remark[]">
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
            <label class="col-xl-3 offset-xl-1 col-form-label">รายละเอียด</label>
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
  $(".item-decrease, .other-decrease").hide();
  $(document).on("click", ".item-increase", function() {
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

    initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/bom/item-select");
  });

  $(document).on("click", ".other-increase", function() {
    let row = $(".other-tr:last");
    let clone = row.clone();
    clone.find("input, select, span").val("").empty();
    clone.find(".other-increase").hide();
    clone.find(".other-decrease").show();
    clone.find(".other-decrease").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();

    initializeSelect2($(".other-select"), "-- สิ่งแปลกปลอม --", "/waste/other-select");
  });

  initializeSelect2($(".item-select"), "-- วัตถุดิบ --", "/bom/item-select");
  initializeSelect2($(".other-select"), "-- สิ่งแปลกปลอม --", "/waste/other-select");

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
        axios.post("/waste/item-delete", {
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