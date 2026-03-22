// Set up CSRF token for ALL AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    console.log('AJAX Navigation Loaded');
    
    // Handle all navigation links
    $(document).on('click', 'a:not([data-no-ajax])', function(e) {
        // Skip if it's an external link or has target="_blank"
        if (this.target === '_blank' || this.href.indexOf(window.location.origin) !== 0) {
            return true;
        }
        
        // Skip if it's a file download, anchor link, or has no-ajax class
        if (this.hasAttribute('download') || this.hash !== '' || $(this).hasClass('no-ajax')) {
            return true;
        }
        
        // Skip if it's a bootstrap dropdown toggle or collapse
        if ($(this).attr('data-toggle') === 'dropdown' || $(this).attr('data-toggle') === 'collapse') {
            return true;
        }
        
        // Skip if it's inside a form (like logout)
        if ($(this).closest('form').length > 0) {
            return true;
        }
        
        e.preventDefault();
        
        var url = this.href;
        var title = $(this).text() || document.title;
        
        console.log('Loading URL via AJAX:', url);
        
        // Update URL in browser without reload
        history.pushState({ url: url }, title, url);
        
        // Load content
        loadContent(url);
    });
    
    // Handle browser back/forward buttons
    $(window).on('popstate', function(e) {
        var url = e.originalEvent.state ? e.originalEvent.state.url : window.location.href;
        console.log('Popstate:', url);
        loadContent(url);
    });
    
    // Function to load content via AJAX
    function loadContent(url) {
        // Show loading indicator
        showLoading();
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            success: function(html) {
                console.log('Content loaded successfully');
                
                // Extract the content area from the response
                var $html = $(html);
                var newContent = $html.find('#page-content-wrapper').html();
                
                if (newContent) {
                    // Update content with fade effect
                    $('#page-content-wrapper').fadeOut(200, function() {
                        $(this).html(newContent).fadeIn(200);
                    });
                } else {
                    // Fallback: try to get the main content
                    var mainContent = $html.find('.container-fluid').html();
                    if (mainContent) {
                        $('#page-content-wrapper').fadeOut(200, function() {
                            $(this).html(mainContent).fadeIn(200);
                        });
                    } else {
                        $('#page-content-wrapper').fadeOut(200, function() {
                            $(this).html(html).fadeIn(200);
                        });
                    }
                }
                
                // Update page title
                var newTitle = $html.filter('title').text();
                if (newTitle) {
                    document.title = newTitle;
                }
                
                // Hide loading indicator
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.status, error);
                hideLoading();
                
                // If it's a 419 error or other error, reload the page
                window.location.reload();
            }
        });
    }
    
    // Handle form submissions - bypass AJAX for logout
    $(document).on('submit', 'form', function(e) {
        // If it's the logout form, let it submit normally
        if ($(this).attr('id') === 'logout-form') {
            return true;
        }
        
        // For other forms with data-ajax attribute, handle via AJAX
        if ($(this).attr('data-ajax') === 'true') {
            e.preventDefault();
            submitFormViaAjax($(this));
        }
    });
    
    // Function to submit form via AJAX
    function submitFormViaAjax(form) {
        var url = form.attr('action');
        var method = form.attr('method') || 'POST';
        var data = form.serialize();
        
        showLoading();
        
        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                hideLoading();
                
                if (typeof response === 'object') {
                    if (response.success) {
                        showAlert('success', response.message);
                        if (response.redirect) {
                            loadContent(response.redirect);
                        }
                    } else {
                        showAlert('danger', response.message);
                    }
                } else {
                    var $response = $(response);
                    var newContent = $response.find('#page-content-wrapper').html();
                    if (newContent) {
                        $('#page-content-wrapper').html(newContent);
                    }
                }
            },
            error: function(xhr) {
                hideLoading();
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        showAlert('danger', value[0]);
                    });
                } else {
                    showAlert('danger', 'An error occurred. Please try again.');
                }
            }
        });
    }
    
    // Show loading indicator
    function showLoading() {
        if ($('#loading-overlay').length === 0) {
            $('body').append(`
                <div id="loading-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);
        } else {
            $('#loading-overlay').show();
        }
    }
    
    function hideLoading() {
        $('#loading-overlay').hide();
    }
    
    // Show alert message
    function showAlert(type, message) {
        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 10000; min-width: 300px;">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
});