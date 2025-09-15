<?php $__env->startSection('content'); ?>
    <div class="m-3">
        <h4>История расходов</h4>
        <form method="get" class="form-inline my-2">
            <div class="form-group mr-2">
                <label class="mr-2">С</label>
                <input type="date" name="from" class="form-control" value="<?php echo e(request('from')); ?>">
            </div>
            <div class="form-group mr-2">
                <label class="mr-2">По</label>
                <input type="date" name="to" class="form-control" value="<?php echo e(request('to')); ?>">
            </div>
            <button class="btn btn-primary">Показать</button>
        </form>

        <?php if(isset($byCategory) && count($byCategory) > 0): ?>
            <h5 class="mt-3">Итого по категориям</h5>
            <table class="table table-sm table-bordered">
                <thead>
                <tr>
                    <th>Категория</th>
                    <th>Сумма</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $byCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($row->category); ?></td>
                        <td><?php echo e(number_format($row->sum, 2, '.', ' ')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endif; ?>

        <table class="table table-light mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Категория</th>
                <th>Описание</th>
                <th>Сумма</th>
                <th>Касса</th>
                <th>Кассир</th>
                <th>Дата</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($cu = $cashboxUsers[$i->user_id] ?? null); ?>
                <tr>
                    <td><?php echo e($i->id); ?></td>
                    <td><?php echo e($i->category); ?></td>
                    <td><?php echo e($i->description); ?></td>
                    <td><?php echo e(number_format($i->amount, 2, '.', ' ')); ?></td>
                    <td><?php echo e($cu ? $cu->cashbox->name : '-'); ?></td>
                    <td><?php echo e($cu ? ($cu->user->last_name . ' ' . $cu->user->first_name) : '-'); ?></td>
                    <td><?php echo date('Y-m-d',$i->occurred_at); ?></td>
                    <td>
                        <a href="/admin/expenses/<?php echo e($i->id); ?>/delete" class="text-danger" onclick="return confirm('Удалить расход и восстановить баланс?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <?php echo e($items->withQueryString()->links()); ?>

    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/expense/index.blade.php ENDPATH**/ ?>