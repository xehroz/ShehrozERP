<h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
<form class="user" action="/auth/authenticate" method="post">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">Remember Me</label>
    </div>
    <button type="submit" class="btn btn-primary btn-block w-100 mb-3">Login</button>
</form>
<hr>
<div class="text-center">
    <a class="small" href="/auth/forgot-password">Forgot Password?</a>
</div>

<div class="mt-4 alert alert-info">
    <h5>Demo Login</h5>
    <p class="mb-0">Username: admin<br>Password: admin</p>
</div>