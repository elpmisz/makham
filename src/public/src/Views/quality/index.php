<?php
$menu = "service";
$page = "service-quality";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Quality;

$QUALITY = new Quality();
$approver = $QUALITY->auth_approve([$user['id']]);
$approver_count = $QUALITY->approver_count();
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบตรวจสอบคุณภาพ</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <?php if (intval($user['level']) === 9) : ?>
            <div class="col-xl-3 mb-2">
              <a href="/quality/auth" class="btn btn-info btn-sm btn-block">
                <i class="fas fa-users pr-2"></i>สิทธิ์ใช้งาน
              </a>
            </div>
          <?php endif; ?>
          <?php if (intval($approver) > 0 && intval($user['level']) === 9) : ?>
            <div class="col-xl-3 mb-2">
              <a href="/quality/manage" class="btn btn-success btn-sm btn-block">
                <i class="fas fa-list pr-2"></i>จัดการ
              </a>
            </div>
          <?php endif; ?>
          <div class="col-xl-3 mb-2">
            <a href="/quality/download" class="btn btn-danger btn-sm btn-block">
              <i class="fas fa-download pr-2"></i>นำข้อมูลออก
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/quality/subject" class="btn btn-primary btn-sm btn-block">
              <i class="fas fa-plus pr-2"></i>หัวข้อ
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/quality/create" class="btn btn-primary btn-sm btn-block">
              <i class="fas fa-plus pr-2"></i>เพิ่ม
            </a>
          </div>
        </div>

        <?php if (intval($approver) > 0 && intval($approver_count) > 0) : ?>
          <div class="row mb-2">
            <div class="col-xl-12">
              <div class="card shadow">
                <div class="card-header">
                  <h5 class="text-center">รายการรอดำเนินการ</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover approve-data">
                      <thead>
                        <tr>
                          <th width="10%">สถานะ</th>
                          <th width="10%">เลขที่เอกสาร</th>
                          <th width="10%">ผู้ทำรายการ</th>
                          <th width="20%">วัตถุดิบ</th>
                          <th width="10%">วันที่คัดมะขาม</th>
                          <th width="10%">วันที่รับเข้า</th>
                          <th width="20%">รายละเอียด</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="card shadow">
              <div class="card-header">
                <h5 class="text-center">รายการขอใช้บริการ</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover quality-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">เลขที่เอกสาร</th>
                        <th width="10%">ผู้ทำรายการ</th>
                        <th width="20%">วัตถุดิบ</th>
                        <th width="10%">วันที่คัดมะขาม</th>
                        <th width="10%">วันที่รับเข้า</th>
                        <th width="20%">รายละเอียด</th>
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
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  function filter_datatable() {
    $(".quality-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/quality/quality-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 4, 5],
        className: "text-center",
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

    $(".approve-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/quality/approve-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 4, 5],
        className: "text-center",
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
  };
</script>