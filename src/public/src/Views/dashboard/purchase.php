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

        <div class="row mb-2">
          <div class="col-xl-3 mb-2">
            <div class="card bg-primary text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['total']) ? $card['total'] : 0) ?></h3>
                <h5 class="text-right">ยอดผลิตทั้งหมด</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-info text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['dd']) ? $card['dd'] : 0) ?></h3>
                <h5 class="text-right">ยอดผลิตประจำวัน</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-success text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['mm']) ? $card['mm'] : 0) ?></h3>
                <h5 class="text-right">ยอดผลิตรายเดือน</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-danger text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['yy']) ? $card['yy'] : 0) ?></h3>
                <h5 class="text-right">ยอดผลิตรายปี</h5>
              </div>
            </div>
          </div>
        </div>

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
            <select class="form-control form-control-sm bom-select"></select>
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
                        <th width="10%">สูตรการผลิต</th>
                        <th width="10%">เครื่องจักร</th>
                        <th width="10%">เป้าหมาย</th>
                        <th width="10%">จำนวน</th>
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

        <div class="row mb-2">
          <div class="col-xl-5">
            <div class="card shadow">
              <div class="card-header">
                <h5>ยอดผลิตแยกตามสูตรการผลิต ประจำเดือน</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="bom-month-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover bom-month-table"></table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-7">
            <div class="card shadow">
              <div class="card-header">
                <h5>ยอดผลิตแยกตามสูตรการผลิต ประจำปี</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="bom-year-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover bom-year-table"></table>
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

  // function filter_datatable(date, bom) {
  //   $(".purchase-data").DataTable({
  //     serverSide: true,
  //     searching: true,
  //     scrollX: true,
  //     order: [],
  //     ajax: {
  //       url: "/dashboard/purchase/purchase-data",
  //       type: "POST",
  //       data: {
  //         date: date,
  //         bom: bom,
  //       }
  //     },
  //     columnDefs: [{
  //       targets: [0, 3, 4, 5],
  //       className: "text-center",
  //     }],
  //     "oLanguage": {
  //       "sLengthMenu": "แสดง _MENU_ ลำดับ ต่อหน้า",
  //       "sZeroRecords": "ไม่พบข้อมูลที่ค้นหา",
  //       "sInfo": "แสดง _START_ ถึง _END_ ของ _TOTAL_ ลำดับ",
  //       "sInfoEmpty": "แสดง 0 ถึง 0 ของ 0 ลำดับ",
  //       "sInfoFiltered": "",
  //       "sSearch": "ค้นหา :",
  //       "oPaginate": {
  //         "sFirst": "หน้าแรก",
  //         "sLast": "หน้าสุดท้าย",
  //         "sNext": "ถัดไป",
  //         "sPrevious": "ก่อนหน้า"
  //       }
  //     },
  //   });
  // };

  axios.post("/dashboard/purchase/bom-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.bom_name);
      let datas = result.map(item => item.mm);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">สูตร</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.bom_name + '</td>';
          div += '<td class="text-right">' + Number(v.mm).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".bom-month-table").empty().html(div);
        bomMonthRender("bom-month-chart", subjects, datas);
      } else {
        $(".bom-month-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  axios.post("/dashboard/purchase/bom-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.bom_name);
      let datas = result.map(item => item.yy);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">สูตร</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.bom_name + '</td>';
          div += '<td class="text-right">' + Number(v.yy).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".bom-year-table").empty().html(div);
        bomYearRender("bom-year-chart", subjects, datas);
      } else {
        $(".bom-year-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  var bomMonthChart = new Chart(document.getElementById("bom-month-chart"));

  function bomMonthRender(name, subjects, datas) {
    bomMonthChart.destroy();
    bomMonthChart = new Chart(
      document.getElementById(name),
      config = {
        type: "doughnut",
        data: {
          labels: subjects,
          datasets: [{
            label: "ประจำเดือน",
            data: datas,
            borderWidth: 1,
            fill: true,
            backgroundColor: getRandomColor(subjects.length),
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          }
        }
      }
    );
  }

  var bomYearChart = new Chart(document.getElementById("bom-year-chart"));

  function bomYearRender(name, subjects, datas) {
    bomYearChart.destroy();
    bomYearChart = new Chart(
      document.getElementById(name),
      config = {
        type: "bar",
        data: {
          labels: subjects,
          datasets: [{
            label: "ประจำปี",
            data: datas,
            fill: false,
            backgroundColor: getRandomColor(subjects.length),
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          }
        },
      }
    );
  }

  function getRandomColor(amount) {
    var colors = [];
    for (var i = 0; i < amount; i++) {
      var letters = '0123456789ABCDEF';
      var color = '#';
      for (var x = 0; x < 6; x++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      colors.push(color);
    }
    return colors;
  }

  $(".bom-select").select2({
    placeholder: "-- สูตรการผลิต --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/purchase/bom-select",
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