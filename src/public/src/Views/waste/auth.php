<?php
$menu = "service";
$page = "service-waste";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">สิทธิ์ใช้งาน</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <button class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#modal-auth">
              <i class="fas fa-plus pr-2"></i>เพิ่ม
            </button>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover auth-data">
                <thead>
                  <tr>
                    <th width="10%">#</th>
                    <th width="10%">สิทธิ์</th>
                    <th width="50%">ชื่อ</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <div class="row justify-content-center mb-2">
          <div class="col-xl-3 mb-2">
            <a href="/purchase" class="btn btn-sm btn-danger btn-block">
              <i class="fa fa-arrow-left pr-2"></i>กลับ
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-auth" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <form action="/waste/auth" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
          <div class="row mb-2">
            <label class="col-xl-3 col-form-label text-right">รายชื่อ</label>
            <div class="col-xl-6">
              <select class="form-control form-control-sm user-select" name="user_id" required></select>
              <div class="invalid-feedback">
                กรุณาเลือกข้อมูล!
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <label class="col-xl-3 col-form-label text-right">สิทธิ์</label>
            <div class="col-xl-8">
              <div class="row pb-2">
                <div class="col-xl-4">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="type" value="1" required>
                    <span class="text-success">จัดการระบบ</span>
                  </label>
                </div>
                <div class="col-xl-4">
                  <label class="form-check-label px-3 py-2">
                    <input class="form-check-input" type="radio" name="type" value="2" required>
                    <span class="text-danger">ผู้อนุมัติ</span>
                  </label>
                </div>
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

  function filter_datatable() {
    $(".auth-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/waste/auth-data",
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

  $(".user-select").select2({
    placeholder: "-- รายชื่อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/waste/user-select",
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

  $(document).on("click", ".auth-delete", function(e) {
    let id = $(this).prop("id");
    e.preventDefault();
    Swal.fire({
      title: "ยืนยันที่จะทำรายการ?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "ตกลง",
      cancelButtonText: "ปิด",
    }).then((result) => {
      if (result.value) {
        axios.post("/waste/auth-delete", {
          id: id
        }).then((res) => {
          let result = res.data;
          if (result === 200) {
            location.reload()
          } else {
            location.reload()
          }
        }).catch((error) => {
          console.log(error);
        });
      } else {
        return false;
      }
    })
  });
</script>