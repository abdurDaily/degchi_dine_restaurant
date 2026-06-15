<!-- Theme Settings -->
<form action="javascript:void(0)" method="post" class="border-0 offcanvas offcanvas-end" tabindex="-1"
    id="theme-settings-offcanvas">
    <div class="p-3 d-flex align-items-center bg-primary bg-gradient offcanvas-header">
        <h5 class="m-0 text-white me-2">Theme Customizer</h5>

        <button type="button" class="btn-close btn-close-white ms-auto" id="customizerclose-btn"
            data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="p-0 offcanvas-body">
        <div data-simplebar class="h-100">
            <div class="p-4">
                <h6 class="mb-0 fw-semibold text-uppercase">Layout</h6>
                <p class="text-muted">Choose your layout</p>

                <div class="row gy-3">
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input id="customizer-layout01" name="data-layout" type="radio" value="vertical"
                                class="form-check-input">
                            <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                for="customizer-layout01">
                                <span class="gap-1 d-flex h-100">
                                    <span class="flex-shrink-0">
                                        <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                            <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="p-1 bg-light d-block"></span>
                                            <span class="p-1 mt-auto bg-light d-block"></span>
                                        </span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13">Vertical</h5>
                    </div>
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input id="customizer-layout02" name="data-layout" type="radio" value="horizontal"
                                class="form-check-input">
                            <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                for="customizer-layout02">
                                <span class="gap-1 d-flex h-100 flex-column">
                                    <span class="gap-1 p-1 bg-light d-flex align-items-center">
                                        <span class="p-1 rounded d-block bg-primary-subtle me-1"></span>
                                        <span class="p-1 px-2 pb-0 d-block bg-primary-subtle ms-auto"></span>
                                        <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                    </span>
                                    <span class="p-1 bg-light d-block"></span>
                                    <span class="p-1 mt-auto bg-light d-block"></span>
                                </span>
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13">Horizontal</h5>
                    </div>
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input id="customizer-layout03" name="data-layout" type="radio" value="twocolumn"
                                class="form-check-input">
                            <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                for="customizer-layout03">
                                <span class="gap-1 d-flex h-100">
                                    <span class="flex-shrink-0">
                                        <span class="gap-1 bg-light d-flex h-100 flex-column">
                                            <span class="p-1 mb-2 d-block bg-primary-subtle"></span>
                                            <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                        </span>
                                    </span>
                                    <span class="flex-shrink-0">
                                        <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="p-1 bg-light d-block"></span>
                                            <span class="p-1 mt-auto bg-light d-block"></span>
                                        </span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13">Two Column</h5>
                    </div>
                    <!-- end col -->

                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input id="customizer-layout04" name="data-layout" type="radio" value="semibox"
                                class="form-check-input">
                            <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                for="customizer-layout04">
                                <span class="gap-1 d-flex h-100">
                                    <span class="flex-shrink-0 p-1">
                                        <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                            <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1">
                                        <span class="pt-1 d-flex h-100 flex-column pe-2">
                                            <span class="p-1 bg-light d-block"></span>
                                            <span class="p-1 mt-auto bg-light d-block"></span>
                                        </span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13">Semi Box</h5>
                    </div>
                    <!-- end col -->
                </div>

                <div class="mt-4 mb-3 form-check form-switch form-switch-md">
                    <input type="checkbox" class="form-check-input" id="sidebarUserProfile">
                    <label class="form-check-label" for="sidebarUserProfile">Sidebar User Profile Avatar</label>
                </div>

                <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Theme</h6>
                <p class="text-muted">Choose your suitable Theme.</p>

                <div class="row">
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme01" name="data-theme" type="radio" value="default"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme01">
                                <img src="{{asset('assets/images/demos/default.png')}}" alt="" class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Default</h5>
                    </div>
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme02" name="data-theme" type="radio" value="saas"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme02">
                                <img src="{{asset('assets/images/demos/saas.png')}}" alt="" class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Sass</h5>
                    </div>
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme03" name="data-theme" type="radio" value="corporate"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme03">
                                <img src="{{asset('assets/images/demos/corporate.png')}}" alt=""
                                    class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Corporate</h5>
                    </div>
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme04" name="data-theme" type="radio" value="galaxy"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme04">
                                <img src="{{asset('assets/images/demos/galaxy.png')}}" alt="" class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Galaxy</h5>
                    </div>
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme05" name="data-theme" type="radio" value="material"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme05">
                                <img src="{{asset('assets/images/demos/material.png')}}" alt=""
                                    class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Material</h5>
                    </div>
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme06" name="data-theme" type="radio" value="creative"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme06">
                                <img src="{{asset('assets/images/demos/creative.png')}}" alt=""
                                    class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Creative</h5>
                    </div>
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme07" name="data-theme" type="radio" value="minimal"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme07">
                                <img src="{{asset('assets/images/demos/minimal.png')}}" alt="" class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Minimal</h5>
                    </div>
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme08" name="data-theme" type="radio" value="modern"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme08">
                                <img src="{{asset('assets/images/demos/modern.png')}}" alt="" class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Modern</h5>
                    </div>
                    <!-- end col -->
                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme09" name="data-theme" type="radio" value="interactive"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme09">
                                <img src="{{asset('assets/images/demos/interactive.png')}}" alt=""
                                    class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Interactive</h5>
                    </div><!-- end col -->

                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme10" name="data-theme" type="radio" value="classic"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme10">
                                <img src="{{asset('assets/images/demos/classic.png')}}" alt="" class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Classic</h5>
                    </div><!-- end col -->

                    <div class="col-6">
                        <div class="form-check card-radio">
                            <input id="customizer-theme11" name="data-theme" type="radio" value="vintage"
                                class="form-check-input">
                            <label class="p-0 form-check-label" for="customizer-theme11">
                                <img src="{{asset('assets/images/demos/vintage.png')}}" alt="" class="img-fluid">
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13 fw-medium">Vintage</h5>
                    </div><!-- end col -->
                </div>

                <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Color Scheme</h6>
                <p class="text-muted">Choose Light or Dark Scheme.</p>

                <div class="colorscheme-cardradio">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-mode-light"
                                    value="light">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="layout-mode-light">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Light</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check card-radio dark">
                                <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-mode-dark"
                                    value="dark">
                                <label class="p-0 form-check-label avatar-md w-100 bg-dark material-shadow"
                                    for="layout-mode-dark">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-white bg-opacity-10 d-flex h-100 flex-column">
                                                <span
                                                    class="p-1 px-2 mb-2 bg-white rounded d-block bg-opacity-10"></span>
                                                <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                                <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                                <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-white bg-opacity-10 d-block"></span>
                                                <span class="p-1 mt-auto bg-white bg-opacity-10 d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Dark</h5>
                        </div>
                    </div>
                </div>

                <div id="sidebar-visibility">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Visibility</h6>
                    <p class="text-muted">Choose show or Hidden sidebar.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-visibility"
                                    id="sidebar-visibility-show" value="show">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-visibility-show">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0 p-1">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="pt-1 d-flex h-100 flex-column pe-2">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Show</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-visibility"
                                    id="sidebar-visibility-hidden" value="hidden">
                                <label class="p-0 px-2 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-visibility-hidden">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-grow-1">
                                            <span class="px-2 pt-1 d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Hidden</h5>
                        </div>
                    </div>
                </div>

                <div id="layout-width">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Layout Width</h6>
                    <p class="text-muted">Choose Fluid or Boxed layout.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-width"
                                    id="layout-width-fluid" value="fluid">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="layout-width-fluid">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Fluid</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-width"
                                    id="layout-width-boxed" value="boxed">
                                <label class="p-0 px-2 form-check-label avatar-md w-100 material-shadow"
                                    for="layout-width-boxed">
                                    <span class="gap-1 d-flex h-100 border-start border-end">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Boxed</h5>
                        </div>
                    </div>
                </div>

                <div id="layout-position">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Layout Position</h6>
                    <p class="text-muted">Choose Fixed or Scrollable Layout Position.</p>

                    <div class="btn-group radio" role="group">
                        <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-fixed"
                            value="fixed">
                        <label class="btn btn-light w-sm" for="layout-position-fixed">Fixed</label>

                        <input type="radio" class="btn-check" name="data-layout-position"
                            id="layout-position-scrollable" value="scrollable">
                        <label class="btn btn-light w-sm ms-0" for="layout-position-scrollable">Scrollable</label>
                    </div>
                </div>
                <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Topbar Color</h6>
                <p class="text-muted">Choose Light or Dark Topbar Color.</p>

                <div class="row">
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-light"
                                value="light">
                            <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                for="topbar-color-light">
                                <span class="gap-1 d-flex h-100">
                                    <span class="flex-shrink-0">
                                        <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                            <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="p-1 bg-light d-block"></span>
                                            <span class="p-1 mt-auto bg-light d-block"></span>
                                        </span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13">Light</h5>
                    </div>
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-dark"
                                value="dark">
                            <label class="p-0 form-check-label avatar-md w-100 material-shadow" for="topbar-color-dark">
                                <span class="gap-1 d-flex h-100">
                                    <span class="flex-shrink-0">
                                        <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                            <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="p-1 bg-primary d-block"></span>
                                            <span class="p-1 mt-auto bg-light d-block"></span>
                                        </span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <h5 class="mt-2 text-center fs-13">Dark</h5>
                    </div>
                </div>

                <div id="sidebar-size">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Size</h6>
                    <p class="text-muted">Choose a size of Sidebar.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-size"
                                    id="sidebar-size-default" value="lg">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-size-default">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Default</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-size"
                                    id="sidebar-size-compact" value="md">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-size-compact">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Compact</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-size"
                                    id="sidebar-size-small" value="sm">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-size-small">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 mb-2 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Small (Icon View)</h5>
                        </div>

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-size"
                                    id="sidebar-size-small-hover" value="sm-hover">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-size-small-hover">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 mb-2 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Small Hover View</h5>
                        </div>
                    </div>
                </div>

                <div id="sidebar-view">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar View</h6>
                    <p class="text-muted">Choose Default or Detached Sidebar view.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-style"
                                    id="sidebar-view-default" value="default">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-view-default">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Default</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-style"
                                    id="sidebar-view-detached" value="detached">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-view-detached">
                                    <span class="d-flex h-100 flex-column">
                                        <span class="gap-1 p-1 px-2 bg-light d-flex align-items-center">
                                            <span class="p-1 rounded d-block bg-primary-subtle me-1"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle ms-auto"></span>
                                            <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                        </span>
                                        <span class="gap-1 p-1 px-2 d-flex h-100">
                                            <span class="flex-shrink-0">
                                                <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                    <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                    <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                    <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="p-1 px-2 mt-auto bg-light d-block"></span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Detached</h5>
                        </div>
                    </div>
                </div>
                <div id="sidebar-color">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Color</h6>
                    <p class="text-muted">Choose a color of Sidebar.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio" data-bs-toggle="collapse"
                                data-bs-target="#collapseBgGradient.show">
                                <input class="form-check-input" type="radio" name="data-sidebar"
                                    id="sidebar-color-light" value="light">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-color-light">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-white border-end d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Light</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio" data-bs-toggle="collapse"
                                data-bs-target="#collapseBgGradient.show">
                                <input class="form-check-input" type="radio" name="data-sidebar" id="sidebar-color-dark"
                                    value="dark">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="sidebar-color-dark">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-primary d-flex h-100 flex-column">
                                                <span
                                                    class="p-1 px-2 mb-2 bg-white rounded d-block bg-opacity-10"></span>
                                                <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                                <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                                <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Dark</h5>
                        </div>
                        <div class="col-4">
                            <button class="p-0 overflow-hidden border btn btn-link avatar-md w-100 collapsed"
                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseBgGradient"
                                aria-expanded="false" aria-controls="collapseBgGradient">
                                <span class="gap-1 d-flex h-100">
                                    <span class="flex-shrink-0">
                                        <span class="gap-1 p-1 bg-vertical-gradient d-flex h-100 flex-column">
                                            <span class="p-1 px-2 mb-2 bg-white rounded d-block bg-opacity-10"></span>
                                            <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                            <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                            <span class="p-1 px-2 pb-0 bg-white d-block bg-opacity-10"></span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="p-1 bg-light d-block"></span>
                                            <span class="p-1 mt-auto bg-light d-block"></span>
                                        </span>
                                    </span>
                                </span>
                            </button>
                            <h5 class="mt-2 text-center fs-13">Gradient</h5>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="collapse" id="collapseBgGradient">
                        <div class="flex-wrap gap-2 p-2 px-3 rounded d-flex img-switch bg-light">

                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar"
                                    id="sidebar-color-gradient" value="gradient">
                                <label class="p-0 form-check-label avatar-xs rounded-circle"
                                    for="sidebar-color-gradient">
                                    <span class="avatar-title rounded-circle bg-vertical-gradient"></span>
                                </label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar"
                                    id="sidebar-color-gradient-2" value="gradient-2">
                                <label class="p-0 form-check-label avatar-xs rounded-circle"
                                    for="sidebar-color-gradient-2">
                                    <span class="avatar-title rounded-circle bg-vertical-gradient-2"></span>
                                </label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar"
                                    id="sidebar-color-gradient-3" value="gradient-3">
                                <label class="p-0 form-check-label avatar-xs rounded-circle"
                                    for="sidebar-color-gradient-3">
                                    <span class="avatar-title rounded-circle bg-vertical-gradient-3"></span>
                                </label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar"
                                    id="sidebar-color-gradient-4" value="gradient-4">
                                <label class="p-0 form-check-label avatar-xs rounded-circle"
                                    for="sidebar-color-gradient-4">
                                    <span class="avatar-title rounded-circle bg-vertical-gradient-4"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="sidebar-img">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Images</h6>
                    <p class="text-muted">Choose a image of Sidebar.</p>

                    <div class="flex-wrap gap-2 d-flex img-switch">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-none"
                                value="none">
                            <label class="h-auto p-0 form-check-label avatar-sm" for="sidebarimg-none">
                                <span
                                    class="w-auto avatar-md bg-light d-flex align-items-center justify-content-center">
                                    <i class="ri-close-fill fs-20"></i>
                                </span>
                            </label>
                        </div>

                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-01"
                                value="img-1">
                            <label class="h-auto p-0 form-check-label avatar-sm" for="sidebarimg-01">
                                <img src="{{asset('assets/images/sidebar/img-1.jpg')}}" alt=""
                                    class="w-auto avatar-md object-fit-cover">
                            </label>
                        </div>

                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-02"
                                value="img-2">
                            <label class="h-auto p-0 form-check-label avatar-sm" for="sidebarimg-02">
                                <img src="{{asset('assets/images/sidebar/img-2.jpg')}}" alt=""
                                    class="w-auto avatar-md object-fit-cover">
                            </label>
                        </div>
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-03"
                                value="img-3">
                            <label class="h-auto p-0 form-check-label avatar-sm" for="sidebarimg-03">
                                <img src="{{asset('assets/images/sidebar/img-3.jpg')}}" alt=""
                                    class="w-auto avatar-md object-fit-cover">
                            </label>
                        </div>
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-sidebar-image" id="sidebarimg-04"
                                value="img-4">
                            <label class="h-auto p-0 form-check-label avatar-sm" for="sidebarimg-04">
                                <img src="{{asset('assets/images/sidebar/img-4.jpg')}}" alt=""
                                    class="w-auto avatar-md object-fit-cover">
                            </label>
                        </div>
                    </div>
                </div>

                <div id="sidebar-color">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Primary Color</h6>
                    <p class="text-muted">Choose a color of Primary.</p>

                    <div class="flex-wrap gap-2 d-flex">
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-01"
                                value="default">
                            <label class="p-0 form-check-label avatar-xs" for="themeColor-01"></label>
                        </div>
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-02"
                                value="green">
                            <label class="p-0 form-check-label avatar-xs" for="themeColor-02"></label>
                        </div>
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-03"
                                value="purple">
                            <label class="p-0 form-check-label avatar-xs" for="themeColor-03"></label>
                        </div>
                        <div class="form-check sidebar-setting card-radio">
                            <input class="form-check-input" type="radio" name="data-theme-colors" id="themeColor-04"
                                value="blue">
                            <label class="p-0 form-check-label avatar-xs" for="themeColor-04"></label>
                        </div>
                    </div>
                </div>

                {{-- <div id="preloader-menu">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Preloader</h6>
                    <p class="text-muted">Choose a preloader.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-preloader"
                                    id="preloader-view-custom" value="enable">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="preloader-view-custom">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                    <!-- <div id="preloader"> -->
                                    <div id="status" class="d-flex align-items-center justify-content-center">
                                        <div class="m-auto spinner-border text-primary avatar-xxs" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <!-- </div> -->
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Enable</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-preloader"
                                    id="preloader-view-none" value="disable">
                                <label class="p-0 form-check-label avatar-md w-100 material-shadow"
                                    for="preloader-view-none">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Disable</h5>
                        </div>
                    </div>

                </div> --}}
                <!-- end preloader-menu -->

                <div id="body-img" style="display: none;">
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Background Image</h6>
                    <p class="text-muted">Choose a body background image.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-body-image" id="body-img-none"
                                    value="none">
                                <label class="p-0 form-check-label avatar-md w-100" data-body-image="none"
                                    for="body-img-none">
                                    <span class="gap-1 d-flex h-100">
                                        <span class="flex-shrink-0">
                                            <span class="gap-1 p-1 bg-light d-flex h-100 flex-column">
                                                <span class="p-1 px-2 mb-2 rounded d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                                <span class="p-1 px-2 pb-0 d-block bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="p-1 bg-light d-block"></span>
                                                <span class="p-1 mt-auto bg-light d-block"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">None</h5>
                        </div>
                        <!-- end col -->
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-body-image" id="body-img-one"
                                    value="img-1">
                                <label class="p-0 form-check-label avatar-md w-100" data-body-image="img-1"
                                    for="body-img-one">
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">One</h5>
                        </div>
                        <!-- end col -->

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-body-image" id="body-img-two"
                                    value="img-2">
                                <label class="p-0 form-check-label avatar-md w-100" data-body-image="img-2"
                                    for="body-img-two">
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Two</h5>
                        </div>
                        <!-- end col -->

                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-body-image" id="body-img-three"
                                    value="img-3">
                                <label class="p-0 form-check-label avatar-md w-100" data-body-image="img-3"
                                    for="body-img-three">
                                </label>
                            </div>
                            <h5 class="mt-2 text-center fs-13">Three</h5>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
            </div>
        </div>

    </div>
    <div class="p-3 text-center offcanvas-footer border-top">
        <div class="row">
            <div class="col-6">
                <button type="button" class="btn btn-light w-100" id="reset-layout">Reset</button>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>
    $(document).ready(function() {
            $('#theme-settings-offcanvas').on('submit', function(e) {
                e.preventDefault();
                $('#preloader').show();
                var formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('theme.customize') }}",
                    type: 'POST',
                    data: formData,
                    success: res => {
                        $('#preloader').hide();
                        if (res.status == 'success') {
                            Command: toastr[res.status](res.message);
                        }
                    },
                    error: err => {
                        $('#preloader').hide();
                        Command: toastr['error'](err.message);
                    }
                })
            })
        })
</script>
@endpush
