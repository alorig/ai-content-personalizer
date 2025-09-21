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
        initDrawers();
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
    
    // Drawer functionality
    function initDrawers() {
        // Open drawer
        $('.igny8-edit-card').on('click', function() {
            const drawerId = 'igny8-edit-drawer';
            $('#' + drawerId).show();
        });
        
        // Close drawer
        $('.igny8-drawer-close, .igny8-drawer-overlay, .igny8-drawer-cancel').on('click', function() {
            $(this).closest('.igny8-side-drawer').hide();
        });
        
        // Save drawer
        $('.igny8-drawer-save').on('click', function() {
            const drawer = $(this).closest('.igny8-side-drawer');
            const formData = getDrawerFormData(drawer);
            
            // Save data (placeholder for now)
            console.log('Saving drawer data:', formData);
            saveDrawerData(formData);
            
            drawer.hide();
        });
        
        // Close drawer on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                $('.igny8-side-drawer:visible').hide();
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
    
    function getDrawerFormData(drawer) {
        const formData = {};
        drawer.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            const value = $(this).val();
            if (name) {
                formData[name] = value;
            }
        });
        return formData;
    }
    
    function saveDrawerData(formData) {
        // Placeholder for drawer data saving
        console.log('Saving drawer data:', formData);
        
        // Show loading state
        $('.igny8-side-drawer').addClass('igny8-loading');
        
        // Simulate API call
        setTimeout(() => {
            $('.igny8-side-drawer').removeClass('igny8-loading');
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
    // Handle global status toggle
    const globalToggle = document.querySelector('input[name="igny8_content_engine_global_status_toggle"]');
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
    
    // Handle post type toggles
    const postTypeToggles = document.querySelectorAll('input[name="igny8_content_engine_enabled_post_types[]"]');
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
    
    // Handle other toggle switches (save variations, include page context, etc.)
    const otherToggles = document.querySelectorAll('input[name="igny8_content_engine_save_variations"], input[name="igny8_content_engine_include_page_context"]');
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
    
    // Handle Field Mode toggle
    const fieldModeToggle = document.querySelector('input[name="igny8_content_engine_field_mode"]');
    if (fieldModeToggle) {
        fieldModeToggle.addEventListener('change', function() {
            const label = document.querySelector('.igny8-toggle-label');
            if (label) {
                label.textContent = this.checked ? 'Auto Detect (GPT)' : 'Fixed Fields';
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
    
    // Drawer functionality
    initDrawers();
}

function initDrawers() {
    // Open drawer handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('igny8-add-new')) {
            e.preventDefault();
            const type = e.target.getAttribute('data-type');
            openAddDrawer(type);
        }
        
        if (e.target.classList.contains('igny8-edit-record')) {
            e.preventDefault();
            const type = e.target.getAttribute('data-type');
            const id = e.target.getAttribute('data-id');
            openEditDrawer(type, id);
        }
    });
    
    // Close drawer handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('igny8-drawer-close') || 
            e.target.classList.contains('igny8-drawer-overlay')) {
            closeDrawers();
        }
    });
    
    // Escape key to close drawer
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDrawers();
        }
    });
}

function openAddDrawer(type) {
    const drawer = document.getElementById('igny8-add-drawer');
    const title = document.getElementById('igny8-drawer-title');
    const content = document.getElementById('igny8-form-content');
    
    // Set title based on type
    const titles = {
        'keyword': 'Add New Keyword',
        'cluster': 'Add New Cluster',
        'task': 'Add New Task',
        'profile': 'Add New Profile',
        'link': 'Add New Link'
    };
    
    title.textContent = titles[type] || 'Add New Record';
    
    // Generate form content based on type
    content.innerHTML = generateFormFields(type);
    
    // Show drawer
    drawer.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openEditDrawer(type, id) {
    const drawer = document.getElementById('igny8-edit-drawer');
    const title = document.getElementById('igny8-edit-drawer-title');
    const content = document.getElementById('igny8-edit-form-content');
    
    // Set title based on type
    const titles = {
        'keyword': 'Edit Keyword',
        'cluster': 'Edit Cluster',
        'task': 'Edit Task',
        'profile': 'Edit Profile',
        'link': 'Edit Link'
    };
    
    title.textContent = titles[type] || 'Edit Record';
    
    // Generate form content based on type
    content.innerHTML = generateFormFields(type, id);
    
    // Show drawer
    drawer.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDrawers() {
    const drawers = document.querySelectorAll('.igny8-drawer');
    drawers.forEach(drawer => {
        drawer.classList.remove('active');
    });
    document.body.style.overflow = '';
}

function generateFormFields(type, id = null) {
    const fields = {
        'keyword': `
            <div class="igny8-input-group">
                <label>Keyword</label>
                <input type="text" name="keyword" placeholder="Enter keyword" required>
            </div>
            <div class="igny8-input-group">
                <label>Search Volume</label>
                <input type="number" name="search_volume" placeholder="1000" min="0">
            </div>
            <div class="igny8-input-group">
                <label>Difficulty Level</label>
                <input type="number" name="difficulty" placeholder="50" min="0" max="100">
            </div>
            <div class="igny8-input-group">
                <label>CPC</label>
                <input type="number" name="cpc" placeholder="2.50" step="0.01" min="0">
            </div>
            <div class="igny8-input-group">
                <label>Intent</label>
                <select name="intent">
                    <option value="">Select Intent</option>
                    <option value="informational">Informational</option>
                    <option value="transactional">Transactional</option>
                    <option value="commercial">Commercial</option>
                    <option value="navigational">Navigational</option>
                </select>
            </div>
            <div class="igny8-input-group">
                <label>Sector</label>
                <select name="sector">
                    <option value="">Select Sector</option>
                    <option value="technology">Technology</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="finance">Finance</option>
                    <option value="education">Education</option>
                </select>
            </div>
        `,
        'cluster': `
            <div class="igny8-input-group">
                <label>Cluster Name</label>
                <input type="text" name="cluster_name" placeholder="Enter cluster name" required>
            </div>
            <div class="igny8-input-group">
                <label>Cluster Page Title</label>
                <input type="text" name="page_title" placeholder="Enter page title">
            </div>
            <div class="igny8-input-group">
                <label>Target URL</label>
                <input type="url" name="target_url" placeholder="https://example.com">
            </div>
            <div class="igny8-input-group">
                <label>Priority</label>
                <select name="priority">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="igny8-input-group">
                <label>Sector</label>
                <select name="sector">
                    <option value="">Select Sector</option>
                    <option value="technology">Technology</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="finance">Finance</option>
                    <option value="education">Education</option>
                </select>
            </div>
        `,
        'task': `
            <div class="igny8-input-group">
                <label>Task Title</label>
                <input type="text" name="title" placeholder="Enter task title" required>
            </div>
            <div class="igny8-input-group">
                <label>Content Type</label>
                <select name="content_type">
                    <option value="">Select Content Type</option>
                    <option value="hub-page">Hub Page</option>
                    <option value="sub-page">Sub Page</option>
                    <option value="blog">Blog</option>
                    <option value="product-page">Product Page</option>
                    <option value="service-page">Service Page</option>
                </select>
            </div>
            <div class="igny8-input-group">
                <label>Status</label>
                <select name="status">
                    <option value="pending">Pending</option>
                    <option value="queued">Queued</option>
                    <option value="generated">Generated</option>
                    <option value="published">Published</option>
                    <option value="refresh-scheduled">Refresh Scheduled</option>
                </select>
            </div>
            <div class="igny8-input-group">
                <label>Schedule Date</label>
                <input type="datetime-local" name="schedule_date">
            </div>
            <div class="igny8-input-group">
                <label>Refresh After Days</label>
                <input type="number" name="refresh_days" placeholder="30" min="1">
            </div>
        `,
        'profile': `
            <div class="igny8-input-group">
                <label>Profile Name</label>
                <input type="text" name="profile_name" placeholder="Enter profile name" required>
            </div>
            <div class="igny8-input-group">
                <label>Prompt Template</label>
                <textarea name="prompt_template" placeholder="Enter prompt template"></textarea>
            </div>
            <div class="igny8-input-group">
                <label>Schema Hints</label>
                <textarea name="schema_hints" placeholder="Enter schema hints"></textarea>
            </div>
            <div class="igny8-input-group">
                <label>Voice Tone</label>
                <select name="voice_tone">
                    <option value="">Select Voice Tone</option>
                    <option value="professional">Professional</option>
                    <option value="casual">Casual</option>
                    <option value="friendly">Friendly</option>
                    <option value="authoritative">Authoritative</option>
                </select>
            </div>
        `,
        'link': `
            <div class="igny8-input-group">
                <label>Source Page</label>
                <select name="source_page">
                    <option value="">Select Source Page</option>
                    <!-- Pages will be loaded dynamically -->
                </select>
            </div>
            <div class="igny8-input-group">
                <label>Target Page</label>
                <select name="target_page">
                    <option value="">Select Target Page</option>
                    <!-- Pages will be loaded dynamically -->
                </select>
            </div>
            <div class="igny8-input-group">
                <label>Anchor Text</label>
                <input type="text" name="anchor_text" placeholder="Enter anchor text" required>
            </div>
            <div class="igny8-input-group">
                <label>Link Type</label>
                <select name="link_type">
                    <option value="upward">Upward</option>
                    <option value="downward">Downward</option>
                    <option value="horizontal">Horizontal</option>
                </select>
            </div>
            <div class="igny8-input-group">
                <label>Priority</label>
                <input type="number" name="priority" placeholder="1" min="1" max="10">
            </div>
            <div class="igny8-input-group">
                <label>Status</label>
                <select name="status">
                    <option value="suggested">Suggested</option>
                    <option value="approved">Approved</option>
                    <option value="inserted">Inserted</option>
                </select>
            </div>
        `
    };
    
    return fields[type] || '<p>Form fields not available for this type.</p>';
}
