<!-- Topbar start -->
<nav class="top-bar" data-topbar role="navigation">
    <ul class="title-area">
        <li class="name">
            <h1><a href="{{ URL::to('/') }}"><img src="{{ URL::asset('img/app-icon.png') }}"></img> Aplikasi Rapor Kurikulum 2013</a></h1>
        </li>
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
    </ul>

    <section class="top-bar-section">
        <ul class="right">
            <li class="active"><a><b><i class="fa fa-user"></i> {{ str_limit(Auth::user()->nama, 32) }}</b></a></li>
            <div class="show-for-small-only">
                <li><a href="#">Jump 1</a></li>
                <li><a href="#">Jump 2</a></li> 
            </div>
        </ul>
    </section>
</nav>
<!-- Topbar end -->