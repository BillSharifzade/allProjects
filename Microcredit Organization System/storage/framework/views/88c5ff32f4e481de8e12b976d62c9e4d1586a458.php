<?php $__env->startSection('content'); ?>
    <form method="get" class="m-2 p-2 bg-light">
        <div class="form-row">
            <div class="col"><input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control" /></div>
            <div class="col"><input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control" /></div>
            <div class="col"><button class="btn btn-primary">Показать</button></div>
        </div>
    </form>
    <?php ($groups = $items->getCollection()->groupBy('cashbox_id')); ?>
    <?php ($n = $items->firstItem()); ?>
    <table class="table table-sm table-striped m-2">
        <thead><tr><th>#</th><th>Касса</th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Инкассатор</th><th>Кассир</th><th>Забрал</th><th>Доставил</th><th>Статус</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashboxId => $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="table-secondary">
                <td colspan="10"><strong><?php echo e(optional($cashboxes[$cashboxId] ?? null)->name ?: 'Без кассы'); ?></strong></td>
            </tr>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($n); ?></td>
                    <td><?php echo e(optional($cashboxes[$row->cashbox_id] ?? null)->name); ?></td>
                    <td><?php echo e($row->contract_no); ?></td>
                    <td><?php echo e($row->client_name); ?></td>
                    <td><?php echo e($row->loan_info); ?></td>
                    <td><?php echo e($row->incassator_id ? (($users[$row->incassator_id]->last_name ?? '') . ' ' . ($users[$row->incassator_id]->first_name ?? '')) : '-'); ?></td>
                    <td><?php echo e($row->cashier_id ? (($users[$row->cashier_id]->last_name ?? '') . ' ' . ($users[$row->cashier_id]->first_name ?? '')) : '-'); ?></td>
                    <td><?php echo e($row->picked_by_incassator ? date('Y-m-d H:i', $row->picked_at) : '-'); ?></td>
                    <td><?php echo e($row->delivered_by_incassator ? date('Y-m-d H:i', $row->delivered_at) : '-'); ?></td>
                    <td>
                        <?php ($log = ($latestLogs[$row->id] ?? null)); ?>
                        <?php if($row->accepted_by_cashier): ?>
                            <span class="badge badge-success">Принято <?php echo e(date('Y-m-d H:i', $row->accepted_at)); ?></span>
                        <?php elseif($log && $log->action === 'reset'): ?>
                            <span class="badge badge-danger">Отклонено кассиром <?php echo e(date('Y-m-d H:i', $log->created_at)); ?></span>
                        <?php elseif($row->delivered_by_incassator): ?>
                            <span class="badge badge-warning">Ожидает приема</span>
                        <?php elseif($row->picked_by_incassator): ?>
                            <span class="badge badge-info">В пути</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Ожидает инкассатора</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php ($n++); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links()); ?>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/incassation/index.blade.php ENDPATH**/ ?>