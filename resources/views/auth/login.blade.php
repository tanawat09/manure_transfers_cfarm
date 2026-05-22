<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success border-0 mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3 text-start">
            <label for="email" class="form-label">ที่อยู่อีเมล (Email)</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" 
                   type="email" name="email" :value="old('email')" 
                   placeholder="ระบุอีเมลผู้ใช้งาน..." required autofocus autocomplete="username">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4 text-start">
            <label for="password" class="form-label">รหัสผ่าน (Password)</label>
            <input id="password" class="form-control @error('password') is-invalid @enderror" 
                   type="password" name="password" 
                   placeholder="กรอกรหัสผ่านของคุณ..." required autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check text-start">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label" for="remember_me">
                    จดจำฉันในระบบ
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i> เข้าสู่ระบบ
        </button>
        
        <div class="mt-4 text-center">
            <small class="text-white-50">กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่เพื่อทำรายการ</small>
        </div>
    </form>
</x-guest-layout>
