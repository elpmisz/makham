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
$text = (!empty($row['text']) ? str_replace("\n", "<br>", $row['text']) : "");
$type = (!empty($row['type']) ? $row['type'] : "");
$type_name = (!empty($row['type_name']) ? $row['type_name'] : "");
$type_color = (!empty($row['type_color']) ? $row['type_color'] : "");
$created = (!empty($row['created']) ? $row['created'] : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center"><?php echo "ใบ{$type_name}สินค้า" ?></h4>
      </div>
      <div class="card-body">
        <form action="/issue/approve" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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

          <div class="row">
            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 col-form label">ผู้ทำรายการ</label>
                <div class="col-xl-8 text-underline">
                  <?php echo $fullname ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 col-form-label">ประเภท</label>
                <div class="col-xl-4 text-underline text-<?php echo $type_color ?>">
                  <?php echo $type_name ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 col-form-label">วันที่<?php echo $type_name ?></label>
                <div class="col-xl-6 text-underline">
                  <?php echo $date ?>
                </div>
              </div>
            </div>

            <div class="col-xl-6">
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่เอกสาร</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $ticket ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-3 offset-xl-1 col-form-label">วันที่</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $created ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">รายละเอียด</label>
            <div class="col-xl-6 text-underline">
              <?php echo $text ?>
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
                        <th width="20%">ปริมาณ <?php echo "({$row['type_name']})" ?></th>
                        <th width="20%">ปริมาณ (ตรวจสอบ)</th>
                        <th width="10%">หน่วยนับ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $key => $item) : $key++; ?>
                        <tr>
                          <td class="text-center">
                            <?php echo $key ?>
                            <input type="hidden" class="form-control form-control-sm text-center" name="product[]" value="<?php echo $item['item_id'] ?>" readonly>
                          </td>
                          <td>
                            <a href="/product/edit/<?php echo $item['uuid'] ?>" target="_blank"><?php echo $item['product_name'] ?></a>
                          </td>
                          <td><?php echo $item['location_name'] ?></td>
                          <td><?php echo $item['store_name'] ?></td>
                          <td class="text-right"><?php echo number_format($item['quantity'], 0, '.', ',') ?></td>
                          <td class="text-right"><?php echo number_format($item['confirm'], 0, '.', ',') ?></td>
                          <td class="text-center"><?php echo $item['unit_name'] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  <?php endif; ?>
                  <?php if ($type === 3) : ?>
                    <thead>
                      <tr>
                        <th width="5%">#</th>
                        <th width="20%">วัตถุดิบ</th>
                        <th width="10%">คลัง (ต้นทาง)</th>
                        <th width="10%">ห้อง (ต้นทาง)</th>
                        <th width="10%">คลัง (ปลายทาง)</th>
                        <th width="10%">ห้อง (ปลายทาง)</th>
                        <th width="10%">ปริมาณ (โอนย้าย)</th>
                        <th width="10%">ปริมาณ (ตรวจสอบ)</th>
                        <th width="10%">หน่วยนับ</th>
                      </tr>
                    </thead>
                    <?php
                    foreach ($items as $key => $item) :
                      $key++;
                    ?>
                      <tr>
                        <td class="text-center">
                          <?php echo $key ?>
                        </td>
                        <td>
                          <a href="/product/edit/<?php echo $item['uuid'] ?>" target="_blank"><?php echo $item['product_name'] ?></a>
                        </td>
                        <td><?php echo $item['send_location'] ?></td>
                        <td><?php echo $item['send_store'] ?></td>
                        <td><?php echo $item['receive_location'] ?></td>
                        <td><?php echo $item['receive_store'] ?></td>
                        <td class="text-right">
                          <?php echo number_format($item['quantity'], 0) . ($item['unit_id'] === $item['unit'] ? "" : " ({$item['product_quantity']} {$item['product_unit']})") ?>
                        </td>
                        <td class="text-right">
                          <?php echo number_format($item['confirm'], 0) . ($item['unit_id'] === $item['unit'] ? "" : " ({$item['product_confirm']} {$item['product_unit']})") ?>
                        </td>
                        <td class="text-center"><?php echo $item['unit_name'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </table>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">ผลการตรวจสอบ</label>
            <div class="col-xl-4 text-underline">
              <span class="text-<?php echo $row['status_color'] ?>"><?php echo $row['status_name'] ?></span>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">ผู้ดำเนินการ</label>
            <div class="col-xl-4 text-underline">
              <span class="text-primary"><?php echo $row['approver_firstname'] . " - " . $row['approved'] ?></span>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-xl-3 mb-2">
              <a href="/issue" class="btn btn-sm btn-danger btn-block">
                <i class="fa fa-arrow-left pr-2"></i>กลับ
              </a>
            </div>
            <div class="col-xl-3 mb-2">
              <a href="/issue/print/<?php echo $uuid ?>" class="btn btn-sm btn-primary btn-block">
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
  $(".text-div").hide();
  $(document).on("click", "input[name='status']:checked", function() {
    let status = parseInt($(this).val());
    if (status === 3) {
      $(".text-div").show();
      $("textarea[name='remark']").prop("required", true);
    } else {
      $(".text-div").hide();
      $("textarea[name='remark']").prop("required", false);
    }
  });
</script>