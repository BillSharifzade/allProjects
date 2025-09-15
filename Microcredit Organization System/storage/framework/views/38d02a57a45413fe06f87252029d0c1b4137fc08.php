<?php $__env->startSection('content'); ?>
    <?php echo e(Form::open(['url' => '/admin/cashbox-users/' . $cashboxUser->id . '/update', 'method' => 'post'])); ?>

        <?php echo e(csrf_field()); ?>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo app('arrilot.widget')->run('Error'); ?>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Редатирование кассира</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="first_name">Имя</label>
                        <?php echo e(Form::text('first_name', $user->first_name, ['id' => 'first_name', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="last_name">Фамилия</label>
                        <?php echo e(Form::text('last_name', $user->last_name, ['id' => 'last_name', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <?php echo e(Form::number('phone', $user->phone, ['id' => 'phone', 'step' => '0', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Логин</label>
                        <?php echo e(Form::text('login', $user->login, ['id' => 'login', 'disabled' => 'disabled', 'class' => 'form-control'])); ?>

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
                        <?php echo e(Form::select('cashbox_id', $cashboxes, $cashboxUser->cashbox_id, ['id' => 'cashbox_id', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="role">Роль</label>
                        <?php echo e(Form::select('role', ['cashier' => 'Кассир', 'cashier-audit' => 'Кассир-аудит'], $user->role, ['id' => 'role', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="user_license">Лицензия кассира</label>
                        <?php echo e(Form::text('user_license', $cashboxUser->user_license, [ 'class' => 'form-control'])); ?>

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

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/cashboxUser/edit.blade.php ENDPATH**/ ?>