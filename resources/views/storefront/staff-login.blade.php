@extends('storefront.layout')

@section('title', 'Staff login — Othbar')

@section('content')

<div class="sf-page-header" style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD;">
    <div class="sf-container">
        <p class="section-label">Staff</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3rem); color: #1E3A2A; margin-top: 0.5rem;">Store administration</h1>
    </div>
</div>

<div class="sf-container" style="max-width: 560px; padding-top: 2.5rem; padding-bottom: 4rem;">
    <div style="background: #F7F2E8; border: 1px solid #D8CCAD; padding: 2.5rem;">
        <p style="font-size: 0.92rem; color: rgba(30,58,42,0.75); line-height: 1.75; margin: 0 0 2rem;">
            Manage orders, products, and payment verification in the Othbar admin. Use the button below to open the secure sign-in page.
        </p>
        <a href="{{ $adminLoginUrl }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; width: 100%; text-decoration: none; box-sizing: border-box;">
            Continue to admin login
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
        </a>
        <p style="font-size: 0.72rem; color: rgba(30,58,42,0.5); margin: 1.5rem 0 0; line-height: 1.6;">
            Admin panel URL: <code style="background: rgba(30,58,42,0.08); padding: 0.15rem 0.4rem; font-size: 0.68rem;">/admin/login</code>
        </p>
    </div>
</div>

@endsection
