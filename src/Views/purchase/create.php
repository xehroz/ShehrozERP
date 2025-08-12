<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Create Purchase Order</h1>
        <a href="/purchase/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Purchase Order Details</h6>
        </div>
        <div class="card-body">
            <form id="poForm" method="POST" action="/purchase/store">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="po_number"><strong>PO Number</strong></label>
                            <input type="text" class="form-control" id="po_number" name="po_number" value="<?php echo htmlspecialchars($nextPoNumber); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="po_date"><strong>PO Date</strong></label>
                            <input type="date" class="form-control" id="po_date" name="po_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="delivery_date"><strong>Expected Delivery Date</strong></label>
                            <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="supplier_id"><strong>Supplier</strong></label>
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>">
                                        <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Purchase Items</h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price (PKR)</th>
                                <th>Tax Rate (%)</th>
                                <th>Tax Amount (PKR)</th>
                                <th>Total (PKR)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="item-row-1">
                                <td>
                                    <select class="form-control item-select" name="items[0][item_id]" required>
                                        <option value="">Select Item</option>
                                        <?php foreach ($items as $item): ?>
                                            <option value="<?php echo $item['id']; ?>" 
                                                    data-price="<?php echo $item['current_price']; ?>"
                                                    data-unit="<?php echo $item['unit']; ?>">
                                                <?php echo htmlspecialchars($item['item_code'] . ' - ' . $item['item_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control item-description" name="items[0][description]">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" class="form-control item-quantity" name="items[0][quantity]" value="1" min="1" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text item-unit">Unit</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control item-price" name="items[0][unit_price]" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control item-tax-rate" name="items[0][tax_rate]" value="18.00" readonly>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control item-tax" name="items[0][tax_amount]" readonly>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control item-total" name="items[0][total]" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    <button type="button" class="btn btn-success btn-sm" id="add-item">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Subtotal:</strong></td>
                                <td colspan="3">
                                    <input type="number" step="0.01" class="form-control" id="subtotal" name="subtotal" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Tax Total:</strong></td>
                                <td colspan="3">
                                    <input type="number" step="0.01" class="form-control" id="tax_total" name="tax_amount" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                                <td colspan="3">
                                    <input type="number" step="0.01" class="form-control" id="grand_total" name="total_amount" readonly>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="form-group mt-3">
                    <label for="notes"><strong>Notes & Terms</strong></label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Purchase Order
                    </button>
                    <a href="/purchase/list" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowCount = 1;
    
    // Add new item row
    document.getElementById('add-item').addEventListener('click', function() {
        rowCount++;
        const newRow = document.querySelector('#item-row-1').cloneNode(true);
        newRow.id = 'item-row-' + rowCount;
        
        // Update field names with new index
        const inputs = newRow.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', '[' + (rowCount - 1) + ']'));
            }
            
            // Clear values except tax rate
            if (!input.classList.contains('item-tax-rate')) {
                input.value = input.type === 'number' ? (input.classList.contains('item-quantity') ? 1 : '') : '';
            }
        });
        
        // Reset unit text
        newRow.querySelector('.item-unit').textContent = 'Unit';
        
        document.querySelector('#itemsTable tbody').appendChild(newRow);
        
        // Reattach event listeners to the new row
        attachRowEventListeners(newRow);
    });
    
    // Remove item row
    function attachRemoveHandler(button) {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            if (document.querySelectorAll('#itemsTable tbody tr').length > 1) {
                row.remove();
                recalculateTotals();
            } else {
                alert('You must have at least one item.');
            }
        });
    }
    
    // Item selection change
    function attachItemSelectHandler(select) {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const option = this.options[this.selectedIndex];
            const price = option.dataset.price || '';
            const unit = option.dataset.unit || 'Unit';
            
            row.querySelector('.item-price').value = price;
            row.querySelector('.item-unit').textContent = unit;
            
            updateRowCalculations(row);
        });
    }
    
    // Quantity or price change
    function attachQuantityPriceHandler(input) {
        input.addEventListener('input', function() {
            updateRowCalculations(this.closest('tr'));
        });
    }
    
    // Update single row calculations
    function updateRowCalculations(row) {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const taxRate = parseFloat(row.querySelector('.item-tax-rate').value) || 0;
        
        const lineTotal = quantity * price;
        const taxAmount = lineTotal * (taxRate / 100);
        const totalWithTax = lineTotal + taxAmount;
        
        row.querySelector('.item-tax').value = taxAmount.toFixed(2);
        row.querySelector('.item-total').value = totalWithTax.toFixed(2);
        
        recalculateTotals();
    }
    
    // Recalculate all totals
    function recalculateTotals() {
        let subtotal = 0;
        let taxTotal = 0;
        
        document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const tax = parseFloat(row.querySelector('.item-tax').value) || 0;
            
            subtotal += quantity * price;
            taxTotal += tax;
        });
        
        const grandTotal = subtotal + taxTotal;
        
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('tax_total').value = taxTotal.toFixed(2);
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
    }
    
    // Attach event listeners to all rows
    function attachRowEventListeners(row) {
        attachRemoveHandler(row.querySelector('.remove-item'));
        attachItemSelectHandler(row.querySelector('.item-select'));
        attachQuantityPriceHandler(row.querySelector('.item-quantity'));
        attachQuantityPriceHandler(row.querySelector('.item-price'));
    }
    
    // Initialize event listeners for the first row
    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
        attachRowEventListeners(row);
    });
    
    // Form validation before submit
    document.getElementById('poForm').addEventListener('submit', function(e) {
        let valid = true;
        const requiredFields = this.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>