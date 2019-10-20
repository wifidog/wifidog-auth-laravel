@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if ($social_login['enabled'])
            <div class="card">
                <div class="card-header">{{ __('Social Login') }}</div>

                <div class="card-body text-center">
                    @if (in_array('wechat_web', $social_login['providers']))
                        <script src="https://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
                        <div class="card-text" id="login_container"></div>
                        <script>
                            var obj = new WxLogin({
                                self_redirect: false,
                                id: "login_container",
                                appid: "{{ config('services.wechat_web.client_id') }}",
                                scope: "snsapi_login",
                                redirect_uri: "{{ config('services.wechat_web.redirect') }}",
                                state: "{{ csrf_token() }}",
                                style: "",
                                href: ""
                            });
                        </script>
                        @php
                            unset($social_login['providers'][array_search('wechat_web', $social_login['providers'])]);
                        @endphp
                    @endif

                    <ul class="nav justify-content-center">
                        @foreach ($social_login['providers'] as $provider)
                            <li class="nav-item mr-2 mt-2">
                                <a class="nav-link btn {{ $errors->has($provider) ? 'btn-danger' : 'btn-primary' }}" href="/login/{{ strtolower(str_replace('_', '-', $provider)) }}" role="button">{{ __($provider) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if ($password_login['enabled'])
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-sm-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
