<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Purchase Orders</h1>
        <a href="/purchase/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Purchase Order
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Order List</h6>
            <div class="d-flex">
                <div class="input-group mr-2" style="width: 250px;">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search..." aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="?status=all">All Orders</a></li>
                        <li><a class="dropdown-item" href="?status=draft">Draft</a></li>
                        <li><a class="dropdown-item" href="?status=pending_approval">Pending Approval</a></li>
                        <li><a class="dropdown-item" href="?status=approved">Approved</a></li>
                        <li><a class="dropdown-item" href="?status=rejected">Rejected</a></li>
                        <li><a class="dropdown-item" href="?status=completed">Completed</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="poTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Amount (PKR)</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No purchase orders found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['po_number']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['po_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($order['supplier_name']); ?></td>
                                    <td class="text-right"><?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?php echo $this->getStatusBadgeClass($order['status']); ?>">
                                            <?php echo $this->getStatusLabel($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['created_by'] ?? 'System'); ?></td>
                                    <td>
                                        <a href="/purchase/view/<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($order['status'] === 'draft'): ?>
                                            <a href="/purchase/edit/<?php echo $order['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="/purchase/pdf/<?php echo $order['id']; ?>" class="btn btn-sm btn-secondary" title="Download PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (!empty($orders)): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        // Show 5 pagination links
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $startPage + 4);
                        
                        // Adjust start page if needed
                        if ($endPage - $startPage < 4) {
                            $startPage = max(1, $endPage - 4);
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++): 
                        ?>
                            <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
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
            const tableRows = document.querySelectorAll('#poTable tbody tr');
            
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
});
</script>