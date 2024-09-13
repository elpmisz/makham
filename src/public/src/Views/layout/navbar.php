<nav class="navbar navbar-expand-xl sticky-top shadow w-100">
  <div class="container-fluid">

    <a class="navbar-brand d-none d-xl-block" id="sidebarCollapse" href="javascript:void(0)">
      <i class="fa fa-bars pr-2"></i>
      <span class="font-weight-bold"><?php echo $system_name  ?></span>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
      <i class="fas fa-bars pr-2"></i>
      <span class="font-weight-bold"><?php echo $system_name ?></span>
    </button>

    <div class="collapse navbar-collapse" id="navbar-menu">

      <ul class="navbar-nav ml-3 d-xl-none">
        <li class=" nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" data-toggle="dropdown">
            <i class="fa fa-list pr-2"></i>
            <span class="font-weight-bold">รายงาน</span>
            <i class="fas fa-caret-down pl-2"></i>
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/dashboard/purchase">
              <i class="fa fa-address-book pr-2"></i>
              <span class="font-weight-bold">รายงานสั่งผลิต</span>
            </a>
            <a class="dropdown-item" href="/dashboard/issue">
              <i class="fa fa-key pr-2"></i>
              <span class="font-weight-bold">รายงานนำสินค้าเข้า - ออก</span>
            </a>
            <a class="dropdown-item" href="/dashboard/waste">
              <i class="fa fa-key pr-2"></i>
              <span class="font-weight-bold">รายงานสรุปของเสีย</span>
            </a>
            <a class="dropdown-item" href="/dashboard/product">
              <i class="fa fa-key pr-2"></i>
              <span class="font-weight-bold">รายงานวัตถุดิบ / สินค้า</span>
            </a>
          </div>
        </li>
        <li class=" nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" data-toggle="dropdown">
            <i class="fa fa-list pr-2"></i>
            <span class="font-weight-bold">ข้อมูลส่วนตัว</span>
            <i class="fas fa-caret-down pl-2"></i>
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/user/profile">
              <i class="fa fa-address-book pr-2"></i>
              <span class="font-weight-bold">รายละเอียด</span>
            </a>
            <a class="dropdown-item" href="/user/change">
              <i class="fa fa-key pr-2"></i>
              <span class="font-weight-bold">เปลี่ยนรหัสผ่าน</span>
            </a>
          </div>
        </li>
        <li class=" nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" data-toggle="dropdown">
            <i class="fa fa-list pr-2"></i>
            <span class="font-weight-bold">บริการ</span>
            <i class="fas fa-caret-down pl-2"></i>
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/purchase">
              <i class="fa fa-desktop pr-2"></i>
              <span class="font-weight-bold">ใบสั่งผลิต</span>
            </a>
            <a class="dropdown-item" href="/issue">
              <i class="fa fa-desktop pr-2"></i>
              <span class="font-weight-bold">ใบนำสินค้าเข้า - ออก</span>
            </a>
            <a class="dropdown-item" href="/waste">
              <i class="fa fa-desktop pr-2"></i>
              <span class="font-weight-bold">ใบสรุปของเสีย</span>
            </a>
          </div>
        </li>
        <li class=" nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" data-toggle="dropdown">
            <i class="fa fa-list pr-2"></i>
            <span class="font-weight-bold">ตั้งค่าระบบ</span>
            <i class="fas fa-caret-down pl-2"></i>
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/system">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">ระบบ</span>
            </a>
            <a class="dropdown-item" href="/user">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">ผู้ใช้งาน</span>
            </a>
            <a class="dropdown-item" href="/auth">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">จัดการสิทธิ์</span>
            </a>
            <a class="dropdown-item" href="/product">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">วัตถุดิบ / สินค้า</span>
            </a>
            <a class="dropdown-item" href="/customer">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">ลูกค้า</span>
            </a>
            <a class="dropdown-item" href="/supplier">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">ผู้จัดจำหน่าย</span>
            </a>
            <a class="dropdown-item" href="/location">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">คลัง</span>
            </a>
            <a class="dropdown-item" href="/store">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">สถานที่จัดเก็บ</span>
            </a>
            <a class="dropdown-item" href="/unit">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">หน่วยนับ</span>
            </a>
            <a class="dropdown-item" href="/brand">
              <i class="fa fa-gear pr-2"></i>
              <span class="font-weight-bold">ยี่ห้อ</span>
            </a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link logout-btn" href="javascript:void(0)">
            <i class="fa fa-sign-out pr-2"></i>
            <span class="font-weight-bold">ออกจากระบบ</span>
          </a>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto d-none d-xl-block">
        <li class="nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" data-toggle="dropdown">
            <span class="font-weight-bold"><?php echo $firstname ?></span>
            <i class="fas fa-caret-down pl-3"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="/user/profile">
              <i class="fa fa-address-book pr-2"></i>
              <span class="font-weight-bold">รายละเอียด</span>
            </a>
            <a class="dropdown-item" href="/user/change">
              <i class="fa fa-key pr-2"></i>
              <span class="font-weight-bold">เปลี่ยนรหัสผ่าน</span>
            </a>
            <a class="dropdown-item logout-btn" href="javascript:void(0)">
              <i class="fa fa-sign-out pr-2"></i>
              <span class="font-weight-bold">ออกจากระบบ</span>
            </a>
          </div>
        </li>
      </ul>

    </div>
  </div>
</nav>