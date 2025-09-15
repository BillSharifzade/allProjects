<table>
    <tr>
        <td colspan="6" style="font-weight:bold;font-size:14pt">Отчет закрытия смены</td>
    </tr>
    <tr><td>Касса</td><td colspan="5"><?php echo e($cashbox->name ?? ''); ?></td></tr>
    <tr><td>Кассир</td><td colspan="5"><?php echo e($cashierName); ?></td></tr>
    <tr><td>Смена</td><td colspan="5">#<?php echo e($shift->id); ?> от <?php echo e(date('Y-m-d H:i', $shift->opened_at)); ?> до <?php echo e(date('Y-m-d H:i', $shift->closed_at)); ?></td></tr>
    <tr style="font-weight:bold;background:#f3f3f3"><td colspan="6">Итог по кассе</td></tr>
    <tr><td>Открытие</td><td><?php echo e(number_format($shift->opening_balance,2,'.',' ')); ?></td><td>Ожидаемый</td><td><?php echo e(number_format($expectedBalance,2,'.',' ')); ?></td><td>Фактический</td><td><?php echo e(number_format($shift->closing_balance,2,'.',' ')); ?></td></tr>
    <tr><td>Расчет по формуле</td><td><?php echo e(number_format($balanceComputed ?? 0,2,'.',' ')); ?></td><td>Расхождение (counted-expect)</td><td><?php echo e(number_format($shift->discrepancy,2,'.',' ')); ?></td><td>Ожидаемый-формула</td><td><?php echo e(number_format($balanceDelta ?? 0,2,'.',' ')); ?></td></tr>

    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6" style="font-weight:bold">Активный портфель (на момент закрытия)</td></tr>
    <tr><td>Сумма выданных</td><td><?php echo e(number_format($portfolio->initial_sum,2,'.',' ')); ?></td><td>Остаток</td><td><?php echo e(number_format($portfolio->left_sum,2,'.',' ')); ?></td><td colspan="2"></td></tr>

    <tr><td colspan="6"></td></tr>
    <tr style="font-weight:bold;background:#f3f3f3"><td colspan="6">Движение за смену</td></tr>
    <tr><td>Платежи: проценты</td><td><?php echo e(number_format($payments->interest,2,'.',' ')); ?></td><td>Платежи: основной</td><td><?php echo e(number_format($payments->principal,2,'.',' ')); ?></td><td colspan="2"></td></tr>

    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6" style="font-weight:bold">Расходы</td></tr>
    <tr><td>Категория</td><td>Сумма</td><td colspan="4">Описание</td></tr>
    <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($e->category); ?></td>
            <td><?php echo e(number_format($e->amount,2,'.',' ')); ?></td>
            <td colspan="4"><?php echo e($e->description); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr><td>Итого</td><td><?php echo e(number_format($expensesTotal,2,'.',' ')); ?></td><td colspan="4"></td></tr>

    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6" style="font-weight:bold">Переводы</td></tr>
    <tr><td>Тип</td><td>Сумма</td><td colspan="4">event_id</td></tr>
    <?php $__currentLoopData = $transfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td>
                <?php ($label = $t->event_type == 'admin_fund' ? 'Подкрепление основателя' : ($t->event_type == 'transfer_in' ? 'Подкрепление' : 'Передача')); ?>
                <?php echo e($label); ?>

            </td>
            <td><?php echo e(number_format($t->amount,2,'.',' ')); ?></td>
            <td colspan="4"><?php echo e($t->event_id); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr><td>Всего вход</td><td><?php echo e(number_format($transferIn,2,'.',' ')); ?></td><td>Всего выход</td><td><?php echo e(number_format($transferOut,2,'.',' ')); ?></td><td>Сальдо</td><td><?php echo e(number_format($transferNet ?? 0,2,'.',' ')); ?></td></tr>

    <tr><td colspan="6"></td></tr>
    <tr style="font-weight:bold;background:#f3f3f3"><td colspan="6">Проданные кредиты</td></tr>
    <tr>
        <td>Сумма закрытия</td>
        <td><?php echo e(number_format($salesTotal,2,'.',' ')); ?></td>
        <td>Прибыль</td>
        <td><?php echo e(number_format($salesProfit ?? 0,2,'.',' ')); ?></td>
        <td>Убыток</td>
        <td><?php echo e(number_format($salesLoss ?? 0,2,'.',' ')); ?></td>
    </tr>
    <tr>
        <td colspan="2">Чистый денежный эффект продажи</td>
        <td colspan="4"><?php echo e(number_format($netSalesCash ?? 0,2,'.',' ')); ?></td>
    </tr>

    <tr><td colspan="6"></td></tr>
    <tr style="font-weight:bold;background:#f3f3f3"><td colspan="6">Итог баланса</td></tr>
    <tr><td>Ожидаемый</td><td><?php echo e(number_format($expectedBalance,2,'.',' ')); ?></td><td>По формуле</td><td><?php echo e(number_format($balanceComputed ?? 0,2,'.',' ')); ?></td><td colspan="2"></td></tr>

    <tr><td colspan="6"></td></tr>
    <tr style="font-weight:bold;background:#f3f3f3"><td colspan="6">Реконсиляция портфеля</td></tr>
    <tr>
        <td>Остаток портфеля на конец</td>
        <td><?php echo e(number_format($portfolio->left_sum,2,'.',' ')); ?></td>
        <td>Выдано (за смену)</td>
        <td><?php echo e(number_format($disbursements ?? 0,2,'.',' ')); ?></td>
        <td>Погашено основного</td>
        <td><?php echo e(number_format($payments->principal,2,'.',' ')); ?></td>
    </tr>
    <tr>
        <td>Закрыто продажей (основной)</td>
        <td><?php echo e(number_format($salesPrincipalCleared ?? 0,2,'.',' ')); ?></td>
        <td>Расчетный остаток на начало</td>
        <td><?php echo e(number_format($portfolioStartLeft ?? 0,2,'.',' ')); ?></td>
        <td colspan="2"></td>
    </tr>
</table>

<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/exports/shift_close.blade.php ENDPATH**/ ?>