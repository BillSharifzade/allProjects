<?php echo $__env->make('widgets.cashier_navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-4">
            <h5>Ожидают инкассации</h5>
            <div class="list-group">
                <?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-group-item">
                        <div><strong><?php echo e($t->contract_no); ?></strong> — <?php echo e($t->client_name); ?></div>
                        <div class="text-muted"><?php echo e($t->loan_info); ?></div>
                        <div class="small">Создано: <?php echo e(date('Y-m-d H:i', $t->created_at)); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="list-group-item text-muted">Нет записей</div>
                <?php endif; ?>
            </div>
            <?php echo e($pending->withQueryString()->links()); ?>

        </div>

        <div class="col-md-4">
            <h5>В пути</h5>
            <div class="list-group">
                <?php $__empty_1 = true; $__currentLoopData = $delivering; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-group-item">
                        <div><strong><?php echo e($t->contract_no); ?></strong> — <?php echo e($t->client_name); ?></div>
                        <div class="text-muted"><?php echo e($t->loan_info); ?></div>
                        <div class="small">Забрано: <?php echo e($t->picked_at ? date('Y-m-d H:i', $t->picked_at) : ''); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="list-group-item text-muted">Нет записей</div>
                <?php endif; ?>
            </div>
            <?php echo e($delivering->withQueryString()->links()); ?>

        </div>

        <div class="col-md-4">
            <h5>Ожидают приемки</h5>
            <form action="/incassation/accept" method="post">
                <?php echo e(csrf_field()); ?>

                <div class="list-group">
                    <?php $__empty_1 = true; $__currentLoopData = $awaiting; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <label class="list-group-item">
                            <input type="checkbox" name="ids[]" value="<?php echo e($t->id); ?>" />
                            <strong><?php echo e($t->contract_no); ?></strong> — <?php echo e($t->client_name); ?>

                            <div class="text-muted"><?php echo e($t->loan_info); ?></div>
                            <div class="small">Доставлено: <?php echo e($t->delivered_at ? date('Y-m-d H:i', $t->delivered_at) : ''); ?></div>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="list-group-item text-muted">Нет записей</div>
                    <?php endif; ?>
                </div>
                <div class="mt-2">
                    <button class="btn btn-success btn-sm">Принять выбранные</button>
                    <button formaction="/incassation/not-delivered" class="btn btn-outline-secondary btn-sm" type="submit">Вернуть в доставку</button>
                </div>
            </form>
            <?php echo e($awaiting->withQueryString()->links()); ?>

        </div>
    </div>
</div>


<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/incassation/index.blade.php ENDPATH**/ ?>