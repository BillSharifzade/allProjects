<?php $__env->startSection('content'); ?>

    <?php echo e(Form::open(['url' => '/signin', 'method' => 'post', 'autocomplete' => 'off'])); ?>

    <?php echo e(csrf_field()); ?>

        <div class="container">
            <div class="row mt-3 justify-content-center">
                <div class="col-12 col-md-8 col-lg-5 p-0 m-0">
                    <div class="card shadow-sm border-0" style="border-radius: .75rem;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-4 text-center" style="font-weight:700;">Вход в систему</h3>

                            <?php if(isset($lockedUntil) && $lockedUntil > time()): ?>
                                <div class="alert alert-danger" role="alert">
                                    Слишком много попыток. Попробуйте позже.
                                </div>
                            <?php endif; ?>

                            <div class="form-group mb-3">
                                <?php echo e(Form::text('login', old('login',''), [
                                    'class' => 'form-control form-control-lg' . ($errors->has('login') ? ' is-invalid' : ''),
                                    'placeholder' => 'Логин',
                                    'autocapitalize' => 'off',
                                    'autocorrect' => 'off',
                                    'spellcheck' => 'false',
                                    'autocomplete' => 'username',
                                    'autofocus' => true,
                                ])); ?>

                                <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group mb-4">
                                <?php echo e(Form::password('password', [
                                    'class' => 'form-control form-control-lg' . (($errors->has('password') || ($errors->any() && !$errors->has('login') && !$errors->has('password'))) ? ' is-invalid' : ''),
                                    'placeholder' => 'Пароль',
                                    'autocomplete' => 'current-password',
                                ])); ?>

                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php if($errors->any() && !$errors->has('login') && !$errors->has('password')): ?>
                                    <div class="invalid-feedback d-block"><?php echo e($errors->first()); ?></div>
                                <?php endif; ?>
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
    <?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('signin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/signin/index.blade.php ENDPATH**/ ?>