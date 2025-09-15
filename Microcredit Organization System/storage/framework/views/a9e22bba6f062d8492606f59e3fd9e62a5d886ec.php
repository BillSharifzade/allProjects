<?php $__env->startSection('content'); ?>
    <div class="m-3 card p-3">
        <h4>Расходы за сегодня</h4>
        <a href="/expenses/create" class="btn btn-sm btn-success locked-hide">Добавить расход</a>

        <table class="table table-light table-hover zebra mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Категория</th>
                <th>Описание</th>
                <th>Сумма</th>
                <th>Время</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($i->id); ?></td>
                    <td><?php echo e($i->category); ?></td>
                    <td><?php echo e($i->description); ?></td>
                    <td><?php echo e(number_format($i->amount, 2, '.', ' ')); ?></td>
                    <td><?php echo date('Y-m-d',$i->occurred_at); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <?php echo e($items->withQueryString()->links()); ?>

    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/expense/index.blade.php ENDPATH**/ ?>