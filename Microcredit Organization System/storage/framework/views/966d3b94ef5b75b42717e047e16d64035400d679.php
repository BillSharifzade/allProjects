<?php $__env->startSection('content'); ?>
<div class="container">
    <h5>Просрочка 65+ (Экспорт)</h5>
    <form method="get" action="/admin/reports/overdue65">
        <div class="row">
            <div class="col-md-3">
                <label>Касса</label>
                <select name="cashbox" class="form-control">
                    <option value="0">Все</option>
                    <?php $__currentLoopData = ($cashboxes ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cb->id); ?>" <?php echo e((int)request('cashbox') === (int)$cb->id ? 'selected' : ''); ?>><?php echo e($cb->name); ?> (<?php echo e($cb->nickname); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>375</label>
                <input type="number" step="0.01" name="p375" class="form-control no-spin" value="<?php echo e(request('p375')); ?>"/>
            </div>
            <div class="col-md-2">
                <label>585</label>
                <input type="number" step="0.01" name="p585" class="form-control no-spin" value="<?php echo e(request('p585')); ?>"/>
            </div>
            <div class="col-md-2">
                <label>750</label>
                <input type="number" step="0.01" name="p750" class="form-control no-spin" value="<?php echo e(request('p750')); ?>"/>
            </div>
            <div class="col-md-2">
                <label>875</label>
                <input type="number" step="0.01" name="p875" class="form-control no-spin" value="<?php echo e(request('p875')); ?>"/>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-block" type="submit">Экспорт (XLSX)</button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/overdue65.blade.php ENDPATH**/ ?>