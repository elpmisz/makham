<?php
$menu = "service";
$page = "service-sale";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Sale;

$SALE = new Sale();

$row = $SALE->sale_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$fullname = (!empty($row['fullname']) ? $row['fullname'] : "");
$ticket = (!empty($row['ticket']) ? $row['ticket'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$promotion = (!empty($row['promotion']) ? $row['promotion'] : "");
$promotion_name = (!empty($row['promotion_name']) ? $row['promotion_name'] : "");
$promotion_type = (!empty($row['promotion_type']) ? $row['promotion_type'] : "");
$discount = (!empty($row['discount']) ? $row['discount'] : "");
$vat = (!empty($row['vat']) ? $row['vat'] : "");
$amount = (!empty($row['amount']) ? $row['amount'] : "");
$discount_amount = (!empty($row['discount_amount']) ? $row['discount_amount'] : "");
$discount_total = (!empty($row['discount_total']) ? $row['discount_total'] : "");
$vat_total = (!empty($row['vat_total']) ? $row['vat_total'] : "");
$sale_total = (!empty($row['sale_total']) ? $row['sale_total'] : "");
$created = (!empty($row['created']) ? $row['created'] : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">เพิ่ม</h4>
      </div>
      <div class="card-body">
        <form action="/sale/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-2 offset-xl-1 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">เลขที่ใบ</label>
            <div class="col-xl-3 text-underline">
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
            <label class="col-xl-2 offset-xl-1 col-form-label">รายละเอียด</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5" required><?php echo $text ?></textarea>
              <div class="invalid-feedback">
                กรุณากรอกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ส่งเสริมการขาย</label>
            <div class="col-xl-4">
              <select class="form-control form-control-sm promotion-select" name="promotion">
                <?php echo "<option value='{$promotion}'>{$promotion_name}</option>" ?>
              </select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">TYPE</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center promotion-type" value="<?php echo $promotion_type ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">DISCOUNT</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center promotion-discount" value="<?php echo $discount ?>" readonly>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 offset-xl-1 col-form-label">ภาษีมูลค่าเพิ่ม</label>
            <div class="col-xl-2">
              <input type="number" class="form-control form-control-sm text-center item-vat" name="vat" value="<?php echo $vat ?>" min="0" max="10">
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
                      <th width="40%">สินค้า</th>
                      <th width="10%">หน่วยนับ</th>
                      <th width="10%">ราคาขาย</th>
                      <th width="10%">ปริมาณ (คงเหลือ)</th>
                      <th width="10%">ปริมาณ (ขาย)</th>
                      <th width="10%">รวม</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-right h6" colspan="6">รวมเป็นเงิน</td>
                      <td class="text-right h6">
                        <span class="total-result"><?php echo number_format($amount, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="6">ส่วนลด</td>
                      <td class="text-right h6">
                        <span class="result-discount"><?php echo number_format($discount_amount, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="6">ยอดรวมหลังหักส่วนลด</td>
                      <td class="text-right h6">
                        <span class="total-discount"><?php echo number_format($discount_total, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="6">ภาษีมูลค่าเพิ่ม <span class="vat-text"></span></td>
                      <td class="text-right h6">
                        <span class="total-vat"><?php echo number_format($vat_total, 2) ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-right h6" colspan="6">จำนวนเงินรวมทั้งสิ้น</td>
                      <td class="text-right h6">
                        <span class="total-all"><?php echo number_format($sale_total, 2) ?></span>
                      </td>
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
              <a href="/sale" class="btn btn-sm btn-danger btn-block">
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
    row.after(clone);
    clone.show();

    $(".item-select").select2({
      placeholder: "-- วัตถุดิบ --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/sale/product-select",
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

  $(document).on("click change", ".item-decrease, .promotion-select", function() {
    $(this).closest("tr").remove();
    let row = $(".item-tr");
    row.find(".item-quantity").val("");
    $(".item-total, .total-result, .result-discount, .total-discount, .total-vat, .total-all").text("")
  });

  $(document).on("blur change", ".item-vat, .item-quantity, .promotion-select", function() {
    let vat = parseInt($(".item-vat").val());
    let vat_text = (vat > 0 ? vat + " %" : "");
    $(".vat-text").text(vat_text);

    var type = parseInt($(".promotion-type").val());
    var discount = $(".promotion-discount").val();

    let promotion = $(".promotion-select").val();
    axios.post("/sale/promotion-detail", {
        promotion: promotion
      })
      .then((res) => {
        let result = res.data;
        $(".promotion-type").val(result.type);
        $(".promotion-discount").val(result.discount);
      }).catch((error) => {
        console.log(error);
      });

    var total_result = 0;
    $(".item-quantity").each(function() {
      let row = $(this).closest("tr");
      let quantity = row.find(".item-quantity").val();
      let price = row.find(".item-price").text();
      let total = (quantity * price);
      row.find(".item-total").text(total.toLocaleString("en-US", {
        minimumFractionDigits: 2
      }));
      total_result += parseFloat(total);
    });
    $(".total-result").text(total_result.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }));
    discount = (type === 1 ? parseFloat(discount) : parseFloat(total_result * discount));
    $(".result-discount").text(discount.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }))
    let total_discount = (total_result - discount);
    $(".total-discount").text(total_discount.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }))
    let total_vat = (total_discount * (vat / 100));
    $(".total-vat").text(total_vat.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }));
    let total_all = (total_discount + total_vat);
    $(".total-all").text(total_all.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }));
  });

  $(".promotion-select").select2({
    placeholder: "-- ส่งเสริมการขาย --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/sale/promotion-select",
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

  $(".item-select").select2({
    placeholder: "-- วัตถุดิบ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/sale/product-select",
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
        row.find(".product-price").val(result.price);
        row.find(".item-price").text(result.price);
        row.find(".item-unit").text(result.unit_name);
        row.find(".item-quantity").prop("max", result.issue_remain);
      }).catch((error) => {
        console.log(error);
      });
  });
</script>