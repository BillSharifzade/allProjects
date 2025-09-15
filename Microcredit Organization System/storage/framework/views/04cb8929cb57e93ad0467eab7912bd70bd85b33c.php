<?php $__env->startSection('content'); ?>
    <?php if(request()->get('filter') != 'close_requests'): ?>
        <?php echo app('arrilot.widget')->run('cashboxLoanFilters'); ?>
    <?php endif; ?>
    <div class="card m-2 p-2">
    <table class="table table-light table-hover zebra loans-table">
        <thead class=" table-sm">
            <th>
                #
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
                Неоп. дни
            </th>
            <th>
                Остаток
            </th>
            <th>
                Дата
            </th>
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

                    <?php if($loan->audit_document_no > 0): ?>
                        №<?php echo e($loan->document_no); ?>-<?php echo e($loan->audit_document_no); ?>

                    <?php else: ?>
                        №<?php echo e($loan->document_no); ?>

                    <?php endif; ?>

                    <?php echo e(optional($loan->loaner)->full_name); ?>


                        <?php ($monthlyRate = isset($loan->interestRate) ? $loan->interestRate : (isset($loan->interest_rate) ? $loan->interest_rate : 0)); ?>
                        <span class="badge badge-success"><?php echo e($monthlyRate); ?>%</span>
                </td>
                <td>
                    <?php if(optional($loan->loaner)->phone1 != ''): ?>
                        <p class="p-0 m-0"><?php echo e(optional($loan->loaner)->phone1); ?></p>
                    <?php endif; ?>
                    <?php if(optional($loan->loaner)->phone2 != ''): ?>
                        <p class="p-0 m-0"><?php echo e(optional($loan->loaner)->phone2); ?></p>
                    <?php endif; ?>
                    <?php if(optional($loan->loaner)->phone3 != ''): ?>
                        <p class="p-0 m-0"><?php echo e(optional($loan->loaner)->phone3); ?></p>
                    <?php endif; ?>
                    <?php if(optional($loan->loaner)->phone4 != ''): ?>
                        <p class="p-0 m-0"><?php echo e(optional($loan->loaner)->phone4); ?></p>
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
                            <p class="m-0 p-0"><strong>IMEI:</strong> <?php echo e(optional($loan->phone)->imei); ?></p>
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
                    <?php echo e($loan->unpaid_days); ?>

                </td>
                <td>
                    <?php echo e($loan->unpaid_interest); ?>

                </td>
                <td>
                    <?php echo date('Y-m-d',$loan->lend_date); ?>
                </td>
                <td>
                    <?php if(request()->get('filter') != 'close_requests'): ?>

                        <a href="/loans/<?php echo e($loan->id); ?>/update" class="locked-hide">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/payments?loanId=<?php echo e($loan->id); ?>" style="font-size: 16px" class="px-1 locked-hide" title="Платежи">
                            <i class="fas fa-receipt"></i>
                        </a>
                        <a href="/print/loan/<?php echo e($loan->id); ?>" style="font-size: 16px" class="px-1">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="/print/withdrawal/<?php echo e($loan->id); ?>" style="font-size: 16px" class="px-1">
                            <i class="fas fa-print" style="color:red;"></i>
                        </a>
                        <a href="/notes?loan_id=<?php echo e($loan->id); ?>" style="font-size: 16px" class="locked-hide">
                            <i class="fa-solid fa-comment"></i>
                        </a>
                        <?php if($loan->unpaid_days >= 70): ?>
                            <a href="#" class="d-inline locked-hide" data-toggle="modal" data-target="#sellModal<?php echo e($loan->id); ?>" title="Продать">
                                <i class="fas fa-hand-holding-usd text-danger"></i>
                            </a>

                            <div class="modal fade" id="sellModal<?php echo e($loan->id); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Продать залог по договору <?php echo e('№' . $loan->document_no . ($loan->audit_document_no>0?('-'.$loan->audit_document_no):'')); ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="/sales/<?php echo e($loan->id); ?>" method="post">
                                            <?php echo e(csrf_field()); ?>

                                            <div class="modal-body">
                                                <?php if($loan->collateral_type == 1): ?>
                                                    <div class="form-row">
                                                        <div class="form-group col-6">
                                                            <label>Цена 375</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_375" required>
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label>Цена 585</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_585" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-6">
                                                            <label>Цена 750</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_750" required>
                                                        </div>
                                                        <div class="form-group col-6">
                                                            <label>Цена 875</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" name="price_875" required>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="form-group">
                                                        <label>Сумма продажи (сомони)</label>
                                                        <input type="number" step="0.01" min="0" class="form-control" name="proceeds" required>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                                <button type="submit" class="btn btn-danger">Продать</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($loan->left_sum == 0): ?>
                        <a href="/loans/<?php echo e($loan->id); ?>/close" style="font-size: 16px" class="px-1">
                            <i class="fas fa-times-circle <?php echo e($loan->close_request_at > 0 ? 'text-danger' : ''); ?>"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="px-2"><?php echo e($loans->appends($_GET)->links()); ?></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/loan/index.blade.php ENDPATH**/ ?>