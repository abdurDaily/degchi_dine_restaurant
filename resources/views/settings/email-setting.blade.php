<x-admin-master>

    @section('content')
        <div class="container-fluid">
            <div class="card" id="email-sidenav">
                <div class="email-setting-wrap ">
                    <form id="emailSettingForm" action="javascript:void(0)" method="post">
                        @method('post')
                        @csrf
                        <input type="hidden" class="email">
                        <div class="card-header">
                            <h3 class="h5">{{ __('Email Settings') }}</h3>
                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex">
                                <div class="col-sm-6 col-12 mb-3">

                                    <div class="form-group col switch-width">
                                        <label class="col-form-label">Select Email Setting</label>

                                        <select id="email_setting" class="form-control"name="email">
                                            <option value="custom" {{ ($emailType?->value == 'custom' || $emailType == null) ? 'selected' : '' }}> Custom </option>
                                            <option value="smtp" {{ ($emailType?->value == 'smtp') ? 'selected' : '' }}>SMTP</option>
                                            <option value="gmail" {{ ($emailType?->value == 'gmail') ? 'selected' : '' }}>Gmail</option>
                                            <option value="outlook" {{ ($emailType?->value == 'outlook') ? 'selected' : '' }}>Outlook/Office 365</option>
                                            <option value="yahoo" {{ ($emailType?->value == 'yahoo') ? 'selected' : '' }}>Yahoo</option>
                                            <option value="sendgrid" {{ ($emailType?->value == 'sendgrid') ? 'selected' : '' }}>SendGrid</option>
                                            <option value="amazon" {{ ($emailType?->value == 'amazon') ? 'selected' : '' }}>Amazon SES </option>
                                            <option value="mailgun" {{ ($emailType?->value == 'mailgun') ? 'selected' : '' }}>Mailgun</option>
                                            <option value="smtp.com" {{ ($emailType?->value == 'smtp.com') ? 'selected' : '' }}>SMTP.com</option>
                                            <option value="zohomail" {{ ($emailType?->value == 'zohomail') ? 'selected' : '' }}>Zoho Mail</option>
                                            <option value="mandrill" {{ ($emailType?->value == 'mandrill') ? 'selected' : '' }}>Mandrill</option>
                                            <option value="mailtrap" {{ ($emailType?->value == 'mailtrap') ? 'selected' : '' }}>Mailtrap</option>
                                            <option value="sparkpost" {{ ($emailType?->value == 'sparkpost') ? 'selected' : '' }}>SparkPost</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" id="getfields">
                                </div>
                            </div>

                        </div>

                        <div class="card-footer d-flex justify-content-between flex-wrap "style="gap:10px">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#email_setting').select2({
                    placeholder: 'Select Email Setting',
                });
                function getEmailFields(emailType){
                    $.ajax({
                        type: 'GET',
                        url: "{{route('email-setting')}}",
                        data: {
                            emailType: emailType
                        },
                        success: function (data) {
                            $('#getfields').html(data);
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                };
                getEmailFields($('#email_setting').val());

                $(document).on('change', '#email_setting', function () {
                    console.log($(this).val());
                    getEmailFields($(this).val());
                });

                $('#emailSettingForm').on('submit', function (e) {
                    e.preventDefault();
                    $('#preloader').show();
                    $(this).find('span.text-danger').empty();
                    $(this).find('.is-invalid').removeClass('is-invalid');
                    data = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: "{{route('email-setting.store')}}",
                        data: data,
                        success: data => {
                            $('#preloader').hide();
                            if (data.status == 'success') {
                                toastr.success(data.message);
                            }
                            if (data.errors) {
                                toastr.error(data.errors);
                            }
                        },
                        error: err => {
                            $('#preloader').hide();
                            if(err.status == 422){
                                $.each(err.responseJSON.errors, function (i, error) {
                                    let el = $(document).find('#emailSettingForm [name="'+i+'"]');
                                    el.nextAll('span.text-danger').empty().text(error[0]);
                                    el.addClass('is-invalid');
                                });
                                Command: toastr["error"]("Form Input Error");
                            }
                        }

                    })
                })
            });
        </script>
    @endpush
</x-admin-master>
