<?php $__env->startSection('content'); ?>
    <h5 class="m-2">Сейф инкассатора: <?php echo e($user->last_name); ?> <?php echo e($user->first_name); ?></h5>
    <div class="table-responsive m-2">
        <table class="table table-sm table-striped">
            <thead><tr><th>#</th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Добавлено</th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($items->firstItem() + $i); ?></td>
                    <td><?php echo e($row->contract_no); ?></td>
                    <td><?php echo e($row->client_name); ?></td>
                    <td><?php echo e($row->loan_info); ?></td>
                    <td>
                        <?php echo e(($row->created_at instanceof \Illuminate\Support\Carbon) ? $row->created_at->format('Y-m-d H:i') : (is_numeric($row->created_at) ? date('Y-m-d H:i', $row->created_at) : (string)$row->created_at)); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="m-2"><?php echo e($items->links()); ?></div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/incassator/safe.blade.php ENDPATH**/ ?>