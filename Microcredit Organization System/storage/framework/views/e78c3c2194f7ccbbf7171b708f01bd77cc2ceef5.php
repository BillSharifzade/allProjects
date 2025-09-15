<?php $__env->startSection('content'); ?>
    <div class="m-3 card p-3">
        <h4>Сегодняшние переводы</h4>
        <table class="table table-light table-hover zebra mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Тип</th>
                <th>Касса</th>
                <th>Кассир</th>
                <th>Сумма</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($cu = $cashboxUsers[$i->user_id] ?? null); ?>
                <tr>
                    <td><?php echo e($i->id); ?></td>
                    <td>
                        <?php ($typeLabel = $i->event_type == 'admin_fund' ? 'Инвестиция (админ)' : ($i->event_type == 'transfer_in' ? 'Подкрепление' : 'Передача')); ?>
                        <?php echo e($typeLabel); ?>

                    </td>
                    <td><?php echo e($cu ? $cu->cashbox->name : '-'); ?></td>
                    <td><?php echo e($cu ? ($cu->user->last_name . ' ' . $cu->user->first_name) : '-'); ?></td>
                    <td><?php echo e(number_format($i->amount, 2, '.', ' ')); ?></td>
                    <td><?php echo date('Y-m-d',$i->occurred_at); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <?php echo e($items->links()); ?>

    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/transfer/index.blade.php ENDPATH**/ ?>