@extends('signin')

@section('content')

    {{ Form::open(['url' => '/signin', 'method' => 'post', 'autocomplete' => 'off']) }}
    {{ csrf_field() }}
        <div class="container">
            <div class="row mt-3 justify-content-center">
                <div class="col-12 col-md-8 col-lg-5 p-0 m-0">
                    <div class="card shadow-sm border-0" style="border-radius: .75rem;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-4 text-center" style="font-weight:700;">Вход в систему</h3>

                            @if(isset($lockedUntil) && $lockedUntil > time())
                                <div class="alert alert-danger" role="alert">
                                    Слишком много попыток. Попробуйте позже.
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                {{Form::text('login', old('login',''), [
                                    'class' => 'form-control form-control-lg' . ($errors->has('login') ? ' is-invalid' : ''),
                                    'placeholder' => 'Логин',
                                    'autocapitalize' => 'off',
                                    'autocorrect' => 'off',
                                    'spellcheck' => 'false',
                                    'autocomplete' => 'username',
                                    'autofocus' => true,
                                ])}}
                                @error('login')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                {{Form::password('password', [
                                    'class' => 'form-control form-control-lg' . (($errors->has('password') || ($errors->any() && !$errors->has('login') && !$errors->has('password'))) ? ' is-invalid' : ''),
                                    'placeholder' => 'Пароль',
                                    'autocomplete' => 'current-password',
                                ])}}
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @if($errors->any() && !$errors->has('login') && !$errors->has('password'))
                                    <div class="invalid-feedback d-block">{{ $errors->first() }}</div>
                                @endif
                            </div>

                            <!-- Honeypot for bots -->
                            <div style="position:absolute; left:-9999px; top:-9999px;" aria-hidden="true">
                                <input type="text" name="website" tabindex="-1" autocomplete="off" />
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block" style="border-radius:.5rem;">Войти</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{ Form::close() }}
@endsection
