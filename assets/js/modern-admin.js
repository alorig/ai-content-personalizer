/*==================================================
  ## IGNY8 MODERN ADMIN JS - PHASE 3
  Description: Modern SaaS-style admin interface JavaScript
==================================================*/

jQuery(document).ready(function($) {
    
    // Initialize modern admin interface
    initModernAdmin();
    
    function initModernAdmin() {
        initTabs();
        initFilters();
        initTables();
        initModals();
        initTooltips();
        initHelpSections();
        initCharts();
    }
    
    // Tab functionality
    function initTabs() {
        $('.igny8-tab-nav a').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this).attr('href');
            const tabContainer = $(this).closest('.igny8-modern-admin');
            
            // Remove active class from all tabs and contents within this container
            tabContainer.find('.igny8-tab-nav a').removeClass('active');
            tabContainer.find('.igny8-tab-content').removeClass('active');
            
            // Add active class to clicked tab
            $(this).addClass('active');
            
            // Show corresponding content
            tabContainer.find(target).addClass('active');
            
            // Trigger custom event for tab change
            $(document).trigger('igny8:tabChanged', [target]);
        });
        
        // Show first tab by default only if no tab is already active
        $('.igny8-modern-admin').each(function() {
            const container = $(this);
            if (container.find('.igny8-tab-nav a.active').length === 0 && container.find('.igny8-tab-nav a').length > 0) {
                container.find('.igny8-tab-nav a').first().click();
            }
        });
    }
    
    // Filter functionality
    function initFilters() {
        $('.igny8-apply-filters').on('click', function() {
            const filters = {};
            
            $('.igny8-filter-bar input, .igny8-filter-bar select').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                
                if (value && value !== '') {
                    filters[name] = value;
                }
            });
            
            // Apply filters (placeholder for now)
            console.log('Applying filters:', filters);
            applyFilters(filters);
        });
        
        $('.igny8-clear-filters').on('click', function() {
            $('.igny8-filter-bar input, .igny8-filter-bar select').val('');
            applyFilters({});
        });
    }
    
    // Table functionality
    function initTables() {
        // Sortable columns
        $('.igny8-data-table th.sortable').on('click', function() {
            const column = $(this).data('column');
            const currentSort = $(this).hasClass('active');
            const sortDirection = currentSort ? 'desc' : 'asc';
            
            // Remove active class from all sortable headers
            $('.igny8-data-table th.sortable').removeClass('active');
            
            // Add active class to clicked header
            $(this).addClass('active');
            
            // Update sort indicator
            $('.igny8-sort-indicator').text(sortDirection === 'asc' ? '↑' : '↓');
            
            // Sort table (placeholder for now)
            console.log('Sorting by:', column, sortDirection);
            sortTable(column, sortDirection);
        });
        
        // Select all checkbox functionality
        $('.igny8-data-table thead .igny8-table-checkbox').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.igny8-data-table tbody .igny8-table-checkbox').prop('checked', isChecked);
        });
        
        // Individual row checkboxes
        $('.igny8-data-table tbody .igny8-table-checkbox').on('change', function() {
            const totalRows = $('.igny8-data-table tbody .igny8-table-checkbox').length;
            const checkedRows = $('.igny8-data-table tbody .igny8-table-checkbox:checked').length;
            
            // Update the select all checkbox state
            $('.igny8-data-table thead .igny8-table-checkbox').prop('checked', totalRows === checkedRows);
        });
        
        // Bulk actions
        $('.igny8-apply-bulk').on('click', function() {
            const action = $('.igny8-bulk-actions').val();
            const selectedRows = $('.igny8-data-table tbody .igny8-table-checkbox:checked');
            
            if (!action) {
                alert('Please select a bulk action');
                return;
            }
            
            if (selectedRows.length === 0) {
                alert('Please select at least one item');
                return;
            }
            
            if (confirm(`Apply "${action}" to ${selectedRows.length} selected items?`)) {
                applyBulkAction(action, selectedRows);
            }
        });
        
        // Inline editing
        $('.igny8-data-table td[data-column]').on('dblclick', function() {
            const column = $(this).data('column');
            const currentValue = $(this).text().trim();
            
            if (column === 'actions') return;
            
            const input = $('<input type="text" value="' + currentValue + '">');
            $(this).html(input);
            input.focus().select();
            
            input.on('blur', function() {
                const newValue = $(this).val();
                $(this).parent().text(newValue);
                saveInlineEdit($(this).parent().closest('tr'), column, newValue);
            });
            
            input.on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    $(this).blur();
                }
            });
        });
    }
    
    // Modal functionality
    function initModals() {
        // Open modal
        $('.igny8-add-new').on('click', function() {
            const modalId = $(this).data('modal');
            $('#' + modalId).show();
        });
        
        // Close modal
        $('.igny8-modal-close, .igny8-modal-overlay, .igny8-modal-cancel').on('click', function() {
            $(this).closest('.igny8-modal').hide();
        });
        
        // Save modal
        $('.igny8-modal-save').on('click', function() {
            const modal = $(this).closest('.igny8-modal');
            const formData = getModalFormData(modal);
            
            // Save data (placeholder for now)
            console.log('Saving modal data:', formData);
            saveModalData(formData);
            
            modal.hide();
        });
        
        // Close modal on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                $('.igny8-modal:visible').hide();
            }
        });
    }
    
    // Tooltip functionality
    function initTooltips() {
        $('.igny8-tooltip').on('mouseenter', function() {
            const tooltip = $(this).attr('data-tooltip');
            if (tooltip) {
                $(this).attr('title', tooltip);
            }
        });
    }
    
    // Help sections functionality
    function initHelpSections() {
        $('.igny8-help-toggle').on('click', function() {
            const content = $(this).next('.igny8-help-content');
            const icon = $(this).find('.dashicons-arrow-down-alt2');
            
            content.slideToggle(300);
            icon.toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
        });
    }
    
    // Chart functionality
    function initCharts() {
        $('.igny8-chart-period').on('change', function() {
            const period = $(this).val();
            const chart = $(this).closest('.igny8-chart-container');
            
            // Update chart (placeholder for now)
            console.log('Updating chart for period:', period);
            updateChart(chart, period);
        });
        
        // Animate chart bars on load
        $('.igny8-chart-bar').each(function(index) {
            $(this).css('height', '0%');
            setTimeout(() => {
                $(this).css('height', $(this).attr('style').match(/height: (\d+)%/)[1] + '%');
            }, index * 100);
        });
    }
    
    // Utility functions
    function applyFilters(filters) {
        // Placeholder for filter application
        console.log('Applying filters:', filters);
        
        // Show loading state
        $('.igny8-data-table-container').addClass('igny8-loading');
        
        // Simulate API call
        setTimeout(() => {
            $('.igny8-data-table-container').removeClass('igny8-loading');
            // Update table with filtered data
        }, 1000);
    }
    
    function sortTable(column, direction) {
        // Placeholder for table sorting
        console.log('Sorting table by:', column, direction);
        
        // Show loading state
        $('.igny8-data-table-container').addClass('igny8-loading');
        
        // Simulate API call
        setTimeout(() => {
            $('.igny8-data-table-container').removeClass('igny8-loading');
            // Update table with sorted data
        }, 500);
    }
    
    function applyBulkAction(action, selectedRows) {
        // Placeholder for bulk actions
        console.log('Applying bulk action:', action, 'to', selectedRows.length, 'rows');
        
        // Show loading state
        $('.igny8-data-table-container').addClass('igny8-loading');
        
        // Simulate API call
        setTimeout(() => {
            $('.igny8-data-table-container').removeClass('igny8-loading');
            alert(`Bulk action "${action}" applied to ${selectedRows.length} items`);
        }, 1000);
    }
    
    function saveInlineEdit(row, column, value) {
        // Placeholder for inline editing
        console.log('Saving inline edit:', column, value);
        
        // Show loading state
        row.addClass('igny8-loading');
        
        // Simulate API call
        setTimeout(() => {
            row.removeClass('igny8-loading');
            // Update row with new value
        }, 500);
    }
    
    function getModalFormData(modal) {
        const formData = {};
        modal.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            const value = $(this).val();
            if (name) {
                formData[name] = value;
            }
        });
        return formData;
    }
    
    function saveModalData(formData) {
        // Placeholder for modal data saving
        console.log('Saving modal data:', formData);
        
        // Show loading state
        $('.igny8-modal').addClass('igny8-loading');
        
        // Simulate API call
        setTimeout(() => {
            $('.igny8-modal').removeClass('igny8-loading');
            alert('Data saved successfully!');
        }, 1000);
    }
    
    function updateChart(chart, period) {
        // Placeholder for chart updates
        console.log('Updating chart for period:', period);
        
        // Show loading state
        chart.addClass('igny8-loading');
        
        // Simulate API call
        setTimeout(() => {
            chart.removeClass('igny8-loading');
            // Update chart with new data
        }, 1000);
    }
    
    // Custom events
    $(document).on('igny8:tabChanged', function(e, target) {
        console.log('Tab changed to:', target);
        
        // Trigger tab-specific initialization
        if (target === '#keywords-clusters-keywords') {
            initKeywordsTab();
        } else if (target === '#keywords-clusters-clusters') {
            initClustersTab();
        } else if (target === '#keywords-clusters-insights') {
            initInsightsTab();
        } else if (target === '#content-engine-new-planner') {
            initPlannerTab();
        } else if (target === '#content-engine-new-context') {
            initContextBuilderTab();
        } else if (target === '#content-engine-new-linking') {
            initInternalLinkingTab();
        } else if (target === '#content-engine-new-performance') {
            initPerformanceTab();
        }
    });
    
    // Tab-specific initialization functions
    function initKeywordsTab() {
        console.log('Initializing Keywords tab');
        // Keywords-specific initialization
    }
    
    function initClustersTab() {
        console.log('Initializing Clusters tab');
        // Clusters-specific initialization
    }
    
    function initInsightsTab() {
        console.log('Initializing Insights tab');
        // Insights-specific initialization
    }
    
    function initPlannerTab() {
        console.log('Initializing Planner tab');
        // Planner-specific initialization
    }
    
    function initContextBuilderTab() {
        console.log('Initializing Context Builder tab');
        // Context Builder-specific initialization
    }
    
    function initInternalLinkingTab() {
        console.log('Initializing Internal Linking tab');
        // Internal Linking-specific initialization
    }
    
    function initPerformanceTab() {
        console.log('Initializing Performance tab');
        // Performance-specific initialization
    }
    
    // Initialize toggle switches
    initToggleSwitches();
    
});

// Toggle Switch Functionality
function initToggleSwitches() {
    // Handle global status toggle (both old and new field names)
    const globalToggle = document.querySelector('input[name="igny8_content_engine_global_status_toggle"], input[name="igny8_personalize_status"]');
    const globalHidden = document.querySelector('input[name="igny8_content_engine_global_status"]');
    
    if (globalToggle && globalHidden) {
        globalToggle.addEventListener('change', function() {
            globalHidden.value = this.checked ? 'enabled' : 'disabled';
            
            // Update card appearance
            const card = this.closest('.igny8-global-card');
            if (card) {
                card.className = card.className.replace(/igny8-card-(enabled|disabled)/, '');
                card.classList.add(this.checked ? 'igny8-card-enabled' : 'igny8-card-disabled');
                
                // Update icon and status
                const icon = card.querySelector('.igny8-card-icon');
                const status = card.querySelector('.igny8-card-status');
                if (icon) icon.textContent = this.checked ? '✓' : '○';
                if (status) status.textContent = this.checked ? 'Enabled' : 'Disabled';
            }
        });
    }
    
    // Handle post type toggles (both old and new field names)
    const postTypeToggles = document.querySelectorAll('input[name="igny8_content_engine_enabled_post_types[]"], input[name="igny8_personalize_enabled_post_types[]"]');
    postTypeToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const card = this.closest('.igny8-global-card, .igny8-post-type-card');
            if (card) {
                card.className = card.className.replace(/igny8-card-(enabled|disabled)/, '');
                card.classList.add(this.checked ? 'igny8-card-enabled' : 'igny8-card-disabled');
                
                // Update icon and status
                const icon = card.querySelector('.igny8-card-icon');
                const status = card.querySelector('.igny8-card-status');
                if (icon) icon.textContent = this.checked ? '✓' : '○';
                if (status) status.textContent = this.checked ? 'Enabled' : 'Disabled';
            }
        });
    });
    
    // Handle other toggle switches (both old and new field names)
    const otherToggles = document.querySelectorAll('input[name="igny8_content_engine_save_variations"], input[name="igny8_content_engine_include_page_context"], input[name="igny8_personalize_save_variations"], input[name="igny8_personalize_include_page_context"]');
    otherToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const card = this.closest('.igny8-global-card');
            if (card) {
                card.className = card.className.replace(/igny8-card-(enabled|disabled)/, '');
                card.classList.add(this.checked ? 'igny8-card-enabled' : 'igny8-card-disabled');
                
                // Update icon and status
                const icon = card.querySelector('.igny8-card-icon');
                const status = card.querySelector('.igny8-card-status');
                if (icon) icon.textContent = this.checked ? '✓' : '○';
                if (status) status.textContent = this.checked ? 'Enabled' : 'Disabled';
            }
        });
    });
    
    // Handle radio button interactions
    const radioGroups = document.querySelectorAll('.igny8-radio-group');
    radioGroups.forEach(group => {
        const radioInputs = group.querySelectorAll('input[type="radio"]');
        radioInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Remove selected class from all options in this group
                const allOptions = group.querySelectorAll('.igny8-radio-option');
                allOptions.forEach(option => option.classList.remove('selected'));
                
                // Add selected class to the chosen option
                const selectedOption = this.closest('.igny8-radio-option');
                if (selectedOption) {
                    selectedOption.classList.add('selected');
                }
            });
        });
    });
    
    // Handle Field Mode toggle (both old and new field names)
    const fieldModeToggle = document.querySelector('input[name="igny8_content_engine_field_mode"], select[name="igny8_personalize_field_mode"]');
    if (fieldModeToggle) {
        fieldModeToggle.addEventListener('change', function() {
            const label = document.querySelector('.igny8-toggle-label');
            if (label) {
                const isAuto = this.type === 'checkbox' ? this.checked : this.value === 'auto';
                label.textContent = isAuto ? 'Auto Detect (GPT)' : 'Fixed Fields';
            }
        });
    }
    
    // Handle Debug Console
    const showDebugBtn = document.getElementById('igny8-show-debug');
    const hideDebugBtn = document.getElementById('igny8-hide-debug');
    const debugData = document.getElementById('igny8-debug-data');
    
    if (showDebugBtn && hideDebugBtn && debugData) {
        showDebugBtn.addEventListener('click', function() {
            debugData.style.display = 'block';
            showDebugBtn.style.display = 'none';
            hideDebugBtn.style.display = 'inline-block';
        });
        
        hideDebugBtn.addEventListener('click', function() {
            debugData.style.display = 'none';
            hideDebugBtn.style.display = 'none';
            showDebugBtn.style.display = 'inline-block';
        });
    }
}