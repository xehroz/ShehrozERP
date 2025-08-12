<div class="text-center">
    <h1 class="h4 text-gray-900 mb-4">Forgot Your Password?</h1>
    <p>Enter your email address below and we'll send you a link to reset your password.</p>
</div>

<form class="user" action="/auth/reset-password" method="post">
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block w-100 mb-3">Reset Password</button>
</form>

<hr>
<div class="text-center">
    <a class="small" href="/auth/login">Back to Login</a>
</div>