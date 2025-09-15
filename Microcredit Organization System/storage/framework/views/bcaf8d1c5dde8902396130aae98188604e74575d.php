<?php $__env->startSection('inc-content'); ?>
    <div class="table-responsive">
    <table class="table table-striped table-sm mb-2">
        <thead>
        <tr><th>#</th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Доставлено</th><th>Принято кассиром</th></tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($items->firstItem() + $i); ?></td>
                <td><?php echo e($row->contract_no); ?></td>
                <td><?php echo e($row->client_name); ?></td>
                <td><?php echo e($row->loan_info); ?></td>
                <td><?php echo date('Y-m-d',$row->delivered_at); ?></td>
                <td>
                    <?php if($row->accepted_by_cashier): ?>
                        <span class="badge badge-success">Принято кассиром</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Ожидает приема</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    </div>
    <div><?php echo e($items->links()); ?></div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('incassator.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/incassator/history/index.blade.php ENDPATH**/ ?>