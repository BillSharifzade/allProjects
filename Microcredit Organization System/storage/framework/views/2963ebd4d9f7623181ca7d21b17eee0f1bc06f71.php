<?php $__env->startSection('content'); ?>
    <h5 class="m-2">Доставленные инкассатором (ожидают приема)</h5>
    <form method="post" action="/incassation/accept" class="m-2">
        <?php echo e(csrf_field()); ?>

        <table class="table table-sm table-striped">
            <thead>
            <tr>
                <th></th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Доставлено</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?php echo e($row->id); ?>" /></td>
                    <td><?php echo e($row->contract_no); ?></td>
                    <td><?php echo e($row->client_name); ?></td>
                    <td><?php echo e($row->loan_info); ?></td>
                    <td><?php echo date('Y-m-d',$row->delivered_at); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="d-flex mb-3">
            <button class="btn btn-success mr-2">Принять выбранные</button>
            <button class="btn btn-outline-danger" formaction="/incassation/not-delivered" formmethod="post">Не доставлено</button>
        </div>
    </form>
    <?php echo e($items->links()); ?>


    <hr />
    <h5 class="m-2">Ожидают доставки инкассатором</h5>
    <table class="table table-sm table-striped m-2">
        <thead>
        <tr>
            <th>Договор</th><th>Клиент</th><th>Инфо</th><th>Статус</th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = ($toDeliver ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($row->contract_no); ?></td>
                <td><?php echo e($row->client_name); ?></td>
                <td><?php echo e($row->loan_info); ?></td>
                <td>
                    <?php if($row->picked_by_incassator): ?>
                        <span class="badge badge-info">Забрано инкассатором</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Ожидает инкассатора</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/incassation/accept.blade.php ENDPATH**/ ?>