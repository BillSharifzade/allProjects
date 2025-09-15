<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->isAudit()): ?>
        <?php echo app('arrilot.widget')->run('cashboxFilter', ['closed' => false, 'from' => false, 'to' => false, 'audit'=> false]); ?>
    <?php else: ?>
        <?php echo app('arrilot.widget')->run('cashboxFilter', ['closed' => false, 'from' => false, 'to' => false]); ?>
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
            Телефон
        </th>
        <th>
            Залог
        </th>
        <th>
            Сумма
        </th>
        <th>
            Лготные дни
        </th>
        <th>
            Неоп. дни
        </th>
        <th>
            Остаток
        </th>
        <th>
            Дата
        </th>

        <?php if(request()->route()->getName() == 'admin-loans-close-requests'): ?>
            <th>
            Дата заявки
            </th>
        <?php endif; ?>

        <?php if(request()->route()->getName() == 'admin-closed-loans'): ?>
            <th>
            Дата закрытия
            </th>
        <?php endif; ?>

        <th>
            Действие
        </th>
        </thead>
        <tbody>
        <?php
            $counter = (\Request::get('page', 1) - 1) * 50
        ?>
        <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $counter = $counter + 1
            ?>
            <tr>
                <td>
                    <?php echo e($counter); ?>.
                </td>
                <td>
                    <?php echo e($loan->cashbox->name); ?><br />
                    <p class="badge badge-info"><?php echo e($loan->cashbox->nickname); ?></p>
                </td>
                <td>

                    <?php if($loan->audit_document_no > 0): ?>
                        №<?php echo e($loan->document_no); ?>-<?php echo e($loan->audit_document_no); ?>

                    <?php else: ?>
                        №<?php echo e($loan->document_no); ?>

                    <?php endif; ?>

                    <?php echo e($loan->full_name); ?>


                    <span class="badge badge-success"><?php echo e($loan->interestRate); ?>%</span>
                </td>
                <td>
                    <?php if($loan->loaner->phone1 != ''): ?>
                        <p class="p-0 m-0"><?php echo e($loan->loaner->phone1); ?></p>
                    <?php endif; ?>
                    <?php if($loan->loaner->phone2 != ''): ?>
                        <p class="p-0 m-0"><?php echo e($loan->loaner->phone2); ?></p>
                    <?php endif; ?>
                    <?php if($loan->loaner->phone3 != ''): ?>
                        <p class="p-0 m-0"><?php echo e($loan->loaner->phone3); ?></p>
                    <?php endif; ?>
                    <?php if($loan->loaner->phone4 != ''): ?>
                        <p class="p-0 m-0"><?php echo e($loan->loaner->phone4); ?></p>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($loan->collateral_type == 1): ?>
                        <?php $__currentLoopData = $loan->jewelries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jewelry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p class="p-0 m-0 font-size-sm"><?php echo e($jewelry->name); ?>, <?php echo e($jewelry->purity); ?> пр., <?php echo e($jewelry->weight); ?> гр.</p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php if($loan->collateral_type == 2 && $loan->auto): ?>
                        <p class="m-0 p-0"><strong>Марка:</strong> <?php echo e(optional($loan->auto)->brand); ?></p>
                        <p class="m-0 p-0"><strong>Год:</strong> <?php echo e(optional($loan->auto)->year); ?></p>
                        <p class="m-0 p-0"><strong>Цвет:</strong> <?php echo e(isset($loan->auto->color) ? \App\Constants::COLORS[$loan->auto->color] : ''); ?></p>
                        <p class="m-0 p-0"><strong>Гос. номер:</strong> <?php echo e(optional($loan->auto)->plate_number); ?></p>
                        <p class="m-0 p-0"><strong>Двигатель:</strong> <?php echo e(optional($loan->auto)->engine); ?></p>
                        <p class="m-0 p-0"><strong>Топливо:</strong> <?php echo e(isset($loan->auto->gas) ? \App\Constants::GAS[$loan->auto->gas] : ''); ?></p>
                        <p class="m-0 p-0"><strong>Трансмиссия:</strong> <?php echo e(isset($loan->auto->transmission) ? \App\Constants::TRANSMISSION[$loan->auto->transmission] : ''); ?></p>
                        <p class="m-0 p-0"><strong>Пробег:</strong> <?php echo e(optional($loan->auto)->mileage); ?></p>
                        <p class="m-0 p-0"><strong>Место хранения:</strong> <?php echo e(optional($loan->auto)->location); ?></p>
                        <p class="m-0 mt-2"><?php echo e(optional($loan->auto)->description); ?></p>
                    <?php endif; ?>
                    <?php if($loan->collateral_type == 3 && $loan->phone): ?>
                        <p class="m-0 p-0"><strong>Бренд:</strong> <?php echo e(optional($loan->phone)->brand); ?></p>
                        <p class="m-0 p-0"><strong>Модель:</strong> <?php echo e(optional($loan->phone)->model); ?></p>
                        <p class="m-0 p-0"><strong>Память:</strong> <?php echo e(optional($loan->phone)->storage_gb); ?> ГБ</p>
                        <p class="m-0 p-0"><strong>Цвет:</strong> <?php echo e(optional($loan->phone)->color); ?></p>
                        <p class="м-0 p-0"><strong>IMEI:</strong> <?php echo e(optional($loan->phone)->imei); ?></p>
                        <p class="m-0 p-0"><strong>Состояние:</strong> <?php echo e(optional($loan->phone)->condition); ?></p>
                        <p class="m-0 mt-2"><?php echo e(optional($loan->phone)->description); ?></p>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo e($loan->left_sum); ?> <span class="badge badge-light">(<?php echo e($loan->initial_sum); ?>)</span>
                    <span class="badge badge-warning">
                        <?php echo e($loan->daily_interest); ?>

                    </span>
                </td>
                <td>
                    <?php echo e($loan->grace_period); ?>

                </td>
                <td>
                    <?php echo e($loan->unpaid_days); ?>

                </td>
                <td>
                    <?php echo e($loan->unpaid_interest); ?>

                </td>
                <td>
                    <?php echo date('Y-m-d',$loan->lend_date); ?>
                </td>

                <?php if($loan->close_request_at > 0 && request()->route()->getName() == 'admin-loans-close-requests'): ?>
                    <td>
                        <?php echo date('Y-m-d',$loan->close_request_at); ?>
                    </td>
                <?php endif; ?>
                <?php if($loan->closed_at > 0 && request()->route()->getName() == 'admin-closed-loans'): ?>
                    <td>
                        <?php echo date('Y-m-d',$loan->closed_at); ?>
                    </td>
                <?php endif; ?>

                <td style="width:90px;">
                    <a href="/reporter/print/loan/<?php echo e($loan->id); ?>" style="font-size: 16px" class="px-1">
                        <i class="fas fa-print"></i>
                    </a>

                    <a href="/reporter/payments?loanId=<?php echo e($loan->id); ?>" style="font-size: 16px" class="px-1" target="_blank">
                        <i class="fas fa-receipt"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php if(count($loans) > 0): ?>
        <?php echo e($loans->appends($_GET)->links()); ?>

    <?php endif; ?>
    <?php if(isset($loansInitialSum)): ?>
        <br />
        <h5>Итог</h5>
        <p class="p-0 m-1">Кредиты: <strong><?php echo e((int)$loansInitialSum); ?> сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Остаток: <strong><?php echo e((int)$loansLeftSum); ?> сомонӣ 00 дирам</strong></p>
    <?php endif; ?>
    <?php if(isset($loanJewelries) && count($loanJewelries) > 0): ?>
        <?php $__currentLoopData = $loanJewelries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loanJewelry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="p-0 m-1">Проба <strong><?php echo e($loanJewelry->purity); ?></strong>: <?php echo e($loanJewelry->weight); ?> г.</p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/reporter/loan/index.blade.php ENDPATH**/ ?>