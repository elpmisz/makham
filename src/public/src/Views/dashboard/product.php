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
                <h3 class="text-right"><?php echo (isset($card['product']) ? $card['product'] : 0) ?></h3>
                <h5 class="text-right">วัตถุดิบ/สินค้า</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-info text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['location']) ? $card['location'] : 0) ?></h3>
                <h5 class="text-right">คลัง</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-success text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['store']) ? $card['store'] : 0) ?></h3>
                <h5 class="text-right">สถานที่จัดเก็บ</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-danger text-white shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['customer']) ? $card['customer'] : 0) ?></h3>
                <h5 class="text-right">ลูกค้า</h5>
              </div>
            </div>
          </div>
          <div class="col-xl mb-2">
            <div class="card bg-warning shadow">
              <div class="card-body">
                <h3 class="text-right"><?php echo (isset($card['supplier']) ? $card['supplier'] : 0) ?></h3>
                <h5 class="text-right">ผู้จัดจำหน่าย</h5>
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
                      <tr class="table-primary">
                        <th width="10%">#</th>
                        <th width="10%">รหัส</th>
                        <th width="20%">ชื่อ</th>
                        <th width="10%">MIN</th>
                        <th width="10%">นำเข้า</th>
                        <th width="10%">นำออก</th>
                        <th width="10%">คงเหลือ</th>
                        <th width="10%">หน่วยนับ</th>
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

        let min = parseInt(data[3]);
        let remain = parseInt(data[6]);

        let comma = [4, 5, 6]
        for (i = 0; i <= comma.length; i++) {
          let value = (parseInt(data[comma[i]]) !== 0 ? parseFloat(data[comma[i]]).toFixed(2) : 0);
          value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          $(row).find('td:eq(' + comma[i] + ')').html(value)
        }

        if (remain < min) {
          $(row).addClass("table-danger");
        }

        if (remain === min) {
          $(row).addClass("table-info");
        }
      },
    });
  };
</script>