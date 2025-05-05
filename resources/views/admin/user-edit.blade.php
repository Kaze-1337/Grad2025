@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="wg-box">
            <h3 class="mb-4">Edit User</h3>
            <form class="form-new-product form-style-1" action="{{ route('admin.user.update', ['id' => $user->id]) }}" method="POST" id="user-edit-form">
                @csrf
                @method('PUT')
                <fieldset class="name mb-3">
                    <div class="body-title">Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" name="name" value="{{ $user->name }}" required>
                </fieldset>
                @error('name')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                <fieldset class="name mb-3">
                    <div class="body-title">Email <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="email" name="email" value="{{ $user->email }}" required>
                </fieldset>
                @error('email')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                <fieldset class="category mb-3">
                    <div class="body-title">Account Type</div>
                    <div class="select flex-grow">
                        <select name="utype" required>
                            <option value="ADM" {{ $user->utype == 'ADM' ? 'selected' : '' }}>Administrator</option>
                            <option value="USR" {{ $user->utype == 'USR' ? 'selected' : '' }}>Customer</option>
                        </select>
                    </div>
                </fieldset>
                @error('utype')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                <fieldset class="name mb-3">
                    <div class="body-title">Current Password</div>
                    <input class="flex-grow" type="password" name="current_password" placeholder="Enter current password">
                </fieldset>
                @error('current_password')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                <fieldset class="name mb-3">
                    <div class="body-title">New Password</div>
                    <input class="flex-grow" type="password" id="password" name="password" placeholder="Enter new password">
                </fieldset>
                @error('password')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                <fieldset class="name mb-3">
                    <div class="body-title">Confirm New Password</div>
                    <input class="flex-grow" type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                </fieldset>
                @error('password_confirmation')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                <div class="bot mt-4">
                    <button class="tf-button w208" type="submit" id="update-btn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function(){
        function checkPasswordMatch() {
            var password = $('#password').val();
            var confirmPassword = $('#password_confirmation').val();
            if(password !== confirmPassword) {
                $('#update-btn').prop('disabled', true);
            } else {
                $('#update-btn').prop('disabled', false);
            }
        }
        $('#password, #password_confirmation').on('keyup change', checkPasswordMatch);
    });
</script>
@endpush
