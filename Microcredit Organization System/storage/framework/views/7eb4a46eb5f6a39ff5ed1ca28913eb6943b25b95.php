<?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashbox => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <table>
        <thead>
        <tr>
            <th colspan="9">Касса: <?php echo e($cashbox ?: 'Без кассы'); ?></th>
        </tr>
        <tr>
            <th>Договор</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Кассир</th>
            <th>Залог</th>
            <th>Сумма</th>
            <th>Неоп. дни</th>
            <th>Неопл. проценты</th>
            <th>Дата выдачи</th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($r['document_full']); ?></td>
                <td><?php echo e($r['full_name']); ?></td>
                <td><?php echo e($r['phone']); ?></td>
                <td><?php echo e($r['cashier']); ?></td>
                <td><?php echo e($r['collateral']); ?></td>
                <td><?php echo e($r['initial_sum']); ?></td>
                <td><?php echo e($r['unpaid_days']); ?></td>
                <td><?php echo e($r['unpaid_interest']); ?></td>
                <td><?php echo e($r['lend_date']); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <?php if(!empty($groupTotals) && isset($groupTotals[$cashbox ?: 'Без кассы'])): ?>
            <tfoot>
            <tr>
                <td colspan="8"><strong>Итого по кассе — Сумма кредитов</strong></td>
                <td><strong><?php echo e($groupTotals[$cashbox ?: 'Без кассы']['loan']); ?></strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>Итого по кассе — Золото</strong></td>
                <td><strong><?php echo e($groupTotals[$cashbox ?: 'Без кассы']['gold']); ?></strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>Итого по кассе — Неопл. проценты</strong></td>
                <td><strong><?php echo e($groupTotals[$cashbox ?: 'Без кассы']['interest']); ?></strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>Итого (кредиты + проценты)</strong></td>
                <td><strong><?php echo e($groupTotals[$cashbox ?: 'Без кассы']['grand']); ?></strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>После вычета стоимости золота</strong></td>
                <td><strong><?php echo e($groupTotals[$cashbox ?: 'Без кассы']['after']); ?></strong></td>
            </tr>
            </tfoot>
        <?php endif; ?>
    </table>
    <br/>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if(!empty($totals)): ?>
    <table>
        <thead>
        <tr><th colspan="2">Итоги</th></tr>
        </thead>
        <tbody>
        <tr>
            <td>Сумма кредитов</td><td><?php echo e($totals['total_loan']); ?></td>
        </tr>
        <tr>
            <td>Сумма неоплаченных процентов</td><td><?php echo e($totals['total_unpaid_interest']); ?></td>
        </tr>
        <tr>
            <td>Стоимость золота (375=<?php echo e($totals['p375']); ?>, 585=<?php echo e($totals['p585']); ?>, 750=<?php echo e($totals['p750']); ?>, 875=<?php echo e($totals['p875']); ?>)</td>
            <td><?php echo e($totals['gold_worth']); ?></td>
        </tr>
        <tr>
            <td>Итого (кредиты + проценты)</td><td><?php echo e($totals['grand_total']); ?></td>
        </tr>
        <tr>
            <td>После вычета стоимости золота</td><td><?php echo e($totals['after_gold_offset']); ?></td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>


<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/exports/overdue65.blade.php ENDPATH**/ ?>