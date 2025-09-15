<?php $__env->startSection('content'); ?>
    <h5 class="m-2">Сегодняшние продажи</h5>
    <table class="table table-sm table-striped m-2">
        <thead>
        <tr>
            <th>#</th>
            <th>Договор</th>
            <th>Клиент</th>
            <th>Сумма</th>
            <th>Дата</th>
            <th>Статус</th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($loan = $loans[$sale->loan_id] ?? null); ?>
            <tr>
                <td><?php echo e($items->firstItem() + $i); ?></td>
                <td>
                    <?php if($loan): ?>
                        №<?php echo e($loan->document_no); ?><?php if($loan->audit_document_no>0): ?>-<?php echo e($loan->audit_document_no); ?><?php endif; ?>
                    <?php endif; ?>
                </td>
                <td><?php echo e(optional(optional($loan)->loaner)->full_name); ?></td>
                <td><?php echo e(number_format($sale->total_amount, 2, '.', ' ')); ?></td>
                <td><?php echo date('Y-m-d',$sale->sold_at); ?></td>
                <td>
                    <?php if($sale->canceled_at > 0): ?>
                        <span class="badge badge-secondary">Отменено админом</span>
                    <?php else: ?>
                        <span class="badge badge-success">Завершено</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->appends($_GET)->links()); ?>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/sale/index.blade.php ENDPATH**/ ?>