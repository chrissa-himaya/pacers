@extends('layouts.app')

@section('content')
<style>
  /* ---------- Page frame ---------- */
  .login-page {
    min-height: 100vh;
    background: #ffffff;
  }

  /* ---------- Header (logo + green bars) ---------- */
  .lp-header {
    background: #d6f3d6;
    border-bottom: 1px solid #c8c8c8;
  }
  .lp-header .lp-bar {
    height: 26px;
    background: #2f6f1f;
    border-top: 1px solid #1f4c14;
    border-bottom: 1px solid #1f4c14;
  }
  .lp-header .lp-inner {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 5px 7px;
  }
  .lp-logo {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    background: #fff;
    border: 1px solid #7a7a7a;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .lp-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* ---------- Main content layout ---------- */
  .lp-body {
    padding: 12px 14px 24px;
  }
  .lp-shell {
    display: grid;
    grid-template-columns: 1fr 500px;
    gap: 18px;
    align-items: start;
  }
  @media (max-width: 992px) {
    .lp-shell {
      grid-template-columns: 1fr;
    }
  }

  /* ---------- Left: Announcements / Events ---------- */
  .lp-tabs {
    display: inline-flex;
    gap: 4px;
    margin: 0 0 0;
    padding: 0;
    list-style: none;
  }
  .lp-tabs li {
    margin: 0;
  }
  .lp-tab {
    display: inline-block;
    padding: 4px 10px;
    font-size: 12px;
    border: 1px solid #6f6f6f;
    border-bottom: 0;
    background: #e9e9e9;
    color: #000;
    text-decoration: none;
  }
  .lp-tab.active {
    background: #ffffff;
    font-weight: 600;
  }
  .lp-panel {
    border: 2px solid #000;
    height: 560px;
    background: #fff;
    position: relative;
  }
  .lp-panel .lp-panel-msg {
    font-size: 12px;
    text-align: center;
    padding: 10px;
    color: #333;
  }

  /* =====================================================
     RIGHT PANEL — match the attached “glass login card”
     ===================================================== */

  /* Whole right column background (soft green) */
  .lp-right {
    padding-top: 26px ;
  }
  .lp-right-wrap {
    min-height: 560px;                 /* align with left panel height */
    background: radial-gradient(1200px 600px at 30% 20%, #b7d37a 0%, #8fc06b 45%, #7bb45f 100%);
    border-radius: 14px;
    padding: 26px;
    display: grid;
    place-items: center;
    overflow: hidden;
    position: relative;
  }
  /* subtle vignette */
  .lp-right-wrap::before {
    content: "";
    position: absolute;
    inset: -60px;
    background:
      radial-gradient(500px 260px at 20% 20%, rgba(255,255,255,.22), transparent 70%),
      radial-gradient(520px 320px at 80% 70%, rgba(255,255,255,.16), transparent 70%);
    filter: blur(0px);
    pointer-events: none;
  }

  /* The glassy card */
  .lp-card {
    width: min(360px, 100%);
    padding: 54px 26px 22px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.28);
    border: 1px solid rgba(255, 255, 255, 0.35);
    box-shadow:
      0 18px 40px rgba(0, 0, 0, 0.18),
      inset 0 1px 0 rgba(255, 255, 255, 0.45);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    position: relative;
  }

  /* Circle avatar (top) */
  .lp-avatar {
    position: absolute;
    top: -34px;
    left: 50%;
    transform: translateX(-50%);
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: #2d89a6;
    display: grid;
    place-items: center;
    box-shadow: 0 8px 20px rgba(0,0,0,.18);
    border: 4px solid rgba(255,255,255,.45);
  }
  .lp-avatar svg {
    width: 34px;
    height: 34px;
    opacity: .95;
    fill: #eaf7fb;
  }

  .lp-title {
    text-align: center;
    font-weight: 700;
    font-size: 30px;
    letter-spacing: .2px;
    margin: 0 0 8px;
    color: rgba(0,0,0,.75);
  }
  .lp-title-underline {
    width: 68px;
    height: 3px;
    border-radius: 99px;
    background: rgba(0,0,0,.55);
    margin: 0 auto 18px;
  }

  .lp-field-label {
    font-size: 12px;
    letter-spacing: .14em;
    color: rgba(0,0,0,.55);
    margin: 14px 0 6px;
  }

  /* Icon + input row */
  .lp-field {
    display: grid;
    grid-template-columns: 42px 1fr;
    align-items: center;
    border-radius: 10px;
    background: rgba(255,255,255,.55);
    border: 1px solid rgba(255,255,255,.55);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
    overflow: hidden;
  }
  .lp-field .lp-ico {
    height: 42px;
    display: grid;
    place-items: center;
    opacity: .75;
  }
  .lp-field .lp-ico svg {
    width: 18px;
    height: 18px;
    fill: rgba(0,0,0,.55);
  }
  .lp-input {
    border: 0;
    outline: none;
    background: transparent;
    height: 42px;
    padding: 0 12px;
    font-size: 13px;
    color: rgba(0,0,0,.75);
  }
  .lp-input::placeholder {
    color: rgba(0,0,0,.35);
    letter-spacing: .06em;
  }

  .lp-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 14px;
    font-size: 12px;
    color: rgba(0,0,0,.62);
  }
  .lp-actions a {
    color: rgba(0,0,0,.60);
    text-decoration: none;
  }
  .lp-actions a:hover {
    text-decoration: underline;
  }

  .lp-btn {
    width: 100%;
    height: 48px;
    border: 0;
    border-radius: 12px;
    background: #2f9aa6;
    color: #e8fbff;
    font-weight: 700;
    letter-spacing: .08em;
    margin-top: 16px;
    box-shadow: 0 10px 22px rgba(0,0,0,.18);
  }
  .lp-btn:hover { filter: brightness(0.97); }
  .lp-btn:active { transform: translateY(1px); }

  /* Make validation messages look okay on glass */
  .lp-card .invalid-feedback {
    color: rgba(120, 0, 0, .85);
  }
</style>

<div class="login-page">
  <!-- Header -->
  <div class="lp-header">
    <div class="lp-inner">
      <div class="lp-logo">
        <img src="{{ asset('images/pmc-logo-header.png') }}" alt="Logo">
      </div>
    </div>
    <div class="lp-bar"></div>
  </div>

  <!-- Body -->
  <div class="lp-body">
    <div class="lp-shell">

      <!-- LEFT: Announcements/Events panel -->
      <div>
        <ul class="lp-tabs">
          <li><a class="lp-tab active" href="#">Announcements</a></li>
          <li><a class="lp-tab" href="#">Events</a></li>
        </ul>
        <div class="lp-panel">
          <div class="lp-panel-msg">
            There are currently no active announcements for this site.
          </div>
        </div>
      </div>

      <!-- RIGHT: Glass login card -->
       <div class="lp-right">
        <div class="lp-right-wrap">
            <div class="lp-card">

            <div class="lp-avatar" aria-hidden="true">
                <!-- user icon -->
                <svg viewBox="0 0 24 24" role="img" aria-label="User">
                <path d="M12 12a4.2 4.2 0 1 0-4.2-4.2A4.2 4.2 0 0 0 12 12Zm0 2c-4.3 0-8 2.2-8 5v1h16v-1c0-2.8-3.7-5-8-5Z"/>
                </svg>
            </div>

            <h2 class="lp-title">Login</h2>
            <div class="lp-title-underline"></div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                <div class="lp-field-label">E MAIL</div>
                <div class="lp-field">
                    <div class="lp-ico" aria-hidden="true">
                    <!-- person/mail-ish icon -->
                    <svg viewBox="0 0 24 24">
                        <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/>
                    </svg>
                    </div>
                    <input id="email" type="email"
                        class="lp-input @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}"
                        placeholder="ENTER EMAIL"
                        required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <div class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></div>
                @enderror
                </div>

                <div>
                <div class="lp-field-label">PASSWORD</div>
                <div class="lp-field">
                    <div class="lp-ico" aria-hidden="true">
                    <!-- lock icon -->
                    <svg viewBox="0 0 24 24">
                        <path d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2Zm-7-2a2 2 0 0 1 4 0v2h-4Z"/>
                    </svg>
                    </div>
                    <input id="password" type="password"
                        class="lp-input @error('password') is-invalid @enderror"
                        name="password" placeholder="ENTER PASSWORD"
                        required autocomplete="current-password">
                </div>
                @error('password')
                    <div class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></div>
                @enderror
                </div>

                <button type="submit" class="lp-btn">LOGIN</button>

                <div class="lp-actions">
                <label class="d-inline-flex align-items-center gap-2" style="cursor:pointer;">
                    <input class="form-check-input m-0" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span style="margin-left:20px">Remember Me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                @endif
                </div>
            </form>

            </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
