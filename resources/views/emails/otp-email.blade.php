{{-- <div>
    <!-- He who is contented is rich. - Laozi -->
    <p>Here's your OTP for login:</p>
    <p>{{ $otp }}</p>
</div> --}}
<div>
    <!-- He who is contented is rich. - Laozi -->
    <p>Here's your OTP for login:</p>
    <div
        style="
        display: inline-block;
        /* border: 2px solid white; */
        border-radius: 6px;
        background: #495057;
        color: #ffffff;
        padding: 8px 16px;
        letter-spacing: 1px;
        font-weight: 500;
        /* font-size: 22px; */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      ">
        {{ $otp }}
        {{-- 123456 --}}
    </div>
</div>
