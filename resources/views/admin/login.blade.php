<!DOCTYPE html>
<html lang="en" style="height:100%;background:#fff">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login – HSPConnect</title>
    @vite(['resources/css/admin.css'])
    <style>
        body { height: 100%; background: #fff !important; font-family: ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body>

<div class="flex min-h-screen flex-col justify-center px-6 py-12">
    <div class="mx-auto w-full max-w-sm">
        <a href="{{ route('home') }}" class="block text-center text-2xl font-bold tracking-tight" style="color:#111827">HSPConnect</a>
        <h2 class="mt-6 text-center text-2xl font-bold tracking-tight" style="color:#111827">Admin Sign In</h2>
    </div>

    <div class="mx-auto mt-10 w-full max-w-sm">

        @if($errors->any())
            <div class="mb-6 rounded-md p-4 text-sm ring-1" style="background:#fef2f2;color:#b91c1c;ring-color:#fca5a5">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium" style="color:#111827">Email address</label>
                <div class="mt-2">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                           class="block w-full rounded-md px-3 py-2 text-sm"
                           style="background:#fff;color:#111827;border:1px solid #d1d5db;outline:none"
                           onfocus="this.style.borderColor='#4f46e5';this.style.boxShadow='0 0 0 2px rgba(79,70,229,.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'" />
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium" style="color:#111827">Password</label>
                <div class="mt-2">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="block w-full rounded-md px-3 py-2 text-sm"
                           style="background:#fff;color:#111827;border:1px solid #d1d5db;outline:none"
                           onfocus="this.style.borderColor='#4f46e5';this.style.boxShadow='0 0 0 2px rgba(79,70,229,.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'" />
                </div>
            </div>

            <div>
                <button type="submit"
                        class="flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm"
                        style="background:#4f46e5"
                        onmouseover="this.style.background='#4338ca'"
                        onmouseout="this.style.background='#4f46e5'">
                    Sign in
                </button>
            </div>
        </form>

        <p class="mt-10 text-center text-sm" style="color:#6b7280">
            <a href="{{ route('home') }}" style="color:#4f46e5;font-weight:600">← Back to site</a>
        </p>
    </div>
</div>

</body>
</html>
