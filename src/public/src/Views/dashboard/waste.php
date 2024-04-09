<?php
$menu = "dashboard";
$page = "dashboard-waste";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\DashboardWaste;

$DASHBOARD = new DashboardWaste();

$card = $DASHBOARD->waste_card();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายงานสรุปของเสีย</h4>
      </div>
      <div class="card-body">

        <div class="row mb-2">
          <div class="col-xl-3 mb-2">
            <div class="card bg-primary text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['total']) ? $card['total'] : 0) ?></h3>
                <h5 class="text-right">รายการทั้งหมด</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-info text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['dd']) ? $card['dd'] : 0) ?></h3>
                <h5 class="text-right">รายการประจำวัน</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-success text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['mm']) ? $card['mm'] : 0) ?></h3>
                <h5 class="text-right">รายการรายเดือน</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-danger text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['yy']) ? $card['yy'] : 0) ?></h3>
                <h5 class="text-right">รายการรายปี</h5>
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
                  <table class="table table-bordered table-hover waste-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">เลขที่เอกสาร</th>
                        <th width="40%">รายละเอียด</th>
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
          <div class="col-xl-6">
            <div class="card shadow">
              <div class="card-header">
                <h5>รายการของเสีย ประจำเดือน</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="month-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover month-table"></table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-6">
            <div class="card shadow">
              <div class="card-header">
                <h5>รายการของเสีย ประจำปี</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="year-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover year-table"></table>
                </div>
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
    let path = "/dashboard/waste/download/" + date;
    window.open(path);
  });

  $(document).on("click", ".search-btn", function() {
    let date = ($(".date-select").val() ? $(".date-select").val() : "");
    if (date) {
      $(".waste-data").DataTable().destroy();
      filter_datatable(date);
    } else {
      $(".waste-data").DataTable().destroy();
      filter_datatable();
    }
  });

  function filter_datatable(date) {
    $(".waste-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/dashboard/waste/waste-data",
        type: "POST",
        data: {
          date: date,
        }
      },
      columnDefs: [{
        targets: [0],
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
      "rowCallback": function(row, data, index) {
        let comma = [6]
        for (i = 0; i <= comma.length; i++) {
          let value = (parseInt(data[comma[i]]) !== 0 ? parseFloat(data[comma[i]]).toFixed(2) : 0);
          value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          $(row).find('td:eq(' + comma[i] + ')').html(value)
        }
      },

    });
  };

  axios.post("/dashboard/waste/item-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.item);
      let datas = result.map(item => item.mm);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">สินค้า</th>';
        div += '<th width="30%">จำนวน</th>';
        div += '<th width="20%">หมายเหตุ</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.item + '</td>';
          div += '<td class="text-center">' + Number(v.mm).toLocaleString() + '</td>';
          div += '<td class="text-center">' + v.remark + '</td>';
          div += '</tr>';
        });

        $(".month-table").empty().html(div);
        monthRender("month-chart", subjects, datas);
      } else {
        $(".month-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  var monthChart = new Chart(document.getElementById("month-chart"));

  function monthRender(name, subjects, datas) {
    monthChart.destroy();
    monthChart = new Chart(
      document.getElementById(name),
      config = {
        type: "bar",
        data: {
          labels: subjects,
          datasets: [{
            label: "ประจำเดือน",
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

  axios.post("/dashboard/waste/item-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.item);
      let datas = result.map(item => item.yy);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">สินค้า</th>';
        div += '<th width="20%">จำนวน</th>';
        div += '<th width="30%">ยอดรวม</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.item + '</td>';
          div += '<td class="text-center">' + Number(v.yy).toLocaleString() + '</td>';
          div += '<td class="text-center">' + v.remark + '</td>';
          div += '</tr>';
        });

        $(".year-table").empty().html(div);
        yearRender("year-chart", subjects, datas);
      } else {
        $(".year-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  var yearChart = new Chart(document.getElementById("year-chart"));

  function yearRender(name, subjects, datas) {
    yearChart.destroy();
    yearChart = new Chart(
      document.getElementById(name),
      config = {
        type: "bar",
        data: {
          labels: subjects,
          datasets: [{
            label: "ประจำปี",
            data: datas,
            borderWidth: 1,
            fill: true,
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
        }
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