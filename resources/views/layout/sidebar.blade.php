<!-- Brand Logo -->
<a href="/dashboard" class="brand-link">
  <img src="img/ktb_fuso.png"
    alt="AdminLTE Logo"
    class="brand-image img-circle elevation-3"
    style="opacity:.8; margin-left:-5px;">

  <span class="brand-text font-weight-light">Portal Supplier</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
  <!-- Sidebar user panel (optional) -->
  <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center" style="margin-left: 10px;">
    <div class="image">
      <i class="nav-icon fas fa-user" style="color: white;"></i>
    </div>
    <div class="info">
      <a href="#" class="d-block">Naufal</h6></a>
    </div>
  </div>

  <!-- Sidebar Menu -->
  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
      <li class="nav-item">
        <a href="/dashboard" class="nav-link">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>
            Dashboard
          </p>
        </a>
      </li>

      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-database"></i>
          <p>
            Master Data
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="/user" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Staff KTB</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/vendor" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Vendor / Supplier</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/material" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Material</p>
            </a>
          </li>
        </ul>
      </li>

      <!-- Purchasing -->
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-shopping-cart"></i>
          <p>
            Purchasing
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="/purchase-request" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Purchase Request</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/purchase-order" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Purchase Order</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/purchase-order-approval" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Approval Purchase Order</p>
            </a>
          </li>
        </ul>
      </li>

      <!-- Surat Jalan -->
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-truck"></i>
          <p>
            Surat Jalan
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="/surat-jalan" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Surat Jalan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/surat-jalan-approval" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Surat Jalan Approval</p>
            </a>
          </li>
        </ul>
      </li>

      <!-- Penerimaan Barang -->
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-box-open"></i>
          <p>
            Penerimaan Barang
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="/good-receipt" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Good Receipt</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/good-receipt-history" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Riwayat Penerimaan Barang</p>
            </a>
          </li>
        </ul>
      </li>


      <!-- Purchase Invoice -->
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-box-open"></i>
          <p>
            Purchase Ivoice
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="/purchase-invoice" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Purchase Invoice</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/purchase-invoice-history" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Daftar Pembayaran</p>
            </a>
          </li>
        </ul>
      </li>

      <!-- Manajemen User -->
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-users-cog"></i>
          <p>
            Manajemen User
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Roles</p>
            </a>
          </li>
        </ul>
      </li>

  </nav>
  <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->