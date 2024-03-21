<?php
$menu = "dashboard";
$page = "dashboard-sale";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\DashboardSale;

$DASHBOARD = new DashboardSale();

$card = $DASHBOARD->sale_card();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายงานขาย</h4>
      </div>
      <div class="card-body">

        <div class="row mb-2">
          <div class="col-xl-3 mb-2">
            <div class="card bg-primary text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['total']) ? $card['total'] : 0) ?></h3>
                <h5 class="text-right">ยอดขายทั้งหมด</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-info text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['dd']) ? $card['dd'] : 0) ?></h3>
                <h5 class="text-right">ยอดขายรายวัน</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-success text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['mm']) ? $card['mm'] : 0) ?></h3>
                <h5 class="text-right">ยอดขายรายเดือน</h5>
              </div>
            </div>
          </div>
          <div class="col-xl-3 mb-2">
            <div class="card bg-danger text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['yy']) ? $card['yy'] : 0) ?></h3>
                <h5 class="text-right">ยอดขายรายปี</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="card shadow">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover sale-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">ผู้ทำรายการ</th>
                        <th width="10%">ลูกค้า</th>
                        <th width="30%">รายละเอียด</th>
                        <th width="10%">ส่งเสริมการขาย</th>
                        <th width="10%">ภาษีมูลค่าเพิ่ม</th>
                        <th width="10%">จำนวนเงิน</th>
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
          <div class="col-xl-7">
            <div class="card shadow">
              <div class="card-header">
                <h5>สินค้าขายดี ประจำเดือน</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="month-chart"></canvas>
                </div>
                <table class="table table-sm table-hover month-table"></table>
              </div>
            </div>
          </div>

          <div class="col-xl-5">
            <div class="card shadow">
              <div class="card-header">
                <h5>สินค้าขายดี ประจำปี</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="year-chart"></canvas>
                </div>
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

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  function filter_datatable() {
    $(".sale-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/dashboard/sale/sale-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 5],
        className: "text-center",
      }, {
        targets: [6],
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

  axios.post("/dashboard/sale/month-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.product_name);
      let datas = result.map(item => item.total);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">สินค้า</th>';
        div += '<th width="20%">จำนวน</th>';
        div += '<th width="30%">ยอดรวม</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.product + '</td>';
          div += '<td class="text-center">' + Number(v.amount).toLocaleString() + '</td>';
          div += '<td class="text-right">' + Number(v.total).toLocaleString() + '</td>';
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


  axios.post("/dashboard/sale/year-data")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.product_name);
      let datas = result.map(item => item.total);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">สินค้า</th>';
        div += '<th width="20%">จำนวน</th>';
        div += '<th width="30%">ยอดรวม</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.product + '</td>';
          div += '<td class="text-center">' + Number(v.amount).toLocaleString() + '</td>';
          div += '<td class="text-right">' + Number(v.total).toLocaleString() + '</td>';
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

  var yearChart = new Chart(
    document.getElementById("year-chart"), {
      type: "doughnut",
    }
  );

  function yearRender(name, subjects, datas) {
    yearChart.destroy();
    yearChart = new Chart(
      document.getElementById(name),
      config = {
        type: "doughnut",
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

  var monthChart = new Chart(
    document.getElementById("month-chart"), {
      type: "bar",
    }
  );

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