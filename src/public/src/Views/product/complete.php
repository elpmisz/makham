<?php
$menu = "setting";
$page = "setting-product";
include_once(__DIR__ . "/../layout/header_no.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Bom;
use App\Classes\Product;

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
$brand = (!empty($row['brand']) ? $row['brand'] : "");
$brand_name = (!empty($row['brand_name']) ? $row['brand_name'] : "");
$category = (!empty($row['category']) ? $row['category'] : "");
$category_name = (!empty($row['category_name']) ? $row['category_name'] : "");
$location = (!empty($row['location']) ? $row['location'] : "");
$location_name = (!empty($row['location_name']) ? $row['location_name'] : "");
$text = (!empty($row['text']) ? $row['text'] : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");

$boms = $BOM->item_view([$bom_uuid]);
$issue_count = $PRODUCT->issue_count([$id]);
?>

<div class="row justify-content-center my-5 mx-2">
  <div class="col-xl-10">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">
        <form action="/product/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">


          <?php if (count($images) > 0) : ?>
            <div class="row mb-2 justify-content-center">
              <div class="col-xl-4">
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
            </div>
          <?php endif; ?>

          <div class="row mb-2">
            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">รหัสสินค้า</label>
                <div class="col-xl-8 text-underline">
                  <?php echo $code ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ชื่อ</label>
                <div class="col-xl-8 text-underline">
                  <?php echo $product_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ราคาซื้อ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $cost ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">ราคาขาย</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $price ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">MIN</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $min ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label">MAX</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $max ?>
                </div>
              </div>
            </div>

            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">สูตรการผลิต</label>
                <div class="col-xl-8 text-underline">
                  <?php echo $bom_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ผู้จัดจำหน่าย</label>
                <div class="col-xl-8 text-underline">
                  <?php echo $supplier_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">หน่วยนับ</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $unit_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">ยี่ห้อ</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $brand_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">หมวดหมู่</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $category_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">สถานที่</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $location_name ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">รายละเอียดเพิ่มเติม</label>
            <div class="col-xl-6 text-underline">
              <?php echo str_replace("\n", "<br>", $text) ?>
            </div>
          </div>

          <?php if (intval($bom_id) > 0) : ?>
            <div class="row mb-2">
              <div class="col-xl-12">
                <div class="card">
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

          <?php if (intval($issue_count) > 0) : ?>
            <div class="row mb-2">
              <div class="col-xl-12">
                <div class="card">
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
                                <th width="10%">ผู้ทำรายการ</th>
                                <th width="10%">ประเภท</th>
                                <th width="20%">รายละเอียด</th>
                                <th width="10%">ปริมาณ</th>
                                <th width="10%">วันที่ล่าสุด</th>
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

        </form>
      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
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
      targets: [0, 2, 5],
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