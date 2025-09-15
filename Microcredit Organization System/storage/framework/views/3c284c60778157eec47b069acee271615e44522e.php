<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h5 class="mb-3">Ежедневные отчёты по кассирам</h5>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
            <tr>
                <th>Касса</th>
                <th>Кассир</th>
                <th>Статус смены</th>
                <th>Последняя смена</th>
                <th class="text-right">Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $key = $link->user_id . ':' . $link->cashbox_id;
                    $ls = $latestShifts[$key][0] ?? null;
                    $isOpen = $ls && (int)$ls->closed_at === 0;
                ?>
                <tr>
                    <td><?php echo e(optional($link->cashbox)->name); ?></td>
                    <td><?php echo e(optional($link->user)->last_name); ?> <?php echo e(optional($link->user)->first_name); ?></td>
                    <td>
                        <?php if($ls): ?>
                            <?php if($isOpen): ?>
                                <span class="badge badge-success">Открыта</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Закрыта</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-light">Нет смен</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($ls): ?>
                            #<?php echo e($ls->id); ?>: <?php echo e(date('Y-m-d H:i', $ls->opened_at)); ?> — <?php echo e((int)$ls->closed_at>0 ? date('Y-m-d H:i', $ls->closed_at) : '...'); ?>

                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if($ls && (int)$ls->closed_at > 0): ?>
                            <a class="btn btn-sm btn-primary" href="/reporter/daily-report/<?php echo e($ls->id); ?>/download">Скачать отчет</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php echo e($items->links()); ?>

</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/reporter/daily_report/index.blade.php ENDPATH**/ ?>