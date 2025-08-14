@extends('Forented.layout.index')
@section('main_contant')
<div class="delete-account-container" style="max-width: 600px; margin: auto; padding: 20px; font-family: Arial, sans-serif; color: #333;">
    <h1 style="color: #d9534f;">Delete Your Account - Magic Wash</h1>

    <p>We’re sorry to see you go!</p>
    <p>If you no longer wish to use <strong>Magic Wash</strong>, you can permanently delete your account here. Once deleted, your account and all associated data will be removed from our system.</p>

    <hr>

    <h3>Before You Proceed</h3>
    <ul>
        <li>All your bookings, car details, and service history will be <strong>permanently deleted</strong>.</li>
        <li>This action is <strong>irreversible</strong>.</li>
        <li>You will <strong>lose access</strong> to any offers, loyalty points, or wallet balance.</li>
        <li>You will need to <strong>create a new account</strong> if you want to use Magic Wash again.</li>
    </ul>

    <hr>

    <h2 style="color: #c9302c;">⚠️ Are You Sure You Want to Delete Your Account?</h2>
    <p>If you’re absolutely certain, click the button below to permanently delete your Magic Wash account.</p>

    <form method="POST" action="/delete-account">
        <!-- Include CSRF token if using Laravel or other frameworks -->
        <button type="submit" style="background-color: #d9534f; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Delete My Account
        </button>
        <p style="color: #a94442; margin-top: 10px;">This action cannot be undone.</p>
    </form>

    <hr>

    <p><strong>Need Help or Changed Your Mind?</strong></p>
    <p>If you're facing any issues or need help, please <a href="mailto:support@magicwash.com" style="color: #0275d8;">contact our support team</a>. We’d love to assist you!</p>
</div>

@endsection