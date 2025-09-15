<?php $__env->startSection('content'); ?>
    <p class="flex justify-content-end">
        <a href="/notes/create?loan_id=<?php echo e(request()->get('loan_id')); ?>" class="btn btn-primary">
            ВНЕСТИ НОВУЮ ЗАМЕТКУ
        </a>
    </p>

    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th style="width: 40px">
            #
        </th>
        <th style="width: 250px">
            Кассир
        </th>
        <th>
            Текст
        </th>
        <th style="width: 200px">
            Дата
        </th>
        <th style="width: 90px">
            Действие
        </th>
        </thead>
        <tbody>
        <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <?php echo e($key+1); ?>.
                </td>
                <td>
                    <?php echo e($note->user->first_name . ' ' . $note->user->last_name); ?>

                </td>
                <td>
                    <?php echo e($note->text); ?>

                </td>
                <td>
                    <?php echo date('Y-m-d',$note->created_at->timestamp); ?>
                </td>
                <td>
                    <a href="/notes/<?php echo e($note->id); ?>/update">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($notes->appends($_GET)->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/note/index.blade.php ENDPATH**/ ?>