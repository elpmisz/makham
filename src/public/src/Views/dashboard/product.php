<?php
$menu = "dashboard";
$page = "dashboard-product";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\DashboardProduct;

$DASHBOARD = new DashboardProduct();

$card = $DASHBOARD->product_card();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายงานวัตถุดิบ / สินค้า</h4>
      </div>
      <div class="card-body">

        <div class="row mb-2">
          <div class="col-xl mb-2">
            <div class="card bg-primary text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['total']) ? $card['total'] : 0) ?></h3>
                <h5 class="text-right">รายการทั้งหมด</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-info text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['cool']) ? $card['cool'] : 0) ?></h3>
                <h5 class="text-right">วัตถุดิบ</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-success text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['fg']) ? $card['fg'] : 0) ?></h3>
                <h5 class="text-right">สินค้าสำเร็จรูป</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-danger text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['mx']) ? $card['mx'] : 0) ?></h3>
                <h5 class="text-right">ส่วนผสม</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-warning shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['pk']) ? $card['pk'] : 0) ?></h3>
                <h5 class="text-right">บรรจุภัณฑ์</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="card shadow">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover product-data">
                    <thead>
                      <tr>
                        <th width="10%">สถานะ</th>
                        <th width="10%">รหัส</th>
                        <th width="20%">วัตถุดิบ / สินค้า</th>
                        <th width="10%">หมวดหมู่</th>
                        <th width="10%">สถานที่</th>
                        <th width="10%">นำเข้า (รวม)</th>
                        <th width="10%">เบิกออก (รวม)</th>
                        <th width="10%">คงเหลือ</th>
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
                <h5>จำนวนสินค้าแยกตามหมวดหมู่</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="category-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover category-table"></table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-6">
            <div class="card shadow">
              <div class="card-header">
                <h5>จำนวนสินค้าแยกตามสถานที่</h5>
              </div>
              <div class="card-body">
                <div class="col-xl-12 mb-2">
                  <canvas id="location-chart"></canvas>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-hover location-table"></table>
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
    $(".product-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/dashboard/product/product-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 3, 4],
        className: "text-center",
      }, {
        targets: [5, 6, 7],
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
        let comma = [5, 6, 7]
        for (i = 0; i <= comma.length; i++) {
          let value = (parseInt(data[comma[i]]) !== 0 ? parseFloat(data[comma[i]]).toFixed(2) : 0);
          value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          $(row).find('td:eq(' + comma[i] + ')').html(value)
        }
      },
    });
  };

  axios.post("/dashboard/product/category")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.category);
      let datas = result.map(item => item.total);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">ชื่อ</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.category + '</td>';
          div += '<td class="text-right">' + Number(v.total).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".category-table").empty().html(div);
        categoryRender("category-chart", subjects, datas);
      } else {
        $(".category-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  var categoryChart = new Chart(document.getElementById("category-chart"));

  function categoryRender(name, subjects, datas) {
    categoryChart.destroy();
    categoryChart = new Chart(
      document.getElementById(name),
      config = {
        type: "bar",
        data: {
          labels: subjects,
          datasets: [{
            label: "หมวดหมู่",
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

  axios.post("/dashboard/product/location")
    .then((res) => {
      let result = res.data;
      let subjects = result.map(item => item.location);
      let datas = result.map(item => item.total);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">วัตถุดิบ / สินค้า</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td>' + v.location + '</td>';
          div += '<td class="text-right">' + Number(v.total).toLocaleString() + '</td>';
          div += '</tr>';
        });

        $(".location-table").empty().html(div);
        locationRender("location-chart", subjects, datas);
      } else {
        $(".location-table").empty().html();
      }
    }).catch((error) => {
      console.log(error);
    });

  var locationChart = new Chart(document.getElementById("location-chart"));

  function locationRender(name, subjects, datas) {
    locationChart.destroy();
    locationChart = new Chart(
      document.getElementById(name),
      config = {
        type: "bar",
        data: {
          labels: subjects,
          datasets: [{
            label: "สถานที่",
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
</script>