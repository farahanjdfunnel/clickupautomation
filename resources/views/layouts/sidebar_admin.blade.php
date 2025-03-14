{{-- <li  class="nav-item dropdown">
    <a href="/" class="nav-link arrow-none">
        <i class="bx bx-home-circle"></i>
        <span key="t-starter-page">@lang('translation.Dashboard')</span>
    </a>
</li> --}}
<li  class="nav-item dropdown">
    <a href="{{ route('admin.setting') }}" class="nav-link arrow-none">
        <i class="bx bx-wrench"></i>
        <span key="t-starter-page">@lang('translation.Setting')</span>
    </a>
</li>
<li  class="nav-item dropdown">
    <a href="{{ route('admin.merchant.index') }}" class="nav-link arrow-none">
        <i class="bx bx-user"></i>
        <span key="t-starter-page">@lang('translation.Merchants')</span>
    </a>
</li>