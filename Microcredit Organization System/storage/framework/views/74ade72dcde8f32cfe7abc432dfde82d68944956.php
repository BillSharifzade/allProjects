<table>
    <tr>
        <td colspan="15" style="font-weight:bold;font-size:14pt;text-align:center;background-color:#f0f0f0;">Месячный отчет</td>
    </tr>
    <tr>
        <th>Месяц</th>
        <th>Проценты</th>
        <th>Основной долг</th>
        <th>Выдачи</th>
        <th>Расходы</th>
        <th>Переводы вход</th>
        <th>Переводы исход</th>
        <th>Инвестиции (админ)</th>
        <th>Продажи (всего)</th>
        <th>Прибыль продаж</th>
        <th>Убыток продаж</th>
        <th>Портфель на начало</th>
        <th>Прирост портфеля</th>
        <th>Портфель на конец</th>
    </tr>
    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($r['month']); ?></td>
            <td><?php echo e(number_format($r['interest'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['principal'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['disbursements'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['expenses'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['transfer_in'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['transfer_out'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['admin_fund'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['sales_total'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['sales_profit'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['sales_loss'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['portfolio_start'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['portfolio_growth'], 2, '.', ' ')); ?></td>
            <td><?php echo e(number_format($r['portfolio_end'], 2, '.', ' ')); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td style="font-weight:bold;background-color:#f0f0f0;">ИТОГО</td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['interest'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['principal'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['disbursements'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['expenses'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['transfer_in'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['transfer_out'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['admin_fund'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['sales_total'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['sales_profit'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['sales_loss'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['portfolio_start'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['portfolio_growth'] ?? 0, 2, '.', ' ')); ?></td>
        <td style="font-weight:bold;"><?php echo e(number_format($totals['portfolio_end'] ?? 0, 2, '.', ' ')); ?></td>
    </tr>
</table>


<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/exports/monthly.blade.php ENDPATH**/ ?>