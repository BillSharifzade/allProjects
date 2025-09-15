<?php $__env->startSection('content'); ?>
<div class="container p-3">
    <h5>Месячные отчеты</h5>
    <?php echo app('arrilot.widget')->run('Error'); ?>
    <form method="get" action="/admin/reports/monthly/export" class="card p-3">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Касса</label>
                <select name="cashbox" class="form-control">
                    <option value="0">Все кассы</option>
                    <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cb->id); ?>"><?php echo e($cb->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>С (YYYY-MM)</label>
                <input type="month" name="from" class="form-control" value="<?php echo e(date('Y-m')); ?>" />
            </div>
            <div class="form-group col-md-4">
                <label>По (YYYY-MM)</label>
                <input type="month" name="to" class="form-control" />
            </div>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="all_time" name="all_time" value="1" onchange="toggleRange(this)">
            <label class="form-check-label" for="all_time">За всё время</label>
        </div>
        <div class="d-flex">
            <button type="submit" class="btn btn-primary">Экспортировать XLSX</button>
        </div>
    </form>
</div>
<script>
function toggleRange(cb){
    var dis = cb.checked;
    document.querySelector('[name=from]').disabled = dis;
    document.querySelector('[name=to]').disabled = dis;
}
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/monthly/index.blade.php ENDPATH**/ ?>