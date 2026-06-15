<div class="row">
    <div class="row">
        <div class="form-group col-md-4 mb-3">
            <label for="mail_driver" class="form-label">Mail Driver</label>
            <input type="text" name="mail_driver" id="mail_driver" class="form-control" placeholder="Enter Mail Driver" value="{{ isset($settings['mail_driver']) ? $settings['mail_driver'] : '' }}">
            <span class="text-danger"></span>
        </div>

        @if ($email_setting == 'custom')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" class="form-control" placeholder="Enter Mail Host" value="{{ isset($settings['mail_host']) ? $settings['mail_host'] : '' }}">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'gmail')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.gmail.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'outlook')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.office365.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'yahoo')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.mail.yahoo.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'sendgrid')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.sendgrid.net" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'amazon')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="email-smtp.us-east-1.amazonaws.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'mailgun')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.mailgun.org" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'smtp.com')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.smtp.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'zohomail')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.zoho.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'mailtrap')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.mailtrap.io" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'mandrill')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.mandrillapp.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'smtp')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" class="form-control" placeholder="Enter Mail Host" value="{{ isset($settings['mail_host']) ? $settings['mail_host'] : '' }}">
                <span class="text-danger"></span>
            </div>
        @elseif ($email_setting == 'sparkpost')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_host" class="form-label">Mail Host</label>
                <input type="text" name="mail_host" id="mail_host" value="smtp.sparkpostmail.com" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @endif

        @if ($email_setting == 'custom' || $email_setting == 'smtp')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_port" class="form-label">Mail Port</label>
                <input type="text" name="mail_port" id="mail_port" class="form-control" placeholder="Enter Mail Port" value="{{ isset($settings['mail_port']) ? $settings['mail_port'] : '' }}">
                <span class="text-danger"></span>
            </div>
        @else
            <div class="form-group col-md-4 mb-3">
                <label for="mail_port" class="form-label">Mail Port</label>
                <input type="text" name="mail_port" id="mail_port" value="587" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @endif

        <div class="form-group col-md-4 mb-3">
            <label for="mail_password" class="form-label">Mail Password</label>
            <input type="password" name="mail_password" id="mail_password" class="form-control" placeholder="Enter Mail Password" value="{{ isset($settings['mail_password']) ? $settings['mail_password'] : '' }}">
            <span class="text-danger"></span>
        </div>

        @if ($email_setting == 'custom' || $email_setting == 'smtp')
            <div class="form-group col-md-4 mb-3">
                <label for="mail_encryption" class="form-label">Mail Encryption</label>
                <input type="text" name="mail_encryption" id="mail_encryption" class="form-control" placeholder="Enter Mail Encryption" value="{{ isset($settings['mail_encryption']) ? $settings['mail_encryption'] : '' }}">
                <span class="text-danger"></span>
            </div>
        @else
            <div class="form-group col-md-4 mb-3">
                <label for="mail_encryption" class="form-label">Mail Encryption</label>
                <input type="text" name="mail_encryption" id="mail_encryption" value="TLS" class="form-control" readonly="readonly">
                <span class="text-danger"></span>
            </div>
        @endif

        <div class="form-group col-md-4 mb-3">
            <label for="mail_from_address" class="form-label">Mail From Address</label>
            <input type="email" name="mail_from_address" id="mail_from_address" class="form-control" placeholder="Enter Mail From Address" value="{{ isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '' }}">
            <span class="text-danger"></span>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label for="mail_from_name" class="form-label">Mail From Name</label>
            <input type="text" name="mail_from_name" id="mail_from_name" class="form-control" placeholder="Enter Mail From Name" value="{{ isset($settings['mail_from_name']) ? $settings['mail_from_name'] : '' }}">
            <span class="text-danger"></span>
        </div>
    </div>
</div>
