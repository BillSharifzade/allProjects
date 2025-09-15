<?php $__env->startSection('content'); ?>
    <h4 class="m-3">Пополнение кассы кассира</h4>
    <?php echo app('arrilot.widget')->run('Error'); ?>
    <form action="/admin/transfer" method="post" class="m-3" style="max-width: 420px;">
        <?php echo e(csrf_field()); ?>

        <div class="form-group">
            <label>Получатель</label>
            <select name="cashbox_user_id" class="form-control">
                <option value="">-- выберите --</option>
                <?php $__currentLoopData = $targets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($t->id); ?>"><?php echo e($t->user->last_name); ?> <?php echo e($t->user->first_name); ?> — <?php echo e($t->cashbox->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label>Сумма</label>
            <input type="number" step="0.01" min="0" class="form-control no-spin" name="amount" placeholder="0.00">
        </div>
        <button class="btn btn-primary">Отправить</button>
    </form>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/transfer/create.blade.php ENDPATH**/ ?>