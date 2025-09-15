<?php $__env->startSection('content'); ?>
    <a href="/admin/incassators/create" class="btn btn-primary m-2">Добавить инкассатора</a>
    <table class="table table-sm table-striped m-2">
        <thead><tr><th>#</th><th>Имя</th><th>Логин</th><th>Телефон</th><th>Сейф</th><th></th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($items->firstItem() + $i); ?></td>
                <td><?php echo e($u->last_name); ?> <?php echo e($u->first_name); ?></td>
                <td><?php echo e($u->login); ?></td>
                <td><?php echo e($u->phone); ?></td>
                <td><a href="/admin/incassators/<?php echo e($u->id); ?>/safe">Открыть</a></td>
                <td><a href="/admin/incassators/<?php echo e($u->id); ?>/delete" onclick="return confirm('Удалить?');">Удалить</a></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links()); ?>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/incassator/index.blade.php ENDPATH**/ ?>