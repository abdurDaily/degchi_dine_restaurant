<x-admin-master>
    @section('title', 'Dashboard')
    @section('content')
        <x-breadcrumb></x-breadcrumb>
        <div class="row">
            <div class="col-xl-12">
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm rounded-3 bg-primary-soft me-3">
                                        <i class="ri-shopping-cart-line fs-22 text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1">Total Orders</p>
                                        <h4 class="mb-0">{{ $ordersCount ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm rounded-3 bg-success-soft me-3">
                                        <i class="ri-user-star-line fs-22 text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1">Total Members</p>
                                        <h4 class="mb-0">{{ $membersCount ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card crm-widget">
                    <div class="p-0 card-body">
                        <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0">
                            <div class="col">
                                <div class="px-3 py-4">
                                    <h5 class="text-muted text-uppercase fs-13">Campaign Sent <i class="align-middle ri-arrow-up-circle-line text-success fs-18 float-end"></i></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-space-ship-line display-6 text-muted cfs-22"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="197">197</span></h2>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            <div class="col">
                                <div class="px-3 py-4 mt-3 mt-md-0">
                                    <h5 class="text-muted text-uppercase fs-13">Annual Profit <i class="align-middle ri-arrow-up-circle-line text-success fs-18 float-end"></i></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-exchange-dollar-line display-6 text-muted cfs-22"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h2 class="mb-0 cfs-22">$<span class="counter-value" data-target="489.4">489.4</span>k</h2>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            <div class="col">
                                <div class="px-3 py-4 mt-3 mt-md-0">
                                    <h5 class="text-muted text-uppercase fs-13">Lead Conversation <i class="align-middle ri-arrow-down-circle-line text-danger fs-18 float-end"></i></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-pulse-line display-6 text-muted cfs-22"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="32.89">32.89</span>%</h2>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            <div class="col">
                                <div class="px-3 py-4 mt-3 mt-lg-0">
                                    <h5 class="text-muted text-uppercase fs-13">Daily Average Income <i class="align-middle ri-arrow-up-circle-line text-success fs-18 float-end"></i></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-trophy-line display-6 text-muted cfs-22"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h2 class="mb-0 cfs-22">$<span class="counter-value" data-target="1596.5">1,596.5</span></h2>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            <div class="col">
                                <div class="px-3 py-4 mt-3 mt-lg-0">
                                    <h5 class="text-muted text-uppercase fs-13">Annual Deals <i class="align-middle ri-arrow-down-circle-line text-danger fs-18 float-end"></i></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-service-line display-6 text-muted cfs-22"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="2659">2,659</span></h2>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                        </div><!-- end row -->
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        </div><!-- end row -->
    @endsection
</x-admin-master>
