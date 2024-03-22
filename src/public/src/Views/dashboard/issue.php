<?php
$menu = "dashboard";
$page = "dashboard-issue";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\DashboardIssue;

$DASHBOARD = new DashboardIssue();

$card = $DASHBOARD->issue_card();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายงานนำสินค้าเข้า - ออก</h4>
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
                <h3 class="text-right"><?php echo (isset($card['approve']) ? $card['approve'] : 0) ?></h3>
                <h5 class="text-right">รายการรอตรวจสอบ</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-success text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['income']) ? $card['income'] : 0) ?></h3>
                <h5 class="text-right">รายการนำเข้า</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-danger text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['outcome']) ? $card['outcome'] : 0) ?></h3>
                <h5 class="text-right">รายการเบิกออก</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="card shadow">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover issue-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">ประเภท</th>
                        <th width="10%">ผู้ทำรายการ</th>
                        <th width="60">รายละเอียด</th>
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
                <h5>ยอดรวมสินค้านำเข้า</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="income-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover income-table"></table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-6">
            <div class="card shadow">
              <div class="card-header">
                <h5>ยอดรวมสินค้าเบิกออก</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="outcome-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover outcome-table"></table>
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
    $(".issue-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/dashboard/issue/issue-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1],
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

  axios.post("/dashboard/issue/income")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.product_name);
      let datas = result.map(item => item.income);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">วัตถุดิบ / สินค้า</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.product + '</td>';
          div += '<td class="text-right">' + Number(v.income).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".income-table").empty().html(div);
        incomeRender("income-chart", subjects, datas);
      } else {
        $(".income-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  axios.post("/dashboard/issue/outcome")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.product_name);
      let datas = result.map(item => item.outcome);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">วัตถุดิบ / สินค้า</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.product + '</td>';
          div += '<td class="text-right">' + Number(v.outcome).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".outcome-table").empty().html(div);
        outcomeRender("outcome-chart", subjects, datas);
      } else {
        $(".outcome-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  var incomeChart = new Chart(document.getElementById("income-chart"));

  function incomeRender(name, subjects, datas) {
    incomeChart.destroy();
    incomeChart = new Chart(
      document.getElementById(name),
      config = {
        type: "pie",
        data: {
          labels: subjects,
          datasets: [{
            label: "นำเข้า",
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

  var outcomeChart = new Chart(document.getElementById("outcome-chart"));

  function outcomeRender(name, subjects, datas) {
    outcomeChart.destroy();
    outcomeChart = new Chart(
      document.getElementById(name),
      config = {
        type: "doughnut",
        data: {
          labels: subjects,
          datasets: [{
            label: "เบิกออก",
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