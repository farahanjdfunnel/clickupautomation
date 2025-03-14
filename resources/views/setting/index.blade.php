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
        {{-- CRM OAUTH SECTION --}}
        <div class="col-xl-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">CRM OAuth Information</h4>
                    <code class="fw-bold fs-5">Redirect URI - add while creating app</code>
                    <p class="card-title-desc">
                        {{ route('crm.oauth_callback','crm') }}
                    </p>
                    <code class="fw-bold fs-5">Scopes - select while creating app</code>
                    <p class="card-title-desc">
                        {{ \CRM::$scopes }}</p>
                    <form class="needs-validation" novalidate id="oauth-information">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="crm_client_id" class="form-label">Client ID</label>
                                    <input type="text" class="form-control" id="crm_client_id"
                                        placeholder="Enter marketplace app client id" name="setting[crm_client_id]"
                                        value="{{ $settings['crm_client_id'] ?? '' }}" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="crm_secret_id" class="form-label">Client Secret</label>
                                    <input type="text" class="form-control" id="crm_secret_id"
                                        placeholder="Enter marketplace app client secret" name="setting[crm_client_secret]"
                                        value="{{ $settings['crm_client_secret'] ?? '' }}" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
         {{-- Clickup Connection --}}
         <div class="col-xl-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ClickUp OAuth</h4>
        
                    @if (!$user->clickupauth)
                        <a href="{{ route('auth.clickup') }}" class="btn btn-primary">
                            Connect to ClickUp
                        </a>
                    @else
                        <div class="alert alert-success mt-2">
                            <strong>Connected to ClickUp</strong>
                        </div>
        
                        <form action="{{ route('auth.clickup.disconnect') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                Disconnect
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- end card -->
    </div> <!-- end col -->
    </div>


    <!-- end row -->
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-editor.init.js') }}"></script>
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
            $('#oauth-information,#hpp-form,#location-page-view,#header-color,#onboarding-form').on('submit',
                function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var data = $(this).serialize();

                    var url = '{{ route('admin.setting.save') }}';
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        success: function(response) {
                            try {
                                $form.removeClass('was-validated');
                                toastr.success('Saved');
                                if ($form.is('#header-color')) {
                                    var color = $('#color-input').val();
                                    $('#page-topbar').css('background-color', color)
                                }
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
            $('#update-admin-profile').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                var formData = $(this).serialize();
                var token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.update.profile') }}",
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    data: formData, // Send serialized form data
                    success: function(result) {
                        if (result.status === 'Success') {
                            $form.removeClass('was-validated');
                            toastr.success(result.message);
                        } else {
                            toastr.error('Error: ' + result.message);
                            console.error('Error:', result.message);
                        }
                    },
                    error: function(xhr) {
                        var error = JSON.parse(xhr.responseText);
                        toastr.error('Error: ' + error.message);
                        console.error('Error:', error.message);
                    }
                });
            });

        });
    </script>
@endsection
