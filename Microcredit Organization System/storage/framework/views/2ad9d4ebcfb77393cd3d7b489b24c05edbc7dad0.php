<?php $__env->startSection('content'); ?>
    <p class="flex justify-content-end">
        <a href="/admin/interest-rates/create" class="btn btn-primary text-uppercase">
            Добавить процентовку
        </a>
    </p>
    <?php echo app('arrilot.widget')->run('Error'); ?>
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Сумма от
        </th>
        <th>
            Сумма до
        </th>
        <th>
            Процент (месяц)
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        <?php $__currentLoopData = $interestRates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $interestRate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <?php echo e($key+1); ?>.
                </td>
                <td>
                    <?php echo e($interestRate->sum_from); ?>

                </td>
                <td>
                    <?php echo e($interestRate->sum_to); ?>

                </td>
                <td>
                    <?php echo e($interestRate->rate); ?>

                </td>
                <td style="width:90px;">
                    <a href="/admin/interest-rates/<?php echo e($interestRate->id); ?>/update">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/admin/interest-rates/<?php echo e($interestRate->id); ?>/delete" style="font-size: 16px" class="px-1">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/interestRate/index.blade.php ENDPATH**/ ?>