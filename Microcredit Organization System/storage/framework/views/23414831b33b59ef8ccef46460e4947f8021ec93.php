<?php $__env->startSection('content'); ?>
    <div class="m-3">
        <h4>Новый расход</h4>
        <?php if(isset($balance)): ?>
            <div class="alert alert-info">Доступный баланс: <?php echo e(number_format($balance, 2, '.', ' ')); ?></div>
        <?php endif; ?>
        <?php echo app('arrilot.widget')->run('Error'); ?>
        <form method="post" action="/expenses/create">
            <?php echo e(csrf_field()); ?>

            <div class="form-group">
                <label>Категория</label>
                <select name="category" class="form-control">
                    <option value="">-- выберите --</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>" <?php echo e(old('category')===$cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group">
                <label>Описание</label>
                <input type="text" name="description" class="form-control" value="<?php echo e(old('description')); ?>" placeholder="Комментарий (необязательно)">
            </div>
            <div class="form-group">
                <label>Сумма</label>
                <input type="number" name="amount" class="form-control no-spin" min="0" step="0.01" value="<?php echo e(old('amount')); ?>" required oninput="checkExpenseBalance()">
                <small id="balanceWarning" class="text-danger d-none">Сумма превышает доступный баланс</small>
            </div>
            <button class="btn btn-primary">Сохранить</button>
        </form>
    </div>
    <script>
        function checkExpenseBalance(){
            var el = document.querySelector('input[name="amount"]');
            var warn = document.getElementById('balanceWarning');
            var bal = <?php echo e(isset($balance) ? number_format($balance,2,'.','') : '0.00'); ?>;
            var val = parseFloat(el.value || '0');
            if (!isNaN(val) && val > bal) {
                warn.classList.remove('d-none');
            } else {
                warn.classList.add('d-none');
            }
        }
    </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/expense/create.blade.php ENDPATH**/ ?>