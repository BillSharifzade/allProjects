<?php $__env->startSection('content'); ?>
    <?php echo e(Form::open(['url' => '/admin/cashbox-users/create', 'method' => 'post'])); ?>

        <?php echo e(csrf_field()); ?>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo app('arrilot.widget')->run('Error'); ?>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Новый кассир</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="first_name">Имя</label>
                        <?php echo e(Form::text('first_name', '', ['id' => 'first_name', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        <?php echo e(Form::text('last_name', '', ['id' => 'last_name', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <?php echo e(Form::number('phone', '', ['id' => 'phone', 'step' => '0', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Логин</label>
                        <?php echo e(Form::text('login', '', ['id' => 'login', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <?php echo e(Form::password('password', [ 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="cashbox_id">Касса</label>
                        <?php echo e(Form::select('cashbox_id', $cashboxes, '', ['id' => 'cashbox_id', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="role">Роль</label>
                        <?php echo e(Form::select('role', ['cashier' => 'Кассир', 'cashier-audit' => 'Кассир-аудит'], '', ['id' => 'role', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="user_license">Лицензия кассира</label>
                        <?php echo e(Form::text('user_license', '', [ 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <?php echo e(Form::submit('СОХРАНИТЬ', ['class' => 'btn btn-primary'])); ?>

                </div>
            </div>
        </div>
    <?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/cashboxUser/create.blade.php ENDPATH**/ ?>