<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <!-- Cover  -->
        <div class="sidebar-brand">
            <!-- <a href="index.html">Stisla</a> -->
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <!-- <a href="index.html">St</a> -->
        </div>
        <ul class="sidebar-menu">
            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('home') }}">
                    <i class="fa-solid fa-house-chimney"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="{{ Request::is('sumber-dana') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('sumber-dana') }}">
                    <i class="fa-sharp fa-solid fa-coins"></i>
                    <span>Sumber Dana</span>
                </a>
            </li>
            <li class="{{ Request::is('transaksi') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('transaksi') }}">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>Transaksi</span>
                </a>
            </li>
        </ul>

        <!-- <div class="hide-sidebar-mini mt-4 mb-4 p-3">
            <a href="https://getstisla.com/docs"
                class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Documentation
            </a>
        </div> -->
    </aside>
</div>
