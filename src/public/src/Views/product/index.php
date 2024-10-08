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
            <select class="form-control form-control-sm location-select"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm store-select"></select>
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

  $(document).on("change", ".location-select, .store-select", function() {
    let location = ($(".location-select").val() ? $(".location-select").val() : "");
    let store = ($(".store-select").val() ? $(".store-select").val() : "");
    if (location || store) {
      $(".product-data").DataTable().destroy();
      filter_datatable(location, store);
    } else {
      $(".product-data").DataTable().destroy();
      filter_datatable();
    }
  });

  function filter_datatable(location, store) {
    $(".product-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/product/product-data",
        type: "POST",
        data: {
          location: location,
          store: store
        }
      },
      columnDefs: [{
        targets: [0, 1, 7],
        className: "text-center",
      }, {
        targets: [3, 4, 5, 6],
        className: "text-right",
      }, {
        targets: [2],
        className: "text-left",
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

  $(".location-select").select2({
    placeholder: "-- คลัง --",
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

  $(".store-select").select2({
    placeholder: "-- สถานที่จัดเก็บ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/product/store-select",
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