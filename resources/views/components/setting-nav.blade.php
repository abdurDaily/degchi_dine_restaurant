@canAny(['theme-customization', 'general-setting', 'email-setting', 'pusher-setting'])
    <li class="nav-item">
        <a class="nav-link menu-link" href="#settingsNav" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="settingsNav">
        <i class="ri-settings-3-line"></i> <span data-key="t-maps">Settings </span>
        </a>
        <div class="collapse menu-dropdown" id="settingsNav">
            <ul class="nav nav-sm flex-column">
                @can('theme-customization')
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link" data-key="t-google"  data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
                            Theme Customizer
                        </a>
                    </li>
                @endcan
                @canAny(['general-setting', 'email-setting', 'pusher-setting'])
                <li class="nav-item">
                    <a href="#systemSettingNav" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="systemSettingNav" data-key="t-level-1.2">
                        System Setting
                    </a>
                    <div class="collapse menu-dropdown" id="systemSettingNav">
                        <ul class="nav nav-sm flex-column">
                            @can('general-setting')
                            <li class="nav-item">
                                <a href="{{route('general-setting')}}" class="nav-link" data-key="t-general-setting"> General Setting
                                </a>
                            </li>
                            @endcan
                            @can('email-setting')
                            <li class="nav-item">
                                <a href="{{route('email-setting')}}" class="nav-link" data-key="t-email-setting"> Email Setting
                                </a>
                            </li>
                            @endcan
                            @can('pusher-setting')
                            <li class="nav-item">
                                <a href="{{route('pusher-setting')}}" class="nav-link" data-key="t-pusher-setting"> Pusher Setting
                                </a>
                            </li>
                             @endcan
                            @can('general-setting')
                            <li class="nav-item">
                                <a href="{{ route('seo-setting') }}" class="nav-link" data-key="t-seo-setting"> SEO & Tracking
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sslcommerz-setting') }}" class="nav-link" data-key="t-sslcommerz-setting"> SSLCommerz Setting
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcan
            </ul>
        </div>
    </li>
@endcan
