<?php
$menu = "setting";
$page = "setting-product";
include_once(__DIR__ . "/../layout/header.php");

?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">วัตถุดิบ / สินค้า</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <button class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#modal-upload">
              <i class="fas fa-upload pr-2"></i>นำข้อมูลเข้า
            </button>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/product/download" class="btn btn-danger btn-sm btn-block">
              <i class="fas fa-download pr-2"></i>นำข้อมูลออก
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/product/create" class="btn btn-primary btn-sm btn-block">
              <i class="fas fa-plus pr-2"></i>เพิ่ม
            </a>
          </div>
        </div>

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm category-select"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm location-select"></select>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover product-data">
                <thead>
                  <tr class="table-primary">
                    <th width="10%">#</th>
                    <th width="10%">รหัส</th>
                    <th width="20%">ชื่อ</th>
                    <th width="10%">หมวดหมู่</th>
                    <th width="10%">สถานที่</th>
                    <th width="10%">ราคาซื้อ</th>
                    <th width="10%">ราคาขาย</th>
                    <th width="10%">MIN</th>
                    <th width="10%">คงเหลือ</th>
                    <th width="10%">วันที่ล่าสุด</th>
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

<div class="modal fade" id="modal-upload" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form action="/product/upload" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
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

  $(document).on("change", ".category-select, .location-select", function() {
    let category = ($(".category-select").val() ? $(".category-select").val() : "");
    let location = ($(".location-select").val() ? $(".location-select").val() : "");
    if (category || location) {
      $(".product-data").DataTable().destroy();
      filter_datatable(category, location);
    } else {
      $(".product-data").DataTable().destroy();
      filter_datatable();
    }
  });

  function filter_datatable(category, location) {
    $(".product-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/product/product-data",
        type: "POST",
        data: {
          category: category,
          location: location
        }
      },
      columnDefs: [{
        targets: [0, 1, 3, 4],
        className: "text-center",
      }, {
        targets: [5, 6, 7, 8],
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

        let min = parseInt(data[7]);
        let remain = parseInt(data[8]);

        let comma = [5, 6, 7, 8]
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

  $(".category-select").select2({
    placeholder: "-- หมวดหมู่ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/category-select",
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

  $(".location-select").select2({
    placeholder: "-- สถานที่ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/location-select",
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
</script>