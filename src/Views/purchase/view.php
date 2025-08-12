<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Purchase Order: <?php echo htmlspecialchars($order['po_number']); ?></h1>
        <div>
            <a href="/purchase/list" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="/purchase/pdf/<?php echo $order['id']; ?>" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <?php if ($order['status'] === 'draft'): ?>
                <a href="/purchase/edit/<?php echo $order['id']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-<?php echo $this->getStatusBadgeClass($order['status']); ?> shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?php echo $this->getStatusBadgeClass($order['status']); ?> text-uppercase mb-1">
                                Purchase Order Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $this->getStatusLabel($order['status']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- PO Details -->
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Purchase Order Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>PO Number:</strong><br> <?php echo htmlspecialchars($order['po_number']); ?></p>
                            <p><strong>PO Date:</strong><br> <?php echo date('d M Y', strtotime($order['po_date'])); ?></p>
                            <p><strong>Expected Delivery:</strong><br> <?php echo date('d M Y', strtotime($order['delivery_date'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created By:</strong><br> <?php echo htmlspecialchars($order['created_by'] ?? 'System'); ?></p>
                            <p><strong>Created On:</strong><br> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                            <?php if (!empty($order['last_updated_at'])): ?>
                                <p><strong>Last Updated:</strong><br> <?php echo date('d M Y H:i', strtotime($order['last_updated_at'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Details -->
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supplier Details</h6>
                </div>
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($order['supplier_name']); ?></h5>
                    <p><strong>Contact Person:</strong> <?php echo htmlspecialchars($order['contact_person'] ?? 'N/A'); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- PO Items -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Items</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th class="text-right">Unit Price (PKR)</th>
                            <th class="text-right">Tax Rate (%)</th>
                            <th class="text-right">Tax Amount (PKR)</th>
                            <th class="text-right">Total (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rowNum = 1; ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo $rowNum++; ?></td>
                                <td><?php echo htmlspecialchars($item['item_code'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($item['description'] ?? $item['item_name'] ?? 'N/A'); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="text-right"><?php echo number_format($item['unit_price'], 2); ?></td>
                                <td class="text-right"><?php echo number_format($item['tax_rate'], 2); ?>%</td>
                                <td class="text-right"><?php echo number_format($item['tax_amount'], 2); ?></td>
                                <td class="text-right"><?php echo number_format($item['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Subtotal:</strong></td>
                            <td colspan="2" class="text-right"><?php echo number_format($order['subtotal'], 2); ?> PKR</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Tax Total:</strong></td>
                            <td colspan="2" class="text-right"><?php echo number_format($order['tax_amount'], 2); ?> PKR</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Total Amount:</strong></td>
                            <td colspan="2" class="text-right"><strong><?php echo number_format($order['total_amount'], 2); ?> PKR</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Notes & Approval Section -->
    <div class="row">
        <!-- Notes -->
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notes & Terms</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($order['notes'])): ?>
                        <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                    <?php else: ?>
                        <p class="text-muted">No notes provided</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Approval Section -->
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Approval History</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($approvals)): ?>
                        <p class="text-muted">No approval actions yet</p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($approvals as $approval): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-<?php echo ($approval['action'] === 'approve') ? 'success' : 'danger'; ?>">
                                        <i class="fas fa-<?php echo ($approval['action'] === 'approve') ? 'check' : 'times'; ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">
                                            <?php echo htmlspecialchars($approval['approver_name']); ?> 
                                            <span class="badge bg-<?php echo ($approval['action'] === 'approve') ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($approval['action']); ?>
                                            </span>
                                        </h6>
                                        <p class="small text-muted mb-1">
                                            <?php echo date('d M Y H:i', strtotime($approval['created_at'])); ?>
                                        </p>
                                        <?php if (!empty($approval['comments'])): ?>
                                            <p class="mb-0"><?php echo htmlspecialchars($approval['comments']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Approval Action Buttons -->
                    <?php if ($canApprove): ?>
                        <hr>
                        <form method="post" action="/purchase/process-approval/<?php echo $order['id']; ?>">
                            <div class="form-group">
                                <label for="comments"><strong>Comments</strong></label>
                                <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button type="submit" name="action" value="approve" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-item:last-child {
    padding-bottom: 0;
}
.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    color: white;
    font-size: 10px;
}
.timeline-item:not(:last-child) .timeline-marker:before {
    content: '';
    position: absolute;
    top: 20px;
    left: 9px;
    height: calc(100% + 10px);
    width: 2px;
    background-color: #e3e6f0;
}
</style>