@extends('layouts.master-layouts')

@section('title')
    @lang('translation.Setting')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Forms
        @endslot
        @slot('title')
            Setting
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">HPP payment form theme & color Setting</h4>
                    <p>
                        Below Setting enables to personalize the iPOS-HP payment input page with them logo,
                        theme, Pay Now button colour. If the personalization entities values are not given, iPOS-HP will
                        show the <code> default settings </code>.
                    </p>
                    </br>
                    <div class="row">
                        <div class="col-md-12">
                            <form id="hpp-payment-form-theme-setting" class="needs-validation" novalidate
                                id="update-user-settings" method="POST" action="">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="merchantName" class="form-label">Merchant Name</label>
                                            <input type="text" class="form-control" id="merchantName"
                                                placeholder="Enter Merchant Name" name="setting[merchant_name]"
                                                value="{{ $settings['merchant_name'] ?? '' }}" maxlength="35" required>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="logoUrl" class="form-label">Logo URL</label>
                                            <input type="url" class="form-control" id="logoUrl"
                                                placeholder="Enter Logo URL (URL must start with http:// or https://)"
                                                name="setting[logo_url]" value="{{ $settings['logo_url'] ?? '' }}"
                                                pattern="https?://.+" title="URL must start with http:// or https://"
                                                required>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="themeColor" class="form-label">Theme Color</label>
                                            <input type="color" class="form-control" id="themeColor"
                                                name="setting[theme_color]" value="{{ $settings['theme_color'] ?? '' }}"
                                                maxlength="7" required>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea maxlength="150" class="form-control" id="description" placeholder="Enter Description"
                                                name="setting[description]" required>{{ $settings['description'] ?? '' }}</textarea>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payNowButtonText" class="form-label">Pay Now Button Text</label>
                                            <input type="text" class="form-control" id="payNowButtonText"
                                                placeholder="Enter Pay Now Button Text"
                                                value="{{ $settings['pay_now_Button_text'] ?? '' }}"
                                                name="setting[pay_now_Button_text]" maxlength="15" required>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="buttonColor" class="form-label">Button Color</label>
                                            <input type="color" class="form-control" id="buttonColor"
                                                name="setting[button_color]" value="{{ $settings['button_color'] ?? '' }}"
                                                maxlength="7" required>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cancelButtonText" class="form-label">Cancel Button Text</label>
                                            <input type="text" class="form-control" id="cancelButtonText"
                                                placeholder="Enter Cancel Button Text"
                                                value="{{ $settings['cancel_button_text'] ?? '' }}"
                                                name="setting[cancel_button_text]" maxlength="15" required>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="disclaimer" class="form-label">Disclaimer</label>
                                            <textarea maxlength="150" class="form-control" id="disclaimer" rows="4" placeholder="Enter Disclaimer"
                                                name="setting[disclaimer]" required>{{ $settings['disclaimer'] ?? '' }}</textarea>
                                            <div class="valid-feedback">Looks good!</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="submit">Save Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/form-validation.init.js') }}"></script>

    <script>
        $("body").on('click', '#auto-login-url', function(e) {
            e.preventDefault();
            let msg = $(this).data('message');
            var url = $(this).closest('.copy-container').find('.auto-login').val();
            if (url) {
                navigator.clipboard.writeText(url).then(function() {
                    toastr.success(msg, {
                        timeOut: 10000
                    });
                }, function() {
                    toastr.error("Error while Copy", {
                        timeOut: 10000
                    });
                });
            } else {
                toastr.error("No data found to copy", {
                    timeOut: 10000
                });
            }
        });

        $(document).ready(function() {
            $('#oauth-information,#hpp-form,#hpp-payment-form-theme-setting').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                var data = $(this).serialize();
                var url = "{{ route('location.setting.save') }}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function(response) {
                        try {
                            $form.removeClass('was-validated');
                            toastr.success('Saved');
                        } catch (error) {
                            toastr.error(error);
                        }
                        console.log('Data saved successfully:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving data:', error);
                    }
                });
            });
        });
    </script>
@endsection
