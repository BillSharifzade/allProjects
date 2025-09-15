<?php $__env->startSection('content'); ?>
    <p class="flex justify-content-end">
        <a href="/admin/cashbox-users/create" class="btn btn-primary text-uppercase">
            Добавить кассира
        </a>
    </p>
    <?php echo app('arrilot.widget')->run('Error'); ?>
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Касса
        </th>
        <th>
            Имя
        </th>
        <th>
            Фамилия
        </th>
        <th>
            Лицензия кассира
        </th>
        <th>
            Логин
        </th>
        <th>
            Роль
        </th>
        <th>
            Телефон
        </th>
        <th>
            Смена
        </th>
        <th>
            Дата
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        <?php ($counter = 0); ?>
        <?php $__currentLoopData = $cashboxUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashboxUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($cashboxUser->user->role == 'cashier' || $cashboxUser->user->role == 'cashier-audit'): ?>
                <?php ($counter++); ?>
                <tr>
                    <td>
                        <?php echo e($counter); ?>.
                    </td>
                    <td>
                        <?php echo e($cashboxUser->cashbox->name); ?><br/>
                        <p class="badge badge-info"><?php echo e($cashboxUser->cashbox->nickname); ?></p>
                    </td>
                    <td>
                        <?php echo e($cashboxUser->user->first_name); ?>

                    </td>
                    <td>
                        <?php echo e($cashboxUser->user->last_name); ?>

                    </td>
                    <td>
                        <?php echo e($cashboxUser->user_license); ?>

                    </td>
                    <td>
                        <?php echo e($cashboxUser->user->login); ?>

                    </td>
                    <td>
                        <?php if($cashboxUser->user->role == 'cashier'): ?>
                            Кассир
                        <?php else: ?>
                            Кассир (аудит)
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo e($cashboxUser->user->phone); ?>

                    </td>
                    <td>
                        <?php ($isOpen = isset($shiftOpenMap[$cashboxUser->user_id . ':' . $cashboxUser->cashbox_id])); ?>
                        <span class="badge <?php echo e($isOpen ? 'badge-success' : 'badge-secondary'); ?>" style="font-size: 12px;">
                            <?php echo e($isOpen ? 'Открыта' : 'Закрыта'); ?>

                        </span>
                    </td>
                    <td>
                        <?php echo date('Y-m-d',$cashboxUser->user->created_at->timestamp); ?>
                    </td>
                    <td style="width:90px;">
                        <a href="/admin/cashbox-users/<?php echo e($cashboxUser->id); ?>/update">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/admin/cashbox-users/<?php echo e($cashboxUser->id); ?>/delete" style="font-size: 16px" class="px-1">
                            <i class="fas fa-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/cashboxUser/index.blade.php ENDPATH**/ ?>