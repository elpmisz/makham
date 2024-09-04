<?php
$menu = "setting";
$page = "setting-product";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Bom;
use App\Classes\Product;
use splitbrain\phpQRCode\QRCode;

$BOM = new Bom();
$PRODUCT = new Product();

$row = $PRODUCT->product_view([$uuid]);
$images = $PRODUCT->image_view([$uuid]);
$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$code = (!empty($row['code']) ? $row['code'] : "");
$product_name = (!empty($row['product_name']) ? $row['product_name'] : "");
$cost = (!empty($row['cost']) ? $row['cost'] : "");
$price = (!empty($row['price']) ? $row['price'] : "");
$min = (!empty($row['min']) ? $row['min'] : "");
$max = (!empty($row['max']) ? $row['max'] : "");
$bom_id = (!empty($row['bom_id']) ? $row['bom_id'] : "");
$bom_uuid = (!empty($row['bom_uuid']) ? $row['bom_uuid'] : "");
$bom_name = (!empty($row['bom_name']) ? $row['bom_name'] : "");
$supplier = (!empty($row['supplier']) ? $row['supplier'] : "");
$supplier_name = (!empty($row['supplier_name']) ? $row['supplier_name'] : "");
$unit = (!empty($row['unit']) ? $row['unit'] : "");
$unit_name = (!empty($row['unit_name']) ? $row['unit_name'] : "");
$per = (!empty($row['per']) ? $row['per'] : "");
$brand = (!empty($row['brand']) ? $row['brand'] : "");
$brand_name = (!empty($row['brand_name']) ? $row['brand_name'] : "");
$category = (!empty($row['category']) ? $row['category'] : "");
$category_name = (!empty($row['category_name']) ? $row['category_name'] : "");
$store = (!empty($row['store']) ? $row['store'] : "");
$store_name = (!empty($row['store_name']) ? $row['store_name'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");

$boms = $BOM->item_view([$bom_uuid]);
$stocks = $PRODUCT->stock_data([$uuid]);
$issue_count = $PRODUCT->issue_count([$id]);

$url = (!empty($_SERVER['HTTP_REFERER']) ? "{$_SERVER['HTTP_REFERER']}/complete/{$uuid}" : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">
        <form action="/product/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <?php if (count($images) > 0) : ?>
            <div class="row mb-2 justify-content-center">
              <div class="col-xl-4 offset-xl-4">
                <div id="control" class="carousel slide" data-ride="carousel">
                  <div class="carousel-inner">
                    <?php foreach ($images as $key => $image) : ?>
                      <div class="carousel-item <?php echo ($key === 0 ? "active" : "") ?>">
                        <img src="/src/Publics/product/<?php echo $image['name'] ?>" class="d-block w-100 rounded  product-image" alt="product-image">
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <button class="carousel-control-prev" type="button" data-target="#control" data-slide="prev">
                  </button>
                  <button class="carousel-control-next" type="button" data-target="#control" data-slide="next">
                  </button>
                </div>
              </div>
              <div class="col-xl-4">
                <?php echo QRCode::svg("{$url}"); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (count($images) <= 0) : ?>
            <div class="row mb-2 justify-content-center">
              <div class="col-xl-2 offset-xl-8">
                <?php echo QRCode::svg("{$url}"); ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">รูปวัตถุดิบ / สินค้า</label>
            <div class="col-xl-6">
              <table class="table table-borderless">
                <?php foreach ($images as $key => $image) : $key++ ?>
                  <tr>
                    <td class="text-center" width="10%">
                      <a href="javascript:void(0)" class="badge badge-danger font-weight-light image-delete" id="<?php echo $image['id'] ?>">ลบ</a>
                    </td>
                    <td width="90%">
                      <a href="/src/Publics/product/<?php echo $image['name'] ?>" target="_blank">
                        <?php echo "{$row['product_name']}_{$key}" ?>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <tr class="tr-file">
                  <td class="text-center" width="10%">
                    <button type="button" class="btn btn-sm btn-success increase-file">+</button>
                    <button type="button" class="btn btn-sm btn-danger decrease-file">-</button>
                  </td>
                  <td>
                    <input type="file" class="form-control-file" name="file[]" accept=".jpeg, .png, .jpg">
                  </td>
                </tr>
              </table>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-xl-6">
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">ID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="id" value="<?php echo $id ?>" readonly>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">UUID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $uuid ?>" readonly>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">รหัสสินค้า</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="code" value="<?php echo $code ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ชื่อ</label>
                <div class="col-xl-8">
                  <input type="text" class="form-control form-control-sm" name="name" value="<?php echo $product_name ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ราคาซื้อ</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="cost" value="<?php echo $cost ?>" step="0.01" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ราคาขาย</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="price" value="<?php echo $price ?>" step="0.01" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">MIN</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="min" value="<?php echo $min ?>" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">MAX</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-right" name="max" value="<?php echo $max ?>" min="0">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-6">

              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">หน่วยนับ</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm unit-select" name="unit" required>
                    <?php
                    if ($unit > 0) {
                      echo "<option value='{$unit}'>{$unit_name}</option>";
                    }
                    ?>
                  </select>
                  <div class="invalid-feedback">
                    กรุณาเลือกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">กิโลกรัม/หน่วย</label>
                <div class="col-xl-4">
                  <input type="number" class="form-control form-control-sm text-center" name="per" min="0" value="<?php echo $per ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ยี่ห้อ</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm brand-select" name="brand">
                    <?php
                    if ($brand > 0) {
                      echo "<option value='{$brand}'>{$brand_name}</option>";
                    }
                    ?>
                  </select>
                  <div class="invalid-feedback">
                    กรุณาเลือกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ผู้จัดจำหน่าย</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm supplier-select" name="supplier">
                    <?php
                    if ($supplier > 0) {
                      echo "<option value='{$supplier}'>{$supplier_name}</option>";
                    }
                    ?>
                  </select>
                  <div class="invalid-feedback">
                    กรุณาเลือกข้อมูล!
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">รายละเอียดเพิ่มเติม</label>
            <div class="col-xl-6">
              <textarea class="form-control form-control-sm" name="text" rows="5"><?php echo $text ?></textarea>
            </div>
          </div>

          <?php if (intval($issue_count) > 0) : ?>
            <div class="row mb-2">
              <div class="col-xl-10">
                <div class="card shadow">
                  <div class="card-header">
                    <h5>รายการสินค้าคงเหลือ</h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col-xl-12">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover">
                            <thead>
                              <tr class="table-success">
                                <th width="10%">#</th>
                                <th width="40%">สถานที่</th>
                                <th width="10%">ปริมาณ (นำเข้า)</th>
                                <th width="10%">ปริมาณ (เบิกออก)</th>
                                <th width="10%">ปริมาณ (คงเหลือ)</th>
                              </tr>
                            </thead>
                            <?php foreach ($stocks as $key => $stock) : $key++ ?>
                              <tr>
                                <td class="text-center"><?php echo $key ?></td>
                                <td class="text-left"><?php echo $stock['location_name'] ?></td>
                                <td class="text-right"><?php echo $stock['income'] ?></td>
                                <td class="text-right"><?php echo $stock['outcome'] ?></td>
                                <td class="text-right"><?php echo $stock['remain'] ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if (intval($issue_count) > 0) : ?>
            <div class="row mb-2">
              <div class="col-xl-12">
                <div class="card shadow">
                  <div class="card-header">
                    <h5>ประวัติการทำรายการ</h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col-xl-12">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover issue-data">
                            <thead>
                              <tr class="table-info">
                                <th width="10%">สถานะ</th>
                                <th width="10%">ประเภท</th>
                                <th width="20%">รายละเอียด</th>
                                <th width="10%">สถานที่</th>
                                <th width="10%">ปริมาณ</th>
                                <th width="10%">หน่วยนับ</th>
                                <th width="10%">วันที่ทำรายการ</th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if (intval($bom_id) > 0) : ?>
            <div class="row mb-2">
              <div class="col-xl-12">
                <div class="card shadow">
                  <div class="card-header">
                    <h5>สูตรการผลิต</h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col-xl-12">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover">
                            <thead>
                              <tr class="table-warning">
                                <th width="10%">#</th>
                                <th width="50%">วัตถุดิบ</th>
                                <th width="20%">ปริมาณ</th>
                                <th width="10%">หน่วยนับ</th>
                              </tr>
                            </thead>
                            <?php foreach ($boms as $key => $bom) : $key++ ?>
                              <tr>
                                <td class="text-center"><?php echo $key ?></td>
                                <td><?php echo $bom['product_name'] ?></td>
                                <td class="text-center"><?php echo $bom['quantity'] ?></td>
                                <td class="text-center"><?php echo $bom['unit_name'] ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">สถานะ</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-3">
                  <label class="form-check-label px-3">
                    <input class="form-check-input" type="radio" name="status" value="1" <?php echo $active ?> required>
                    <span class="text-success">ใช้งาน</span>
                  </label>
                </div>
                <div class="col-xl-3">
                  <label class="form-check-label px-3">
                    <input class="form-check-input" type="radio" name="status" value="2" <?php echo $inactive ?> required>
                    <span class="text-danger">ระงับการใช้งาน</span>
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
              <a href="/product" class="btn btn-sm btn-danger btn-block">
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
  $(".bom-select").select2({
    placeholder: "-- สูตรการผลิต --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/bom-select",
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

  $(".supplier-select").select2({
    placeholder: "-- ผู้จัดจำหน่าย --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/supplier-select",
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
      url: "/product/unit-select",
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

  $(".brand-select").select2({
    placeholder: "-- ยี่ห้อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/brand-select",
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

  $(".category-select").select2({
    placeholder: "-- หมวดหมู่ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/category-select",
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

  $(".store-select").select2({
    placeholder: "-- สถานที่จัดเก็บ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/store-select",
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

  $(".decrease-file").hide();
  $(document).on("click", ".increase-file", function() {
    let row = $(".tr-file:last");
    let clone = row.clone();
    clone.find("input").val("");
    clone.find(".increase-file").hide();
    clone.find(".decrease-file").show();
    clone.find(".decrease-file").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();
  });

  $(document).on("change", "input[name='file[]']", function() {
    let file = $(this).val();
    let size = ($(this)[0].files[0].size / (1024 * 1024)).toFixed(2);
    let extension = file.split(".").pop().toLowerCase();
    let allow = ["png", "jpeg", "jpg"];
    if (size > 5) {
      Swal.fire({
        icon: "error",
        title: "ขนาดรูปไม่เกิน 5MB!",
      })
      $(this).val("");
    }

    if ($.inArray(extension, allow) === -1) {
      Swal.fire({
        icon: "error",
        title: "เฉพาะไฟล์รูป PNG JPG or JPEG",
      })
      $(this).val("");
    }
  });

  $(document).on("click", ".image-delete", function(e) {
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
        axios.post("/product/image-delete", {
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

  let uuid = $("input[name='uuid']").val();
  $(".issue-data").DataTable({
    serverSide: true,
    searching: false,
    scrollX: true,
    order: [],
    ajax: {
      url: "/product/issue-data",
      type: "POST",
      data: {
        uuid: uuid,
      }
    },
    columnDefs: [{
      targets: [0, 1, 3, 5],
      className: "text-center",
    }, {
      targets: [4],
      className: "text-right",
    }],
    "oLanguage": {
      "sLengthMenu": "แสดง _MENU_ ลำดับ ต่อหน้า",
      "sZeroRecords": "ไม่พบข้อมูลที่ค้นหา",
      "sInfo": "แสดง _START_ ถึง _END_ ของ _TOTAL_ ลำดับ",
      "sInfoEmpty": "แสดง 0 ถึง 0 ของ 0 ลำดับ",
      "sInfoFiltered": "",
      "sSearch": "ค้นหา :",
      "oPaginate": {
        "sFirst": "หน้าแรก",
        "sLast": "หน้าสุดท้าย",
        "sNext": "ถัดไป",
        "sPrevious": "ก่อนหน้า"
      }
    },
  });
</script>