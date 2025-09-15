<?php $__env->startSection('content'); ?>
    <p class="flex justify-content-end">
        <a href="/admin/gold-prices/create" class="btn btn-primary text-uppercase">
            Добавить цену
        </a>
    </p>
    <?php echo app('arrilot.widget')->run('Error'); ?>
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th style="width: 10px">
            #
        </th>
        <th>
            Проба
        </th>
        <th>
            Сумма
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        <?php $__currentLoopData = $goldPrices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $goldPrice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <?php echo e($key+1); ?>.
                </td>
                <td>
                    <?php echo e($goldPrice->purity); ?>

                </td>
                <td>
                    <?php echo e($goldPrice->price); ?>

                </td>
                <td style="width:90px;">
                    <a href="/admin/gold-prices/<?php echo e($goldPrice->id); ?>/update">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/admin/gold-prices/<?php echo e($goldPrice->id); ?>/delete" style="font-size: 16px" class="px-1">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/goldPrice/index.blade.php ENDPATH**/ ?>