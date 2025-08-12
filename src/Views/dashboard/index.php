<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <p class="mb-4"><?php echo htmlspecialchars($pageDescription); ?></p>
    </div>
</div>

<!-- Dashboard Stats Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Pending Purchase Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($pendingPOs); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Upcoming Deliveries</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($upcomingDeliveries); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Low Stock Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($lowStockItems); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Active Suppliers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($recentSuppliers); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Pending Purchase Orders -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pending Purchase Orders</h6>
                <a href="/purchase/list" class="btn btn-sm btn-primary shadow-sm">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingPOs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No pending purchase orders</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendingPOs as $po): ?>
                                    <tr>
                                        <td><a href="/purchase/view/<?php echo $po['id']; ?>"><?php echo htmlspecialchars($po['id']); ?></a></td>
                                        <td><?php echo htmlspecialchars($po['supplier']); ?></td>
                                        <td><?php echo htmlspecialchars($po['date']); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($po['amount'], 2)); ?></td>
                                        <td>
                                            <?php
                                                $statusClass = '';
                                                $statusText = 'Unknown';
                                                
                                                switch ($po['status']) {
                                                    case PO_STATUS_DRAFT:
                                                        $statusClass = 'secondary';
                                                        $statusText = 'Draft';
                                                        break;
                                                    case PO_STATUS_PENDING_APPROVAL:
                                                        $statusClass = 'warning';
                                                        $statusText = 'Pending Approval';
                                                        break;
                                                    case PO_STATUS_APPROVED:
                                                        $statusClass = 'success';
                                                        $statusText = 'Approved';
                                                        break;
                                                    case PO_STATUS_REJECTED:
                                                        $statusClass = 'danger';
                                                        $statusText = 'Rejected';
                                                        break;
                                                }
                                            ?>
                                            <span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
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

    <!-- Upcoming Deliveries -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Upcoming Deliveries</h6>
                <a href="/purchase/deliveries" class="btn btn-sm btn-primary shadow-sm">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Supplier</th>
                                <th>Expected</th>
                                <th>Items</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($upcomingDeliveries)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No upcoming deliveries</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($upcomingDeliveries as $delivery): ?>
                                    <tr>
                                        <td><a href="/purchase/view/<?php echo $delivery['id']; ?>"><?php echo htmlspecialchars($delivery['id']); ?></a></td>
                                        <td><?php echo htmlspecialchars($delivery['supplier']); ?></td>
                                        <td><?php echo htmlspecialchars($delivery['expectedDate']); ?></td>
                                        <td><?php echo htmlspecialchars($delivery['items']); ?></td>
                                        <td>
                                            <?php
                                                $statusClass = '';
                                                $statusText = 'Unknown';
                                                
                                                switch ($delivery['status']) {
                                                    case PO_STATUS_SENT:
                                                        $statusClass = 'info';
                                                        $statusText = 'Sent';
                                                        break;
                                                    case PO_STATUS_PARTIALLY_RECEIVED:
                                                        $statusClass = 'primary';
                                                        $statusText = 'Partially Received';
                                                        break;
                                                }
                                            ?>
                                            <span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
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
</div>

<!-- Content Row -->
<div class="row">
    <!-- Low Stock Items -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Low Stock Items</h6>
                <a href="/inventory/low-stock" class="btn btn-sm btn-primary shadow-sm">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>Current</th>
                                <th>Min</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lowStockItems)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No low stock items</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lowStockItems as $item): ?>
                                    <tr>
                                        <td><a href="/inventory/item/<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['id']); ?></a></td>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td class="<?php echo $item['currentStock'] < $item['minStock'] ? 'text-danger' : ''; ?>">
                                            <?php echo htmlspecialchars($item['currentStock']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['minStock']); ?></td>
                                        <td><?php echo htmlspecialchars($item['supplier']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Suppliers -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Suppliers</h6>
                <a href="/suppliers" class="btn btn-sm btn-primary shadow-sm">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Last Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentSuppliers)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No suppliers found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentSuppliers as $supplier): ?>
                                    <tr>
                                        <td><a href="/suppliers/view/<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></a></td>
                                        <td><?php echo htmlspecialchars($supplier['contact']); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['lastOrder']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>