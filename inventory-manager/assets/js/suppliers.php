jQuery(document).ready(function($) {
    'use strict';

    // Supplier form enhancements
    function initSupplierForm() {
        if ($('.supplier-form').length === 0) return;

        // Auto-format phone number
        $('#phone').on('blur', function() {
            let phone = $(this).val().replace(/\D/g, '');
            if (phone.length === 10) {
                phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            } else if (phone.length === 11) {
                phone = phone.replace(/(\d{1})(\d{3})(\d{3})(\d{4})/, '+$1 ($2) $3-$4');
            }
            $(this).val(phone);
        });

        // Tax ID formatting
        $('#tax_id').on('blur', function() {
            let taxId = $(this).val().toUpperCase().replace(/\s/g, '');
            $(this).val(taxId);
        });

        // Email validation
        $('#email').on('blur', function() {
            const email = $(this).val();
            if (email && !isValidEmail(email)) {
                alert('Please enter a valid email address.');
                $(this).focus();
            }
        });

        // Quick stats refresh
        $('.refresh-stats').on('click', function(e) {
            e.preventDefault();
            refreshSupplierStats();
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function refreshSupplierStats() {
        const supplierId = $('input[name="supplier_id"]').val();
        if (!supplierId) return;

        $.ajax({
            url: inventoryManager.ajax_url,
            type: 'POST',
            data: {
                action: 'refresh_supplier_stats',
                supplier_id: supplierId,
                nonce: inventoryManager.nonce
            },
            beforeSend: function() {
                $('.stats-grid').addClass('loading');
            },
            success: function(response) {
                if (response.success) {
                    updateStatsDisplay(response.data);
                }
            },
            complete: function() {
                $('.stats-grid').removeClass('loading');
            }
        });
    }

    function updateStatsDisplay(stats) {
        $('.stat-item').each(function() {
            const statType = $(this).find('.stat-label').text().toLowerCase().replace(':', '').trim();
            if (stats[statType]) {
                $(this).find('.stat-value').text(
                    statType.includes('spent') ? '$' + stats[statType] : stats[statType]
                );
            }
        });
    }

    // Supplier grid enhancements
    function initSupplierGrid() {
        // Quick actions
        $('.supplier-actions .button').tooltip({
            placement: 'top',
            trigger: 'hover'
        });

        // Export functionality
        $('#export-suppliers').on('click', function() {
            exportSuppliers();
        });

        // Bulk actions
        $('#bulk-action-apply').on('click', function() {
            const selected = $('input[name="supplier_ids[]"]:checked');
            if (selected.length === 0) {
                alert('Please select at least one supplier.');
                return;
            }

            const action = $('#bulk-action-select').val();
            if (!action) {
                alert('Please select a bulk action.');
                return;
            }

            performBulkAction(selected, action);
        });
    }

    function exportSuppliers() {
        $.ajax({
            url: inventoryManager.ajax_url,
            type: 'POST',
            data: {
                action: 'export_suppliers',
                nonce: inventoryManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.download_url;
                }
            }
        });
    }

    function performBulkAction(selectedSuppliers, action) {
        const supplierIds = selectedSuppliers.map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: inventoryManager.ajax_url,
            type: 'POST',
            data: {
                action: 'bulk_action_suppliers',
                supplier_ids: supplierIds,
                bulk_action: action,
                nonce: inventoryManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }

    // Initialize
    initSupplierForm();
    initSupplierGrid();

    // Custom tooltip
    $.fn.tooltip = function(options) {
        const settings = $.extend({
            placement: 'top',
            trigger: 'hover'
        }, options);

        return this.each(function() {
            const $this = $(this);
            let tooltip = null;

            $this.on(settings.trigger, function() {
                if (!tooltip) {
                    const title = $this.attr('title');
                    if (!title) return;

                    tooltip = $('<div class="custom-tooltip">' + title + '</div>').appendTo('body');
                    $this.removeAttr('title');

                    const pos = $this.offset();
                    const width = $this.outerWidth();
                    const height = $this.outerHeight();
                    const tooltipWidth = tooltip.outerWidth();
                    const tooltipHeight = tooltip.outerHeight();

                    let top = 0;
                    let left = 0;

                    switch (settings.placement) {
                        case 'top':
                            top = pos.top - tooltipHeight - 10;
                            left = pos.left + (width - tooltipWidth) / 2;
                            break;
                        case 'bottom':
                            top = pos.top + height + 10;
                            left = pos.left + (width - tooltipWidth) / 2;
                            break;
                    }

                    tooltip.css({ top: top, left: left });
                }
            });

            $this.on('mouseleave', function() {
                if (tooltip) {
                    tooltip.remove();
                    tooltip = null;
                }
            });
        });
    };
});