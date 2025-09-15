<?php $__env->startSection('content'); ?>
    <div class="m-3">
        <h4>История переводов</h4>
        <form method="get" class="form-inline my-2">
            <div class="form-group mr-2">
                <label class="mr-2">С</label>
                <input type="date" name="from" class="form-control" value="<?php echo e(request('from')); ?>">
            </div>
            <div class="form-group mr-2">
                <label class="mr-2">По</label>
                <input type="date" name="to" class="form-control" value="<?php echo e(request('to')); ?>">
            </div>
            <button class="btn btn-primary mr-2">Показать</button>
            <button class="btn btn-secondary" name="calc" value="1">Посчитать сумму</button>
        </form>

        <?php if(!is_null($total)): ?>
            <div class="alert alert-info mt-2">
                <strong>Итого за период:</strong> <?php echo e(number_format($total, 2, '.', ' ')); ?>

            </div>
        <?php endif; ?>

        <?php if(isset($summaryList) && count($summaryList) > 0): ?>
            <h5 class="mt-3">Сводка по кассирам</h5>
            <table class="table table-bordered table-sm">
                <thead>
                <tr>
                    <th>Касса</th>
                    <th>Кассир</th>
                    <th>Передача</th>
                    <th>Подкрепление</th>
                    <th>Подкрепление основателя</th>
                    <th>Итого</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $summaryList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($cu = $summaryUsers[$row['user_id']] ?? null); ?>
                    <tr>
                        <td><?php echo e($cu ? $cu->cashbox->name : '-'); ?></td>
                        <td><?php echo e($cu ? ($cu->user->last_name . ' ' . $cu->user->first_name) : '-'); ?></td>
                        <td><?php echo e(number_format($row['transfer_out'], 2, '.', ' ')); ?></td>
                        <td><?php echo e(number_format($row['transfer_in'], 2, '.', ' ')); ?></td>
                        <td><?php echo e(number_format($row['admin_fund'], 2, '.', ' ')); ?></td>
                        <td><?php echo e(number_format($row['grand_total'], 2, '.', ' ')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endif; ?>

        <table class="table table-light mt-3">
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
                        <?php ($typeLabel = $i->event_type == 'admin_fund' ? 'Подкрепление основателя' : ($i->event_type == 'transfer_in' ? 'Подкрепление' : 'Передача')); ?>
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

        <?php echo e($items->withQueryString()->links()); ?>

    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/reporter/transfer/index.blade.php ENDPATH**/ ?>