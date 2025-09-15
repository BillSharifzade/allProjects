<?php $__env->startSection('content'); ?>
    <p class="flex justify-content-end">
        <a href="/admin/cashboxes/create" class="btn btn-primary text-uppercase">
            Добавить кассу
        </a>
    </p>
    <?php echo app('arrilot.widget')->run('Error'); ?>
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Название
        </th>
        <th>
            Внутреннее название
        </th>
        <th>
            Лицензия кассы
        </th>
        <th>
            Адрес
        </th>
        <th>
            Телефон
        </th>
        <th>
            Дата
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cashbox): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <?php echo e($key+1); ?>.
                </td>
                <td>
                    <?php echo e($cashbox->name); ?>


                </td>
                <td>
                    <p class="badge badge-info"><?php echo e($cashbox->nickname); ?></p>
                </td>
                <td>
                    <?php echo e($cashbox->license); ?>

                </td>
                <td>
                    <?php echo e($cashbox->address); ?>

                </td>

                <td>
                    <?php echo e($cashbox->phone); ?>

                </td>
                <td>
                    <?php echo date('Y-m-d',$cashbox->created_at->timestamp); ?>
                </td>
                <td style="width:90px;">
                    <a href="/admin/cashboxes/<?php echo e($cashbox->id); ?>/update">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/admin/cashboxes/<?php echo e($cashbox->id); ?>/delete" style="font-size: 16px" class="px-1">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/cashbox/index.blade.php ENDPATH**/ ?>