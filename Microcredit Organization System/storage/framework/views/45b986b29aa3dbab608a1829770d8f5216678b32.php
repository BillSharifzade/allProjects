<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Сотрудники</h4>
            <a class="btn btn-primary" href="/admin/hr/create">Добавить</a>
        </div>

        <table class="table table-sm table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>ФИО</th>
                <th>Должность</th>
                <th>Телефон</th>
                <th>Текущий контракт</th>
                <th>Оклад</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($last = $i->contracts->first()); ?>
                <tr>
                    <td><?php echo e($i->id); ?></td>
                    <td><?php echo e($i->last_name); ?> <?php echo e($i->first_name); ?></td>
                    <td><?php echo e($i->position); ?></td>
                    <td><?php echo e($i->phone); ?></td>
                    <td><?php echo e(optional($last)->contract_no); ?></td>
                    <td><?php echo e($last ? number_format($last->salary,2,'.',' ') . ' ' . $last->currency : ''); ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-secondary" href="/admin/hr/<?php echo e($i->id); ?>/edit">Редактировать</a>
                        <a class="btn btn-sm btn-outline-danger" href="/admin/hr/<?php echo e($i->id); ?>/delete" onclick="return confirm('Удалить сотрудника <?php echo e($i->last_name); ?> <?php echo e($i->first_name); ?>?');">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <?php echo e($items->links()); ?>

    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/hr/index.blade.php ENDPATH**/ ?>