<div class="tab-pane fade" id="passwordChange" role="tabpanel">
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
                    <h4>Change Password</h4>
                </div>
                <div class="card-body">
                    <form id="password-update-form" action="javascript:void(0)" method="POST" >
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 row align-items-center">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Old Password:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="password" name="current_password" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 row align-items-center">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">New Password</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 row align-items-center">
                                    <div class="col-sm-4">
                                        <label class="col-form-label">Confirm Password</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="password" name="password_confirmation" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 btn btn-primary">Update Password</button>
                    </form>
                </div>
                <!--end card-body-->
            </div><!-- end card -->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div>
