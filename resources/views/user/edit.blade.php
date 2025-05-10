@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="card w-100" style="max-width: 500px;">
            <div class="card-header bg-light">
                <h2 class="page-title mb-0" style="font-size: 1.5rem;">Edit Account</h2>
            </div>
            <div class="card-body">
                @if(session('status'))
                    <div class="alert alert-success mb-3">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('user.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Name <span class="tf-color-1">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="tf-color-1">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Password <span class="tf-color-1">*</span></label>
                        <input type="password" class="form-control" name="current_password" placeholder="Enter current password" required autocomplete="new-password">
                        @error('current_password')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter new password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm new password">
                        @error('password')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-dark w-100" type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection