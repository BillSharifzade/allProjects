<?php $__env->startSection('content'); ?>
    <div class="container">
        <?php if(session()->has('message')): ?>
            <div class="alert alert-success">
                <?php echo e(session()->get('message')); ?>

            </div>
        <?php else: ?>
            <div class="col">
                <?php echo app('arrilot.widget')->run('Error'); ?>
            </div>
            <div class="col">
                <?php echo e(Form::open(['url' => '/admin/passwords', 'method' => 'post'])); ?>

                <?php echo e(csrf_field()); ?>


                <div class="col p-0 m-0">
                    <div class="form-group">
                        <h5 for="admin">Пользователь</h5>
                        <?php echo e(Form::select('login', ['admin' => 'Администратор (admin)', 'reporter' => 'Репортер (reporter)'], '', ['class' => 'form-control'])); ?>

                    </div>
                </div>

                <div class="col p-0 m-0">
                    <div class="form-group">
                        <?php echo e(Form::password('old_password', ['class' => 'form-control', 'placeholder' => 'Старый пароль'])); ?>

                    </div>
                </div>
                <div class="col p-0 m-0">
                    <div class="form-group">
                        <?php echo e(Form::password('new_password', ['class' => 'form-control', 'placeholder' => 'Новый пароль'])); ?>

                    </div>
                </div>
                <div class="col p-0 m-0">
                    <div class="form-group">
                        <?php echo e(Form::password('password_confirm', ['class' => 'form-control', 'placeholder' => 'Повтор нового пароля'])); ?>

                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <?php echo e(Form::submit('СОХРАНИТЬ', ['class' => 'btn btn-primary'])); ?>

                    </div>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/password/index.blade.php ENDPATH**/ ?>