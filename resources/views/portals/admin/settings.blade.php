@extends('layouts.app')

@section('title', 'Profile Settings - TPA')
@section('header_title', 'Profile Settings')
@section('header_subtitle', 'Manage personal credentials and security features')

@section('content')
<div class="animate-fade-in" style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Profile Info -->
    <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
        <h3>Profile Settings</h3>
        <p style="font-size: 0.85rem; color: var(--text-secondary);">Modify your contact details and account name.</p>
        
        <form action="{{ route('admin.settings.profile') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="profile_name">Full Name</label>
                <input class="form-control" type="text" id="profile_name" name="name" value="{{ auth()->user()->name }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="profile_email">Email Address</label>
                <input class="form-control" type="email" id="profile_email" name="email" value="{{ auth()->user()->email }}" required>
            </div>

            <button type="submit" class="btn btn-primary" style="justify-content: center; padding: 0.75rem; margin-top: 0.5rem;">
                Save Profile Details
            </button>
        </form>
    </div>

    <!-- Security Settings -->
    <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
        <h3>Security Settings</h3>
        <p style="font-size: 0.85rem; color: var(--text-secondary);">Update your authentication password periodically.</p>
        
        <form action="{{ route('admin.settings.password') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="current_password">Current Password</label>
                <input class="form-control" type="password" id="current_password" name="current_password" placeholder="••••••••" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-accent" style="justify-content: center; padding: 0.75rem; margin-top: 0.5rem;">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
