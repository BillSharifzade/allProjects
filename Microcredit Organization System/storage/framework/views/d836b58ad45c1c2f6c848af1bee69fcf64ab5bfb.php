<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->isAudit()): ?>
        <?php echo app('arrilot.widget')->run('cashboxFilter', ['closed' => false, 'audit' => false]); ?>
    <?php else: ?>
        <?php echo app('arrilot.widget')->run('cashboxFilter', ['closed' => false]); ?>
    <?php endif; ?>

    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Касса
        </th>
        <th>
            ФИО
        </th>
        <th>
            Сумма платежа
        </th>
        <th>
            Тип
        </th>
        <th>
            Дата платежа
        </th>
        </thead>
        <tbody>
        <?php
            $counter = (\Request::get('page', 1) - 1) * 50
        ?>
        <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $counter = $counter + 1
            ?>
            <?php
                $paymentLoan = null;
            ?>

            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php if($loan->id == $payment->loan_id): ?>

                    <?php
                        $paymentLoan = $loan;
                    ?>

                <?php endif; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td>
                    <?php echo e($counter); ?>.
                </td>
                <td>
                    <?php echo e($paymentLoan->cashbox->name); ?><br/>
                    <p class="badge badge-info"><?php echo e($paymentLoan->cashbox->nickname); ?></p>
                </td>
                <td>
                    <?php if($paymentLoan->audit_document_no > 0): ?>
                        №<?php echo e($paymentLoan->document_no); ?>-<?php echo e($paymentLoan->audit_document_no); ?>

                    <?php else: ?>
                        №<?php echo e($paymentLoan->document_no); ?>

                    <?php endif; ?>

                    <?php echo e($paymentLoan->loaner->full_name); ?>

                </td>
                <td>
                    <?php echo e($payment->sum); ?>

                </td>
                <td>
                    <?php if($payment->type == \App\Constants::PAYMENT_INTEREST): ?>
                        Процент
                    <?php else: ?>
                        Основной кредит
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo date('Y-m-d',$payment->paid_date); ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php if(count($payments) > 0): ?>
        <?php echo e($payments->appends($_GET)->links()); ?>

    <?php endif; ?>
    <br />
    <h5>Итог</h5>
    <p class="p-0 m-1">Основной кредит: <strong><?php echo e((int)$principalPaymentsTotalSum); ?> сомонӣ 00 дирам</strong></p>
    <p class="p-0 m-1">Процент: <strong><?php echo e((int)$interestPaymentsTotalSum); ?> сомонӣ 00 дирам</strong></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/reporter/payment/index.blade.php ENDPATH**/ ?>