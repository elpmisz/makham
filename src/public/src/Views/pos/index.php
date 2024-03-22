<?php
$menu = "service";
$page = "service-pos";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Sale;

$SALE = new Sale();
$products = $SALE->product_show();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="row">
      <div class="col-xl-7">
        <div class="card shadow">
          <div class="card-body">
            <div class="row">
              <div class="col-xl-12">
                <div class="row mb-2">
                  <?php foreach ($products as $product) : ?>
                    <div class="col-xl-4">
                      <div class="card shadow">
                        <img src="/../src/Publics/product/<?php echo $product['image'] ?>" class="card-img-top" alt="product-image">
                        <div class="card-body">
                          <span class="font90"><?php echo $product['product_name'] ?></span>
                          <span><?php echo "{$product['price']} บาท" ?></span>
                          <button class="btn btn-success btn-sm product-add" id="<?php echo $product['product_id'] ?>">หยิบลงตะกร้า</button>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-5">
        <div class="row">
          <div class="col-xl-12">
            <div class="card shadow">
              <div class="card-header text-center">
                รายการขาย
              </div>
              <div class="card-body">
                <form action="/pos/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                  <div class="row mb-2" style="display: none;">
                    <label class="col-xl-4 col-form-label">USER ID</label>
                    <div class="col-xl-4">
                      <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ลูกค้า</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm customer-select" name="customer">
                        <?php
                        if (!empty($_SESSION['customer'])) {
                          echo "<option value='{$_SESSION['customer']}'>{$_SESSION['customer_name']}</option>";
                        }
                        ?>
                      </select>
                      <div class="invalid-feedback">
                        กรุณาเลือกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ส่งเสริมการขาย</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm promotion-select" name="promotion">
                        <?php
                        if (!empty($_SESSION['promotion'])) {
                          echo "<option value='{$_SESSION['promotion']}'>{$_SESSION['promotion_name']}</option>";
                        }
                        ?>
                      </select>
                      <div class="invalid-feedback">
                        กรุณาเลือกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2" style="display: none;">
                    <label class="col-xl-4 col-form-label">TYPE</label>
                    <div class="col-xl-4">
                      <input type="number" class="form-control form-control-sm text-center promotion-type" readonly>
                    </div>
                  </div>
                  <div class="row mb-2" style="display: none;">
                    <label class="col-xl-4 col-form-label">DISCOUNT</label>
                    <div class="col-xl-4">
                      <input type="number" class="form-control form-control-sm text-center promotion-discount" readonly>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ภาษีมูลค่าเพิ่ม</label>
                    <div class="col-xl-4">
                      <input type="number" class="form-control form-control-sm text-center product-vat" name="vat" value="<?php echo (!empty($_SESSION['vat']) ? $_SESSION['vat'] : 7) ?>" min="0" max="10">
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ค้นหาสินค้า</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm product-select"></select>
                      <div class="invalid-feedback">
                        กรุณาเลือกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row justify-content-center mb-2">
                    <div class="col-xl-12">
                      <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                          <thead>
                            <tr>
                              <th width="10%">#</th>
                              <th width="40%">สินค้า</th>
                              <th width="10%">ราคาขาย</th>
                              <th width="20%">จำนวน</th>
                              <th width="20%">รวม</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if (isset($_SESSION['cart'])) :
                              foreach ($_SESSION['cart'] as $pid => $qty) :
                                $pd = $SALE->product_detail([$pid]);
                                $total = ($pd['price'] * $qty);
                            ?>
                                <tr>
                                  <td class="text-center">
                                    <a href="javascript:void(0)" class="badge badge-danger font-weight-light product-delete" id="<?php echo $pid ?>">ลบ</a>
                                  </td>
                                  <td class="text-left"><?php echo $pd['product_name'] ?></td>
                                  <td class="text-right"><span class="product-price"><?php echo $pd['price'] ?></span></td>
                                  <td>
                                    <input type="hidden" class="form-control form-control-sm text-center" name="product[]" value="<?php echo $pid ?>">
                                    <input type="hidden" class="form-control form-control-sm text-center" name="price[]" value="<?php echo $pd['price'] ?>">
                                    <input type="number" class="form-control form-control-sm text-center product-quantity" name="quantity[]" value="<?php echo $qty ?>">
                                  </td>
                                  <td class="text-right"><span class="product-total"><?php echo $total ?></span></td>
                                </tr>
                              <?php endforeach; ?>
                              <tr>
                                <td class="text-right h6" colspan="4">รวมเป็นเงิน</td>
                                <td class="text-right h6"><span class="total-result"></span></td>
                              </tr>
                              <tr>
                                <td class="text-right h6" colspan="4">ส่วนลด</td>
                                <td class="text-right h6"><span class="result-discount"></span></td>
                              </tr>
                              <tr>
                                <td class="text-right h6" colspan="4">ยอดรวมหลังหักส่วนลด</td>
                                <td class="text-right h6"><span class="total-discount"></span></td>
                              </tr>
                              <tr>
                                <td class="text-right h6" colspan="4">ภาษีมูลค่าเพิ่ม <span class="vat-text"></span></td>
                                <td class="text-right h6"><span class="total-vat"></span></td>
                              </tr>
                              <tr>
                                <td class="text-right h6" colspan="4">ราคาไม่รวมภาษีมูลค่าเพิ่ม</td>
                                <td class="text-right h6"><span class="total-all"></span></td>
                              </tr>
                              <tr>
                                <td class="text-right h6" colspan="4">จำนวนเงินรวมทั้งสิ้น</td>
                                <td class="text-right h5"><span class="total-discount"></span></td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <div class="row justify-content-center mb-2">
                    <div class="col-xl-4 mb-2">
                      <button type="submit" class="btn btn-sm btn-success btn-block">
                        <i class="fas fa-check pr-2"></i>ยืนยัน
                      </button>
                    </div>
                    <div class="col-xl-4 mb-2">
                      <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-block cart-clear">
                        <i class="fa fa-times pr-2"></i>ลบตะกร้า
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  $(document).on("change", ".promotion-select", function() {
    $(".product-vat").val(0);
  });
  $(document).on("change blur", ".promotion-select, .product-select, .product-vat, .product-quantity", function() {
    let promotion = $(".promotion-select").val();
    let product = $(".product-select").val();
    let vat = parseInt($(".product-vat").val());
    let vat_text = (vat > 0 ? vat + " %" : "");
    $(".vat-text").text(vat_text);

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
    $(".product-quantity").each(function() {
      let row = $(this).closest("tr");
      let quantity = row.find(".product-quantity").val();
      let price = row.find(".product-price").text();
      let total = (quantity * price);
      row.find(".product-total").text(total.toLocaleString("en-US", {
        minimumFractionDigits: 2
      }));
      total_result += parseFloat(total);
    });
    $(".total-result").text(total_result.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }));
    var type = parseInt($(".promotion-type").val());
    var discount = $(".promotion-discount").val();
    discount = (type === 1 ? parseFloat(discount) : parseFloat(total_result * discount));
    $(".result-discount").text(discount.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }))
    let total_discount = (total_result - discount);
    $(".total-discount").text(total_discount.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }))
    let total_vat = (total_discount * (vat / 107));
    $(".total-vat").text(total_vat.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }));
    let total_all = (total_discount - total_vat);
    $(".total-all").text(total_all.toLocaleString("en-US", {
      minimumFractionDigits: 2
    }));
  });

  $(".customer-select").select2({
    placeholder: "-- ลูกค้า --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/pos/customer-select",
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

  $(".product-select").select2({
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

  $(document).on("click", ".product-add", function() {
    let product = $(this).prop("id");
    let customer = $(".customer-select").val();
    let promotion = $(".promotion-select").val();
    let vat = $(".product-vat").val();

    if (product) {
      axios.post("/pos/product-add", {
          product: product,
          customer: customer,
          promotion: promotion,
          vat: vat
        })
        .then((res) => {
          let result = res.data;
          if (parseInt(result) === 200) {
            location.reload();
          }
        }).catch((error) => {
          console.log(error);
        });
    }
  });

  $(document).on("click", ".product-delete", function() {
    let product = $(this).prop("id");

    if (product) {
      axios.post("/pos/product-delete", {
          product: product
        })
        .then((res) => {
          let result = res.data;
          if (parseInt(result) === 200) {
            location.reload();
          }
        }).catch((error) => {
          console.log(error);
        });
    }
  });

  $(document).on("click", ".cart-clear", function() {
    axios.post("/pos/cart-clear")
      .then((res) => {
        let result = res.data;
        if (parseInt(result) === 200) {
          location.reload();
        }
      }).catch((error) => {
        console.log(error);
      });
  });

  $(document).on("change", ".product-select", function() {
    let product = $(this).val();
    let customer = $(".customer-select").val();
    let promotion = $(".promotion-select").val();
    let vat = $(".product-vat").val();

    if (product) {
      axios.post("/pos/product-add", {
          product: product,
          customer: customer,
          promotion: promotion,
          vat: vat
        })
        .then((res) => {
          let result = res.data;
          if (parseInt(result) === 200) {
            location.reload();
          }
        }).catch((error) => {
          console.log(error);
        });
    }
  });
</script>