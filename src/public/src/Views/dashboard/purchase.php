<?php
$menu = "dashboard";
$page = "dashboard-purchase";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\DashboardPurchase;

$DASHBOARD = new DashboardPurchase();

// $card = $DASHBOARD->purchase_card();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายงานสั่งผลิต</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-block download-btn">
              <i class="fas fa-download pr-2"></i>นำข้อมูลออก
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <input type="text" class="form-control form-control-sm date-select" placeholder="-- วันที่ --">
          </div>
          <div class="col-xl-3 mb-2">
            <button class="btn btn-sm btn-block btn-primary search-btn">
              <i class="fa fa-search pr-2"></i>ค้นหา
            </button>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="card shadow">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover purchase-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">เลขที่เอกสาร</th>
                        <th width="10%">ลูกค้า</th>
                        <th width="10%">เป้าหมาย</th>
                        <th width="10%">วันที่ผลิต</th>
                        <th width="10%">วันที่เสร็จ</th>
                        <th width="10%">สินค้า</th>
                        <th width="20%">รายละเอียด</th>
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

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  $(document).on("click", ".download-btn", function() {
    let date = ($(".date-select").val() ? $(".date-select").val() : "");
    date = date.replaceAll("/", "+", date);
    let bom = ($(".bom-select").val() ? $(".bom-select").val() : "");
    let path = "/dashboard/purchase/download/" + date + "/" + bom;
    window.open(path);
  });

  $(document).on("click", ".search-btn", function() {
    let date = ($(".date-select").val() ? $(".date-select").val() : "");
    let bom = ($(".bom-select").val() ? $(".bom-select").val() : "");
    if (date || bom) {
      $(".purchase-data").DataTable().destroy();
      filter_datatable(date, bom);
    } else {
      $(".purchase-data").DataTable().destroy();
      filter_datatable();
    }
  });

  function filter_datatable(date, bom) {
    $(".purchase-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/dashboard/purchase/purchase-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 3, 4, 5],
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

  $(".date-select").on('keydown paste', function(e) {
    e.preventDefault();
  });

  $(".date-select").daterangepicker({
    autoUpdateInput: false,
    // minDate: moment(),
    showDropdowns: true,
    startDate: moment(),
    endDate: moment().startOf('day').add(1, 'day'),
    locale: {
      "format": "DD/MM/YYYY",
      "applyLabel": "ยืนยัน",
      "cancelLabel": "ยกเลิก",
      "daysOfWeek": [
        "อา", "จ", "อ", "พ", "พฤ", "ศ", "ส"
      ],
      "monthNames": [
        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
      ]
    },
    "applyButtonClasses": "btn-success",
    "cancelClass": "btn-danger"
  });

  $(".date-select").on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
  });

  $(".date-select").on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
  });
</script>