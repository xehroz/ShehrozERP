<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Purchase Orders Pending Approval</h1>
        <a href="/purchase/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to All POs
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Approval Queue for <?php echo ucfirst($userRole); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count($orders); ?> Purchase Orders Pending Your Review
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Pending Approvals</h6>
            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Search..." aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="approvalsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Amount (PKR)</th>
                            <th>Submitted By</th>
                            <th>Waiting Since</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No purchase orders pending your approval</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['po_number']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['po_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($order['supplier_name']); ?></td>
                                    <td class="text-right"><?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($order['created_by'] ?? 'System'); ?></td>
                                    <td><?php echo $this->timeElapsed($order['created_at']); ?></td>
                                    <td>
                                        <a href="/purchase/view/<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Review
                                        </a>
                                        <div class="btn-group btn-group-sm">
                                            <form method="post" action="/purchase/process-approval/<?php echo $order['id']; ?>" class="d-inline quick-approve-form">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="comments" value="Approved from queue">
                                                <button type="submit" class="btn btn-sm btn-success" title="Quick Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger quick-reject-btn" 
                                                    data-po-id="<?php echo $order['id']; ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#rejectModal"
                                                    title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">Reject Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="post" action="">
                <div class="modal-body">
                    <p>Please provide a reason for rejection:</p>
                    <div class="form-group">
                        <textarea class="form-control" id="rejectComments" name="comments" rows="3" required></textarea>
                    </div>
                    <input type="hidden" name="action" value="reject">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#approvalsTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Set up the reject modal
    const rejectButtons = document.querySelectorAll('.quick-reject-btn');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const poId = this.getAttribute('data-po-id');
            document.getElementById('rejectForm').action = `/purchase/process-approval/${poId}`;
        });
    });
    
    // Confirm quick approve
    const quickApproveForms = document.querySelectorAll('.quick-approve-form');
    quickApproveForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to approve this purchase order?')) {
                e.preventDefault();
            }
        });
    });
});
</script>