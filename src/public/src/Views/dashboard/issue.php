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
                <h3 class="text-right"><?php echo (isset($card['exchange']) ? $card['exchange'] : 0) ?></h3>
                <h5 class="text-right">รายการโอนย้าย</h5>
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
            <select class="form-control form-control-sm type-select">
              <option value="">-- ประเภท --</option>
              <?php
              $options = ["นำเข้า", "เบิกออก", "โอนย้าย"];
              foreach ($options as $key => $value) {
                $key++;
                echo "<option value='{$key}'>{$value}</option>";
              }
              ?>
            </select>
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
                  <table class="table table-bordered table-hover issue-data">
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

        <div class="row mb-2">
          <div class="col-xl-6 mb-2">
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

          <div class="col-xl-6 mb-2">
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

  $(document).on("click", ".download-btn", function() {
    let date = ($(".date-select").val() ? $(".date-select").val() : "");
    date = date.replaceAll("/", "+", date);
    let type = ($(".type-select").val() ? $(".type-select").val() : "");
    let path = "/dashboard/issue/download/" + date + "/" + type;
    window.open(path);
  });

  $(document).on("click", ".search-btn", function() {
    let date = ($(".date-select").val() ? $(".date-select").val() : "");
    let type = ($(".type-select").val() ? $(".type-select").val() : "");
    if (date || type) {
      $(".issue-data").DataTable().destroy();
      filter_datatable(date, type);
    } else {
      $(".issue-data").DataTable().destroy();
      filter_datatable();
    }
  });

  function filter_datatable(date, type) {
    $(".issue-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/dashboard/issue/issue-data",
        type: "POST",
        data: {
          date: date,
          type: type,
        },
      },
      columnDefs: [{
        targets: [0, 1, 2],
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
      let subjects = result.map(item => item.item);
      let datas = result.map(item => item.total);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">วัตถุดิบ / สินค้า</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td><a href="/product/edit/' + v.uuid + '" target="_blank">' + v.item + '</a></td>';
          div += '<td class="text-right">' + Number(v.total).toLocaleString() + '</td>';
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
      let subjects = result.map(item => item.item);
      let datas = result.map(item => item.total);

      if (result.length > 0) {
        let div = '<tr>';
        div += '<th width="50%">วัตถุดิบ / สินค้า</th>';
        div += '<th width="50%">จำนวน</th>';
        div += '</tr>';
        result.forEach((v, k) => {
          div += '<tr>';
          div += '<td><a href="/product/edit/' + v.uuid + '" target="_blank">' + v.item + '</a></td>';
          div += '<td class="text-right">' + Number(v.total).toLocaleString() + '</td>';
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
        type: "bar",
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
        type: "bar",
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

  $(".type-select").select2({
    placeholder: "-- ประเภท --",
    allowClear: true,
    width: "100%",
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