// kasir-init.js - Initialize the Kasir system

document.addEventListener('DOMContentLoaded', function() {
    console.log('Kasir system initializing...');
    
    // Ensure orderItems is initialized
    if (!window.orderItems) {
        window.orderItems = [];
        console.log('Initialized window.orderItems as empty array');
    }
    
    if (!window.orderCounter) {
        window.orderCounter = 1;
    }
    
    // Initialize order display
    setTimeout(function() {
        if (typeof updateOrderDisplay === 'function') {
            updateOrderDisplay();
            console.log('Initial order display called');
        }
        
        if (typeof updateTotals === 'function') {
            updateTotals();
            console.log('Initial totals called');
        }
        
        console.log('Available functions:');
        console.log('- addToOrder:', typeof addToOrder);
        console.log('- updateOrderDisplay:', typeof updateOrderDisplay);
        console.log('- updateTotals:', typeof updateTotals);
        
        console.log('Kasir system initialized. Current order items:', window.orderItems.length);
    }, 100);
});
