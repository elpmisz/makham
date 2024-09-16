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
$purchase_uuid = (!empty($row['purchase_uuid']) ? $row['purchase_uuid'] : "");
$purchase_ticket = (!empty($row['purchase_ticket']) ? $row['purchase_ticket'] : "");
$fullname = (!empty($row['firstname']) ? $row['firstname'] : "");
$text = (!empty($row['text']) ? str_replace("\n", "<br>", $row['text']) : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");
$status_name = (!empty($row['status_name']) ? $row['status_name'] : "");
$status_color = (!empty($row['status_color']) ? $row['status_color'] : "");
$approver = (!empty($row['approver_firstname']) ? $row['approver_firstname'] : "");
$approve_text = (!empty($row['approve_text']) ? str_replace("\n", "<br>", $row['approve_text']) : "");
$created = (!empty($row['created']) ? $row['created'] : "");

$items = $WASTE->item_view([$uuid, 1]);
$wastes = $WASTE->item_view([$uuid, 2]);
$texts = $WASTE->text_view([$uuid]);
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบสรุปของเสีย</h4>
      </div>
      <div class="card-body">
        <form action="/waste/approve" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

          <div class="row mb-2" style="display: none;">
            <label class="col-xl-3 offset-xl-1 col-form-label">USER ID</label>
            <div class="col-xl-4">
              <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['id'] ?>" readonly>
            </div>
          </div>
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
                <label class="col-xl-3 col-form-label">เลขที่ใบสั่งผลิต</label>
                <div class="col-xl-4 text-underline">
                  <a href="/purchase/complete/<?php echo $purchase_uuid ?>" target="_blank"><?php echo $purchase_ticket ?></a>
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

          <div class="row justify-content-center mb-2">
            <div class="col-sm-12">
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
                        <td class="text-center"><?php echo $key ?></td>
                        <td class="text-left"><?php echo $item['item'] ?></td>
                        <td class="text-center"><?php echo $item['quantity'] ?></td>
                        <td class="text-left"><?php echo $item['remark'] ?></td>
                      </tr>
                    <?php endforeach; ?>
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
                    <?php foreach ($wastes as $key => $waste) : $key++; ?>
                      <tr>
                        <td class="text-center"><?php echo $key ?></td>
                        <td class="text-left"><?php echo $waste['item'] ?></td>
                        <td class="text-center"><?php echo $waste['quantity'] ?></td>
                        <td class="text-left"><?php echo $waste['remark'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-xl-2 col-form-label">รายละเอียด</label>
            <div class="col-xl-6 text-underline">
              <?php echo $text ?>
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
              <span class="text-primary"><?php echo $row['approver'] ?></span>
            </div>
          </div>

          <div class="row justify-content-center mb-2">
            <div class="col-xl-3 mb-2">
              <a href="/waste" class="btn btn-sm btn-danger btn-block">
                <i class="fa fa-arrow-left pr-2"></i>กลับ
              </a>
            </div>
            <div class="col-xl-3 mb-2">
              <a href="/waste/print/<?php echo $uuid ?>" class="btn btn-sm btn-primary btn-block">
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