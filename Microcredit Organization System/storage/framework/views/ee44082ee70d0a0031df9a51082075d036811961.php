<?php $__env->startSection('content'); ?>
    <form method="get" class="m-2 p-2 bg-light">
        <div class="form-row">
            <div class="col">
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control" />
            </div>
            <div class="col">
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control" />
            </div>
            <div class="col">
                <button class="btn btn-primary">Показать</button>
            </div>
        </div>
    </form>
    <table class="table table-sm table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Договор</th>
            <th>Касса</th>
            <th>Клиент</th>
            <th>Сумма</th>
            <th>Дата</th>
            <th>Статус</th>
            <th>Действие</th>
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
                <td><?php echo e(optional($sale->cashbox)->name); ?></td>
                <td><?php echo e(optional(optional($loan)->loaner)->full_name); ?></td>
                <td><?php echo e(number_format($sale->total_amount, 2, '.', ' ')); ?></td>
                <td><?php echo date('Y-m-d',$sale->sold_at); ?></td>
                <?php if($sale->canceled_at == 0): ?>
                    <?php ($p = (float)$sale->profit_amount); ?>
                    <td class="text-nowrap">
                        <?php if($p > 0): ?>
                            <span class="badge badge-success">+<?php echo e(number_format($p, 2, '.', ' ')); ?></span>
                        <?php elseif($p < 0): ?>
                            <span class="badge badge-danger">-<?php echo e(number_format(abs($p), 2, '.', ' ')); ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary">0</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <a href="/admin/sales/<?php echo e($sale->id); ?>/cancel" class="btn btn-sm btn-outline-danger" onclick="return confirm('Отменить продажу и вернуть баланс?');">Отменить</a>
                    </td>
                <?php else: ?>
                    <td><span class="badge badge-secondary">Отменено</span></td>
                    <td></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->appends($_GET)->links()); ?>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/sale/index.blade.php ENDPATH**/ ?>