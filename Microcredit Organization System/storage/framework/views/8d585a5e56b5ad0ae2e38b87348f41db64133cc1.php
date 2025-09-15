<?php $__env->startSection('content'); ?>
    <div class="card m-2 p-2">
    <table class="table table-light table-hover zebra">
        <thead class=" table-sm">
        <th>
            #
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
    </div>
    <br />
    <h5>Итог</h5>
    <p class="p-0 m-1">Процент: <strong><?php echo e($totalInterestPayments); ?> сомонӣ 00 дирам</strong></p>
    <p class="p-0 m-1">Основной кредит: <strong><?php echo e($totalPrincipalPayments); ?> сомонӣ 00 дирам</strong></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/payment/index.blade.php ENDPATH**/ ?>