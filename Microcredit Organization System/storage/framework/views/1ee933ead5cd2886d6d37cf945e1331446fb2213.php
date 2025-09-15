<?php $__env->startSection('inc-content'); ?>
    <form method="get" action="/inc/safe" class="mb-2">
        <div class="form-group">
            <select name="cashbox" class="form-control" onchange="this.form.submit()">
                <option value="0">Все кассы</option>
                <?php $__currentLoopData = ($cashboxes ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e((int)($selectedCashbox ?? 0) === (int)$c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>
    
    <div class="table-responsive">
    <table class="table table-striped table-sm mb-2">
        <thead><tr><th>#</th><th>Касса</th><th>Договор</th><th>Клиент</th><th>Инфо</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($items->firstItem() + $i); ?></td>
                <td><?php echo e(optional($row->cashbox)->name); ?></td>
                <td><?php echo e($row->contract_no); ?></td>
                <td><?php echo e($row->client_name); ?></td>
                <td><?php echo e($row->loan_info); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    </div>
    <div style="margin-bottom: 120px;"><?php echo e($items->links()); ?></div>

    <div class="inc-actions bg-white border-top" style="position:fixed;left:0;right:0;bottom:0;z-index:1030;">
        <div class="p-2">
            <a href="/inc/safe/create" class="btn btn-primary btn-block d-flex align-items-center justify-content-center" style="min-height:48px; font-size:16px;">Добавить</a>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <style>
        body{padding-bottom:80px;}
    </style>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('incassator.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/incassator/safe/index.blade.php ENDPATH**/ ?>