jQuery(document).ready(function($) {
    'use strict';

    // Real-time calculations for purchase form
    function initPurchaseCalculations() {
        if ($('#purchaseForm').length === 0) return;

        $('.calculation-trigger').on('input', function() {
            calculatePurchaseTotals();
        });
    }

    function calculatePurchaseTotals() {
        const totalInvoice = parseFloat($('#total_invoice').val()) || 0;
        const totalWeight = parseFloat($('#total_weight').val()) || 0;
        const freightCost = parseFloat($('#freight_cost').val()) || 0;
        const customsCost = parseFloat($('#customs_cost').val()) || 0;
        const otherCosts = parseFloat($('#other_costs').val()) || 0;
        const profitMargin = parseFloat($('#profit_margin').val()) || 0;

        // You can add more complex calculations here as needed
        console.log('Purchase calculations updated:', {
            totalInvoice,
            totalWeight,
            freightCost,
            customsCost,
            otherCosts,
            profitMargin
        });
    }

    // Initialize all calculations
    initPurchaseCalculations();
});