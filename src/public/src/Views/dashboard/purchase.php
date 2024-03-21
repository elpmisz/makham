<?php
$menu = "dashboard";
$page = "dashboard-purchase";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\DashboardPurchase;

$DASHBOARD = new DashboardPurchase();

$card = $DASHBOARD->purchase_card();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายงานผลิต</h4>
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
                <h5 class="text-right">ยอดผลิตรายวัน</h5>
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

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="card shadow">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover purchase-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">ผู้ทำรายการ</th>
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
                <h5>ยอดผลิตแยกตามเครื่องจักร ประจำเดือน</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="machine-month-chart"></canvas>
                </div>
                <table class="table table-sm table-hover machine-month-table"></table>
              </div>
            </div>
          </div>

          <div class="col-xl-7">
            <div class="card shadow">
              <div class="card-header">
                <h5>ยอดผลิตแยกตามเครื่องจักร ประจำปี</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="machine-year-chart"></canvas>
                </div>
                <table class="table table-sm table-hover machine-year-table"></table>
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

  axios.post("/dashboard/purchase/machine-month-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.machine_name);
      let datas = result.map(item => item.mm);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">เครื่องจักร</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.machine_name + '</td>';
          div += '<td class="text-right">' + Number(v.mm).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".machine-month-table").empty().html(div);
        machineMonthRender("machine-month-chart", subjects, datas);
      } else {
        $(".machine-month-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  axios.post("/dashboard/purchase/machine-year-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.machine_name);
      let datas = result.map(item => item.yy);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">เครื่องจักร</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.machine_name + '</td>';
          div += '<td class="text-right">' + Number(v.yy).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".machine-year-table").empty().html(div);
        machineYearRender("machine-year-chart", subjects, datas);
      } else {
        $(".machine-year-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  var machineMonthChart = new Chart(
    document.getElementById("machine-month-chart"), {
      type: "doughnut",
    }
  );

  function machineMonthRender(name, subjects, datas) {
    machineMonthChart.destroy();
    machineMonthChart = new Chart(
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

  var machineYearChart = new Chart(
    document.getElementById("machine-year-chart"), {
      type: "bar",
    }
  );

  function machineYearRender(name, subjects, datas) {
    machineYearChart.destroy();
    machineYearChart = new Chart(
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
</script>