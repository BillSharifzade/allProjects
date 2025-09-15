<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->isAudit()): ?>
        <?php echo app('arrilot.widget')->run('cashboxFilter', ['closed' => false, 'audit' => false]); ?>
    <?php else: ?>
        <?php echo app('arrilot.widget')->run('cashboxFilter', ['closed' => false]); ?>
    <?php endif; ?>
    <br />
    <?php if($loansInitialSum > 0 || $loansLeftSum > 0 || $principalPaymentsTotalSum > 0 || $interestPaymentsTotalSum > 0): ?>
        <h5>Итог</h5>
        <p class="p-0 m-1">Сумма кредитов: <strong><?php echo e((int)$loansInitialSum); ?> сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Остаток кредитов: <strong><?php echo e((int)$loansLeftSum); ?> сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Сумма погащений основных кредитов: <strong><?php echo e((int)$principalPaymentsTotalSum); ?> сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Сумма погащений процентов: <strong><?php echo e((int)$interestPaymentsTotalSum); ?> сомонӣ 00 дирам</strong></p>
        <p><a href="/reporter/excel/cashbox?from=<?php echo e(request()->get('from')); ?>&to=<?php echo e(request()->get('to')); ?>&cashbox=<?php echo e(request()->get('cashbox')); ?>&audit=<?php echo e(request()->get('audit')); ?>">Загузить в Excel</a></p>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/reporter/report/index.blade.php ENDPATH**/ ?>