@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/settings/general.saml_title') }}
@parent
@stop

@section('header_right')
<a href="{{ route('settings.index') }}" class="btn btn-default"> {{ trans('general.back') }}</a>
@stop


{{-- Page content --}}
@section('content')

<style>
    .checkbox label {
        padding-right: 40px;
    }
</style>


{{ Form::open(['method' => 'POST', 'files' => false, 'autocomplete' => 'false', 'class' => 'form-horizontal', 'role' => 'form']) }}
<!-- CSRF Token -->
{{csrf_field()}}

<!-- this is a hack to prevent Chrome from trying to autocomplete fields -->
<input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" />
<input type="password" name="password_fake" id="password_fake" value="" style="display:none;" />



<div class="row">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">


        <div class="panel box box-default">
            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-sign-in-alt"></i> {{ trans('admin/settings/general.saml') }}
                </h2>
            </div>
            <div class="box-body">

                <!-- Enable SAML -->
                <div class="form-group{{ $errors->has('saml_integration') ? ' error' : '' }}">
                    <div class="col-md-3">
                        <strong>{{ trans('admin/settings/general.saml_integration') }}</strong>
                    </div>
                    <div class="col-md-9">

                        <label class="form-control{{ config('app.lock_passwords') === true ? ' form-control--disabled': '' }}">
                            {{ Form::checkbox('saml_enabled', '1', old('saml_enabled', $setting->saml_enabled), ['class' => config('app.lock_passwords') === true ? 'disabled ': '',  config('app.lock_passwords') === true ? 'disabled ': '', ]) }}
                            {{ trans('admin/settings/general.saml_enabled') }}
                        </label>

                        {!! $errors->first('saml_integration', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                        @if (config('app.lock_passwords') === true)
                        <p class="text-warning"><i class="fas fa-lock"></i> {{ trans('general.feature_disabled') }}</p>
                        @endif
                    </div>




                    @if ($setting->saml_enabled)
                    <div class="col-md-9 col-md-offset-3">
                        <!-- SAML SP Details -->
                        <!-- SAML SP Entity ID -->
                        {{ Form::label('saml_sp_entitiyid', trans('admin/settings/general.saml_sp_entityid')) }}
                        {{ Form::text('saml_sp_entitiyid', config('app.url'), ['class' => 'form-control', 'readonly']) }}
                        <br>
                        <!-- SAML SP ACS -->
                        {{ Form::label('saml_sp_acs_url', trans('admin/settings/general.saml_sp_acs_url')) }}
                        {{ Form::text('saml_sp_acs_url', route('saml.acs'), ['class' => 'form-control', 'readonly']) }}
                        <br>
                        <!-- SAML SP SLS -->
                        {{ Form::label('saml_sp_sls_url', trans('admin/settings/general.saml_sp_sls_url')) }}
                        {{ Form::text('saml_sp_sls_url', route('saml.sls'), ['class' => 'form-control', 'readonly']) }}
                        <br>
                        <!-- SAML SP Certificate -->
                        @if (!empty($setting->saml_sp_x509cert))
                        {{ Form::label('saml_sp_x509cert', trans('admin/settings/general.saml_sp_x509cert')) }}
                        {{ Form::textarea('saml_sp_x509cert', $setting->saml_sp_x509cert, ['class' => 'form-control', 'wrap' => 'off', 'readonly']) }}
                        <br>
                        @endif
                        <!-- SAML SP Metadata URL -->
                        {{ Form::label('saml_sp_metadata_url', trans('admin/settings/general.saml_sp_metadata_url')) }}
                        {{ Form::text('saml_sp_metadata_url', route('saml.metadata'), ['class' => 'form-control', 'readonly']) }}
                        <br>
                        <p class="help-block">
                            <a href="{{ route('saml.metadata') }}" target="_blank" class="btn btn-default" style="margin-right: 5px;">{{ trans('admin/settings/general.saml_download') }}</a>
                        </p>
                    </div>
                    @endif
                    {!! $errors->first('saml_enabled', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}

                </div>


                <!-- SAML IdP Metadata -->
                <div class="form-group {{ $errors->has('saml_idp_metadata') ? 'error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_idp_metadata', trans('admin/settings/general.saml_idp_metadata')) }}
                    </div>
                    <div class="col-md-9">
                        {{ Form::textarea('saml_idp_metadata', old('saml_idp_metadata', $setting->saml_idp_metadata), ['class' => 'form-control','placeholder' => 'https://example.com/idp/metadata', 'wrap' => 'off', $setting->demoMode]) }}
                        {!! $errors->first('saml_idp_metadata', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}<br>
                        <button type="button" class="btn btn-default" id="saml_idp_metadata_upload_btn" {{ $setting->demoMode }}>{{ trans('button.select_file') }}</button>
                        <input type="file" class="js-uploadFile" id="saml_idp_metadata_upload" data-maxsize="{{ Helper::file_upload_max_size() }}" accept="text/xml,application/xml" style="display:none; max-width: 90%" {{ $setting->demoMode }}>

                        <p class="help-block">{{ trans('admin/settings/general.saml_idp_metadata_help') }}</p>
                    </div>
                </div>

                <!-- SAML Attribute Mapping Username -->
                <div class="form-group {{ $errors->has('saml_attr_mapping_username') ? 'error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_attr_mapping_username', trans('admin/settings/general.saml_attr_mapping_username')) }}
                    </div>
                    <div class="col-md-9">
                        {{ Form::text('saml_attr_mapping_username', old('saml_attr_mapping_username', $setting->saml_attr_mapping_username), ['class' => 'form-control','placeholder' => '', $setting->demoMode]) }}
                        <p class="help-block">{{ trans('admin/settings/general.saml_attr_mapping_username_help') }}</p>
                        {!! $errors->first('saml_attr_mapping_username', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                    </div>
                </div>

                <div class="form-group {{ $errors->has('saml_group_attribute') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('saml_group_attribute', "SAML Group Attribute") }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::text('saml_group_attribute', old('saml_group_attribute', $setting->saml_group_attribute), ['class' => 'form-control','placeholder' => '', $setting->demoMode]) }}
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('saml_name_attribute') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('saml_name_attribute', "SAML Name Attribute") }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::text('saml_name_attribute', old('saml_name_attribute', $setting->saml_name_attribute), ['class' => 'form-control','placeholder' => '', $setting->demoMode]) }}
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('saml_email_attribute') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('saml_email_attribute', "SAML EMail Attribute") }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::text('saml_email_attribute', old('saml_email_attribute', $setting->saml_email_attribute), ['class' => 'form-control','placeholder' => '', $setting->demoMode]) }}
                            </div>
                        </div>

                <div class="form-group{{ $errors->has('group') ? ' has-error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_admin_snipe_group', "Admin Group") }}
                    </div>

                    <div class="col-md-8">

                        @if ($groups->count())
                        @if ((Config::get('app.lock_passwords') || (!Auth::user()->isSuperUser())))
                        <ul>
                            @foreach ($groups as $id => $group)
                            {!! '<li>'.e($group).'</li>' !!}
                            @endforeach
                        </ul>


                        <span class="help-block">{{ trans('admin/users/general.group_memberships_helpblock') }}</span>
                        @else
                        <div class="controls">
                            <select name="saml_admin_snipe_group" aria-label="saml_admin_snipe_group" id="saml_admin_snipe_group" class="form-control select2">
                                <option value="">{{ trans('admin/settings/general.no_default_group') }}</option>
                                @foreach ($groups as $id => $group)
                                <option value="{{ $id }}" {{ $setting->saml_admin_snipe_group == $id ? 'selected' : '' }}>
                                    {{ $group }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @else
                        <p>No groups have been created yet. Visit <code>Admin Settings > Permission Groups</code> to add one.</p>
                        @endif

                    </div>
                </div>

                <div class="form-group {{ $errors->has('saml_admin_saml_group') ? 'error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_admin_saml_group', "Admin SAML Group") }}
                    </div>
                    <div class="col-md-9">
                        {{ Form::text('saml_admin_saml_group', old('saml_admin_saml_group', $setting->saml_admin_saml_group), ['class' => 'form-control','placeholder' => '', $setting->demoMode]) }}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('group') ? ' has-error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_manager_snipe_group', "Manager Group") }}
                    </div>

                    <div class="col-md-8">

                        @if ($groups->count())
                        @if ((Config::get('app.lock_passwords') || (!Auth::user()->isSuperUser())))
                        <ul>
                            @foreach ($groups as $id => $group)
                            {!! '<li>'.e($group).'</li>' !!}
                            @endforeach
                        </ul>


                        <span class="help-block">{{ trans('admin/users/general.group_memberships_helpblock') }}</span>
                        @else
                        <div class="controls">
                            <select name="saml_manager_snipe_group" aria-label="saml_manager_snipe_group" id="saml_manager_snipe_group" class="form-control select2">
                                <option value="">{{ trans('admin/settings/general.no_default_group') }}</option>
                                @foreach ($groups as $id => $group)
                                <option value="{{ $id }}" {{ $setting->saml_manager_snipe_group == $id ? 'selected' : '' }}>
                                    {{ $group }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @else
                        <p>No groups have been created yet. Visit <code>Admin Settings > Permission Groups</code> to add one.</p>
                        @endif

                    </div>
                </div>

                <div class="form-group {{ $errors->has('saml_manager_saml_group') ? 'error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_manager_saml_group', "Manager SAML Group") }}
                    </div>
                    <div class="col-md-9">
                        {{ Form::text('saml_manager_saml_group', old('saml_manager_saml_group', $setting->saml_manager_saml_group), ['class' => 'form-control','placeholder' => '', $setting->demoMode]) }}
                    </div>
                </div>

                <div class="form-group{{ $errors->has('group') ? ' has-error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_user_snipe_group', "User Group") }}
                    </div>

                    <div class="col-md-8">

                        @if ($groups->count())
                        @if ((Config::get('app.lock_passwords') || (!Auth::user()->isSuperUser())))
                        <ul>
                            @foreach ($groups as $id => $group)
                            {!! '<li>'.e($group).'</li>' !!}
                            @endforeach
                        </ul>


                        <span class="help-block">{{ trans('admin/users/general.group_memberships_helpblock') }}</span>
                        @else
                        <div class="controls">
                            <select name="saml_user_snipe_group" aria-label="saml_user_snipe_group" id="saml_user_snipe_group" class="form-control select2">
                                <option value="">{{ trans('admin/settings/general.no_default_group') }}</option>
                                @foreach ($groups as $id => $group)
                                <option value="{{ $id }}" {{ $setting->saml_user_snipe_group == $id ? 'selected' : '' }}>
                                    {{ $group }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @else
                        <p>No groups have been created yet. Visit <code>Admin Settings > Permission Groups</code> to add one.</p>
                        @endif

                    </div>
                </div>

                <div class="form-group {{ $errors->has('saml_user_saml_group') ? 'error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_user_saml_group', "User SAML Group") }}
                    </div>
                    <div class="col-md-9">
                        {{ Form::text('saml_user_saml_group', old('saml_user_saml_group', $setting->saml_user_saml_group), ['class' => 'form-control','placeholder' => '', $setting->demoMode]) }}
                    </div>
                </div>

                <!-- SAML Force Login -->
                <div class="form-group">
                    <div class="col-md-3">
                        <strong>{{ trans('admin/settings/general.saml_forcelogin_label') }}</strong>
                    </div>
                    <div class="col-md-9">
                        <label class="form-control{{ config('app.lock_passwords') === true ? ' form-control--disabled': '' }}">
                            {{ Form::checkbox('saml_forcelogin', '1', old('saml_forcelogin', $setting->saml_forcelogin),['class' =>  $setting->demoMode, $setting->demoMode]) }}
                            {{ trans('admin/settings/general.saml_forcelogin') }}
                        </label>
                        <p class="help-block">{{ trans('admin/settings/general.saml_forcelogin_help') }}</p>
                        <p class="help-block">{{ route('login', ['nosaml']) }}</p>
                        {!! $errors->first('saml_forcelogin', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                    </div>
                </div>

                <!-- SAML Single Log Out -->
                <div class="form-group">
                    <div class="col-md-3">
                        <strong>{{ trans('admin/settings/general.saml_slo_label') }}</strong>
                    </div>
                    <div class="col-md-9">
                        <label class="form-control{{ config('app.lock_passwords') === true ? ' form-control--disabled': '' }}">
                            {{ Form::checkbox('saml_slo', '1', old('saml_slo', $setting->saml_slo),['class' => 'minimal '. $setting->demoMode, $setting->demoMode]) }}
                            {{ trans('admin/settings/general.saml_slo') }}
                        </label>
                        <p class="help-block">{{ trans('admin/settings/general.saml_slo_help') }}</p>
                        {!! $errors->first('saml_slo', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                    </div>
                </div>

                <!-- SAML Custom Options -->
                <div class="form-group {{ $errors->has('saml_custom_settings') ? 'error' : '' }}">
                    <div class="col-md-3">
                        {{ Form::label('saml_custom_settings', trans('admin/settings/general.saml_custom_settings')) }}
                    </div>
                    <div class="col-md-9">
                        {{ Form::textarea('saml_custom_settings', old('saml_custom_settings', $setting->saml_custom_settings), ['class' => 'form-control','placeholder' => 'example.option=false&#13;&#10;sp_x509cert=file:///...&#13;&#10;sp_private_key=file:///', 'wrap' => 'off', $setting->demoMode]) }}
                        <p class="help-block">{{ trans('admin/settings/general.saml_custom_settings_help') }}</p>
                        {!! $errors->first('saml_custom_settings', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                    </div>
                </div>

            </div> <!--/.box-body-->
            <div class="box-footer">
                <div class="text-left col-md-6">
                    <a class="btn btn-link text-left" href="{{ route('settings.index') }}">{{ trans('button.cancel') }}</a>
                </div>
                <div class="text-right col-md-6">
                    <button type="submit" class="btn btn-primary" {{ config('app.lock_passwords') === true ? ' disabled': '' }}><i class="fas fa-check icon-white" aria-hidden="true"></i> {{ trans('general.save') }}</button>
                </div>

            </div>
        </div> <!-- /box -->

    </div> <!-- /.col-md-8-->
</div> <!-- /.row-->

{{Form::close()}}


@stop

@push('js')
<script nonce="{{ csrf_token() }}">
    $('#saml_idp_metadata_upload_btn').click(function() {
        $('#saml_idp_metadata_upload').click();
    });

    $('#saml_idp_metadata_upload').on('change', function() {
        var fr = new FileReader();

        fr.onload = function(e) {
            $('#saml_idp_metadata').text(e.target.result);
        }

        fr.readAsText(this.files[0]);
    });
</script>
@endpush