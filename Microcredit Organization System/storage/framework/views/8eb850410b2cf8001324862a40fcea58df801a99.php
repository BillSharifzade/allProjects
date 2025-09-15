<?php $__env->startSection('content'); ?>
    <p class="flex justify-content-end">
        <a href="/payments/<?php echo e($loan->id); ?>/create" class="btn btn-primary locked-hide">
            ВНЕСТИ НОВЫЙ ПЛАТЕЖ
        </a>
    </p>

    <table class="m-2 table table-light">
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
            <th>
                Действие
            </th>
        </thead>
        <tbody>
        <?php $__currentLoopData = $loan->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <?php echo e($key+1); ?>.
                </td>
                <td>

                    <?php if($loan->audit_document_no > 0): ?>
                        №<?php echo e($loan->document_no); ?>-<?php echo e($loan->audit_document_no); ?>

                    <?php else: ?>
                        №<?php echo e($loan->document_no); ?>

                    <?php endif; ?>

                    <?php echo e($loan->loaner->full_name); ?>

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
                <td>
                    <a href="/print/payment?uuid=<?php echo e($payment->uuid); ?>" style="font-size: 16px" class="px-1">
                        <i class="fas fa-print"></i>
                    </a>

                    <a href="/print/receipt?uuid=<?php echo e($payment->uuid); ?>" style="font-size: 16px" class="px-1">
                        <i class="fas fa-print" style="color: red;"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/payment/loan.blade.php ENDPATH**/ ?>