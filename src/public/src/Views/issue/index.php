<?php
$menu = "service";
$page = "service-issue";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Issue;

$ISSUE = new Issue();
$approver = $ISSUE->auth_approve([$user['id']]);
$approver_count = $ISSUE->approver_count();
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ใบนำสินค้าเข้า - ออก</h4>
      </div>
      <div class="card-body">
        <div class="row justify-content-end mb-2">
          <?php if (intval($user['level']) === 9) : ?>
            <div class="col-xl-3 mb-2">
              <a href="/issue/auth" class="btn btn-info btn-sm btn-block">
                <i class="fas fa-users pr-2"></i>สิทธิ์ใช้งาน
              </a>
            </div>
          <?php endif; ?>
          <?php if (intval($approver) > 0 && intval($user['level']) === 9) : ?>
            <div class="col-xl-3 mb-2">
              <a href="/issue/manage" class="btn btn-success btn-sm btn-block">
                <i class="fas fa-list pr-2"></i>จัดการ
              </a>
            </div>
          <?php endif; ?>
          <div class="col-xl-3 mb-2">
            <button class="btn btn-danger btn-sm btn-block" data-toggle="modal" data-target="#modal-upload">
              <i class="fas fa-upload pr-2"></i>นำข้อมูลเข้า
            </button>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/issue/download" class="btn btn-primary btn-sm btn-block">
              <i class="fas fa-download pr-2"></i>นำข้อมูลออก
            </a>
          </div>
        </div>
        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <a href="/issue/income" class="btn btn-primary btn-sm btn-block">
              <i class="fas fa-plus pr-2"></i>นำเข้า
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/issue/outcome" class="btn btn-success btn-sm btn-block">
              <i class="fas fa-minus pr-2"></i>เบิกออก
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/issue/exchange" class="btn btn-warning btn-sm btn-block">
              <i class="fas fa-exchange-alt pr-2"></i>โอนย้าย
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
                          <th width="10%">ประเภท</th>
                          <th width="10%">ผู้ทำรายการ</th>
                          <th width="50%">รายละเอียด</th>
                          <th width="10%">วันที่</th>
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
                  <table class="table table-bordered table-hover request-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">เลขที่เอกสาร</th>
                        <th width="10%">ประเภท</th>
                        <th width="10%">ผู้ทำรายการ</th>
                        <th width="50%">รายละเอียด</th>
                        <th width="10%">วันที่</th>
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

<div class="modal fade" id="modal-upload" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form action="/issue/upload" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
          <div class="row mb-2">
            <label class="col-xl-4 col-form-label text-right">เอกสาร</label>
            <div class="col-xl-8">
              <input type="file" class="form-control form-control-sm" name="file" required>
              <div class="invalid-feedback">
                กรุณาเลือกเอกสาร!
              </div>
            </div>
          </div>
          <div class="row justify-content-center mb-2">
            <div class="col-xl-4 mb-2">
              <button type="submit" class="btn btn-success btn-sm btn-block btn-submit">
                <i class="fas fa-check pr-2"></i>ยืนยัน
              </button>
            </div>
            <div class="col-xl-4 mb-2">
              <button class="btn btn-danger btn-sm btn-block" data-dismiss="modal">
                <i class="fa fa-times mr-2"></i>ปิด
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  function filter_datatable() {
    $(".request-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/issue/request-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 2],
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
        url: "/issue/approve-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 2],
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

  $("#import-modal").on("hidden.bs.modal", function() {
    $(this).find("form")[0].reset();
  })

  $(document).on("change", "input[name='file']", function() {
    let fileSize = ($(this)[0].files[0].size) / (1024 * 1024);
    let fileExt = $(this).val().split(".").pop().toLowerCase();
    let fileAllow = ["xls", "xlsx", "csv"];
    let convFileSize = fileSize.toFixed(2);
    if (convFileSize > 10) {
      Swal.fire({
        icon: "error",
        title: "LIMIT 10MB!",
      })
      $(this).val("");
    }

    if ($.inArray(fileExt, fileAllow) == -1) {
      Swal.fire({
        icon: "error",
        title: "เฉพาะเอกสารนามสกุล XLS XLSX CSV!",
      })
      $(this).val("");
    }
  });

  $(document).on("submit", ".import", function() {
    $("#import-modal").modal("hide");
    $("#process-modal").modal("show");
  });
</script>