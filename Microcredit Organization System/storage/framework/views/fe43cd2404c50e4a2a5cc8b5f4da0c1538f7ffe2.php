<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex align-items-center mb-3">
            <h5 class="mr-3">Архив кредитов</h5>
        </div>

        <table class="table table-sm table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Кредит</th>
                <th>Тип</th>
                <th>Дата архивации</th>
                <th style="width:240px">Вся информация</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($it->id); ?></td>
                    <td>
                        <div class="small">Loan ID: <strong><?php echo e($it->loan_id); ?></strong></div>
                    </td>
                    <td><span class="badge <?php echo e($it->type==='deleted' ? 'badge-danger' : 'badge-dark'); ?>"><?php echo e($it->type); ?></span></td>
                    <td><?php echo date('Y-m-d',$it->archived_at); ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#snap-<?php echo e($it->id); ?>">Показать</button>
                    </td>
                </tr>
                <tr class="collapse" id="snap-<?php echo e($it->id); ?>">
                    <td colspan="5">
                        <?php ($snap = json_decode($it->snapshot, true) ?? []); ?>
                        <?php ($loan = $snap['loan'] ?? []); ?>
                        <?php ($loaner = $snap['loaner'] ?? []); ?>
                        <?php ($auto = $snap['auto'] ?? []); ?>
                        <?php ($phone = $snap['phone'] ?? []); ?>
                        <?php ($jewelries = $snap['jewelries'] ?? []); ?>
                        <?php ($payments = $snap['payments'] ?? []); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-2">
                                    <div class="card-header">Кредит</div>
                                    <div class="card-body small">
                                        <div>Номер: №<?php echo e($loan['document_no'] ?? ''); ?><?php if(($loan['audit_document_no'] ?? 0) > 0): ?>-<?php echo e($loan['audit_document_no']); ?><?php endif; ?></div>
                                        <div>Сумма: <?php echo e($loan['initial_sum'] ?? 0); ?> | Остаток: <?php echo e($loan['left_sum'] ?? 0); ?></div>
                                        <div>Дата выдачи: <?php echo date('Y-m-d',$loan['lend_date'] ?? 0); ?></div>
                                        <div>Тип залога: <?php echo e(['','Золото','Авто','Телефон'][$loan['collateral_type'] ?? 0] ?? ''); ?></div>
                                        <div>Статус: <?php echo e($it->type==='deleted' ? 'Удалён' : 'Закрыт'); ?></div>
                                    </div>
                                </div>
                                <div class="card mb-2">
                                    <div class="card-header">Клиент</div>
                                    <div class="card-body small">
                                        <div><?php echo e($loaner['full_name'] ?? ''); ?></div>
                                        <div>Тел.: <?php echo e($loaner['phone1'] ?? ''); ?></div>
                                        <div>Паспорт: <?php echo e($loaner['passport_number'] ?? ''); ?></div>
                                        <div>Адрес: <?php echo e($loaner['residence_address'] ?? ''); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if(!empty($jewelries)): ?>
                                <div class="card mb-2">
                                    <div class="card-header">Золото</div>
                                    <div class="card-body small">
                                        <?php $__currentLoopData = $jewelries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div><?php echo e($j['name'] ?? ''); ?>, <?php echo e($j['purity'] ?? ''); ?> пр., <?php echo e($j['weight'] ?? ''); ?> гр.</div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if(!empty($auto)): ?>
                                <div class="card mb-2">
                                    <div class="card-header">Авто</div>
                                    <div class="card-body small">
                                        <div>Марка: <?php echo e($auto['brand'] ?? ''); ?></div>
                                        <div>Год: <?php echo e($auto['year'] ?? ''); ?></div>
                                        <div>Номер: <?php echo e($auto['plate_number'] ?? ''); ?></div>
                                        <div>Двигатель: <?php echo e($auto['engine'] ?? ''); ?></div>
                                        <div>Пробег: <?php echo e($auto['mileage'] ?? ''); ?></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if(!empty($phone)): ?>
                                <div class="card mb-2">
                                    <div class="card-header">Телефон</div>
                                    <div class="card-body small">
                                        <div>Модель: <?php echo e(($phone['brand'] ?? '') . ' ' . ($phone['model'] ?? '')); ?></div>
                                        <div>IMEI: <?php echo e($phone['imei'] ?? ''); ?></div>
                                        <div>Память: <?php echo e($phone['storage_gb'] ?? ''); ?> GB</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card mb-2">
                            <div class="card-header">Платежи (<?php echo e(count($payments)); ?>)</div>
                            <div class="card-body small">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead><tr><th>#</th><th>Тип</th><th>Сумма</th><th>Дата</th></tr></thead>
                                        <tbody>
                                        <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($p['id'] ?? ''); ?></td>
                                                <td><?php echo e((isset($p['type']) && (int)$p['type']===\App\Constants::PAYMENT_INTEREST) ? 'Проценты' : 'Основной долг'); ?></td>
                                                <td><?php echo e($p['sum'] ?? 0); ?></td>
                                                <td><?php echo date('Y-m-d',$p['paid_date'] ?? 0); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php echo e($items->links()); ?>

    </div>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/archive/index.blade.php ENDPATH**/ ?>