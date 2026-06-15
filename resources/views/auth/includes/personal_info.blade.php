<div class="tab-pane active" id="personalInfo" role="tabpanel">
    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3 card-title">Info</h4>
                    <div class="table-responsive">
                       @include('auth.includes.user_info')
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
        <!--end col-->
        <div class="col-xxl-9">
            <div class="card">
                <div class="card-header">
                    <h4>Personal Information</h4>
                </div>
                <div class="card-body">
                    <form id="profile-update-form" action="javascript:void(0)" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 row align-items-center">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Name:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 row align-items-center">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Email</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 row align-items-center">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Image:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name='image' class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 btn btn-primary">Update</button>
                    </form>
                </div>
                <!--end card-body-->
            </div><!-- end card -->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div>
