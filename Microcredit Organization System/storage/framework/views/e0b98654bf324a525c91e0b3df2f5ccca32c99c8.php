<?php $__env->startSection('content'); ?>
    <form method="post" action="/admin/incassators/create" class="m-2" style="max-width:520px">
        <?php echo e(csrf_field()); ?>

        <div class="form-group"><label>Имя</label><input name="first_name" class="form-control" required></div>
        <div class="form-group"><label>Фамилия</label><input name="last_name" class="form-control" required></div>
        <div class="form-group"><label>Телефон</label><input name="phone" class="form-control"></div>
        <div class="form-group"><label>Логин</label><input name="login" class="form-control" required></div>
        <div class="form-group"><label>Пароль</label><input name="password" type="password" class="form-control" required></div>
        <button class="btn btn-success">Сохранить</button>
    </form>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/incassator/create.blade.php ENDPATH**/ ?>