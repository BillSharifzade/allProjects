<table >
    <tbody>
    <tr>
        <td colspan="9">Операции по ЗАЛОГОВЫМ БИЛЕТАМ с <?php echo e(request()->get('from')); ?> по <?php echo e(request()->get('to')); ?></td>
    </tr>
    <tr>
        <td colspan="9">Предприятие: ЧДММ Гаравхонаи Нигин</td>
    </tr>
    <tr>
        <td colspan="9">Ломбард: <?php echo e($data['cashbox']); ?></td>
    </tr>
    <tr>
        <td rowspan="2">Дата</td>
        <td rowspan="2">Код</td>
        <td rowspan="2" width="30">Номер договора</td>
        <td rowspan="2" width="30">Заемщик (ФИО)</td>
        <td>Расход</td>
        <td colspan="4">Приход</td>
    </tr>
    <tr>
        <td width="30">Займ</td>
        <td width="30">Закрытие</td>
        <td width="30">Проценты за пользование займом</td>
        <td width="30">Частичное погашение</td>
        <td width="30">Итого Приход</td>
    </tr>
        <?php
            $sum = 0;
            $closeSum = 0;
            $interestSum = 0;
            $principalSum = 0;
            $totalSum = 0;
        ?>

        <?php $__currentLoopData = $data['transactions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date=>$transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
            <td>
                <?php echo e($date); ?>

            </td>
            <td>

            </td>
            <td>
                <?php echo e(is_array($transaction) ? ($transaction['document_no'] ?? '') : (is_object($transaction) ? ($transaction->document_no ?? '') : '')); ?>

            </td>
            <td>
                <?php echo e(is_array($transaction) ? ($transaction['loaner'] ?? '') : (is_object($transaction) ? ($transaction->loaner ?? '') : '')); ?>

            </td>
            <td>
                <?php echo e($s = (float)(is_array($transaction) ? ($transaction['sum'] ?? 0) : (is_object($transaction) ? ($transaction->sum ?? 0) : 0))); ?>

                <?php ($sum += $s); ?>
            </td>
            <td>
                <?php echo e($cs = (float)(is_array($transaction) ? ($transaction['close_sum'] ?? 0) : (is_object($transaction) ? ($transaction->close_sum ?? 0) : 0))); ?>

                <?php ($closeSum += $cs); ?>
            </td>
            <td>
                <?php echo e($is = (float)(is_array($transaction) ? ($transaction['interest_sum'] ?? 0) : (is_object($transaction) ? ($transaction->interest_sum ?? 0) : 0))); ?>

                <?php ($interestSum += $is); ?>
            </td>
            <td>
                <?php echo e($ps = (float)(is_array($transaction) ? ($transaction['principal_sum'] ?? 0) : (is_object($transaction) ? ($transaction->principal_sum ?? 0) : 0))); ?>

                <?php ($principalSum += $ps); ?>
            </td>
            <td>
                <?php echo e($tot = $is + $ps + $cs); ?>

                <?php ($totalSum += $tot); ?>
            </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td colspan="9"></td>
        </tr>
        <tr>
            <td colspan="4"><strong>ИТОГ</strong></td>
            <td><strong><?php echo e($sum); ?></strong></td>
            <td><strong><?php echo e($closeSum); ?></strong></td>
            <td><strong><?php echo e($interestSum); ?></strong></td>
            <td><strong><?php echo e($principalSum); ?></strong></td>
            <td><strong><?php echo e($totalSum); ?></strong></td>
        </tr>

    </tbody>
</table>
<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/exports/cashbox_report.blade.php ENDPATH**/ ?>